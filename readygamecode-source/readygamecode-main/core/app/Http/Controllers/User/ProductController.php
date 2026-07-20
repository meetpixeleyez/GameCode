<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FileUploader;
use App\Lib\FormProcessor;
use App\Models\Activity;
use App\Models\Category;
use App\Models\Changelog;
use App\Models\Form;
use App\Models\Product;
use App\Models\SubCategory;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class ProductController extends Controller {

    public function selectCategory() {
        $pageTitle = 'Select Category';

        $categories = Category::active()->whereHas('subCategories', function ($subcategory) {
            $subcategory->active();
        })->with(['subCategories' => function ($subcategory) {
            $subcategory->active();
        }])->get();

        return view('Template::user.product.select_category', compact('pageTitle', 'categories'));
    }

    public function upload() {

        $categoryId    = request()->category;
        $subCategoryId = request()->sub_category;
        $isFree        = request()->is_free ?? 0;

        if (!$categoryId || !$subCategoryId) {
            return to_route('user.product.upload.category');
        }

        $categories = Category::active()->whereHas('subCategories', function ($subcategory) {
            $subcategory->active();
        })->with(['subCategories' => function ($subcategory) {
            $subcategory->active();
        }])->get();

        $pageTitle   = 'Upload Product';
        $category    = Category::active()->findOrFail($categoryId);
        $subCategory = SubCategory::active()->where('category_id', $categoryId)->findOrFail($subCategoryId);
        $form        = Form::where('id', $subCategory->form_id)->where('act', 'subcategory_attributes')->first();

        return view('Template::user.product.upload', compact('pageTitle', 'isFree', 'form', 'categories'));
    }

    public function edit($slug) {
        $product   = Product::where('slug', $slug)->firstOrFail();
        $pageTitle = 'Edit Product';
        $form      = Form::where('id', $product->subCategory->form_id)->where('act', 'subcategory_attributes')->first();

        return view('Template::user.product.edit', compact('pageTitle', 'form', 'product'));
    }

    public function saveProduct(Request $request, $id = null) {

        if (!$id) {
            $product          = new Product();
            $product->slug    = productSlug($request->title);
            $product->user_id = auth()->id();
            $isFree           = $request->has('is_free') && $request->is_free == 1;
        } else {
            $product = Product::findOrFail($id);
            $isFree  = $product->is_free;
        }

        $subcategory = SubCategory::active()->findOrFail($request->sub_category);
        $category    = Category::active()->findOrFail($request->category);

        $previewFileTypes   = $category->preview_file_types ?? [];
        $formattedFileTypes = array_map(fn($type) => $type, $previewFileTypes); // No need to quote the file types
        $maxSizeMB          = $category->file_size ?? 5;

        $optional        = $id ? 'nullable' : 'required';
        $priceValidation = $isFree ? 'nullable' : 'required|numeric|gt:0';

        $maxPreviewVideoSize = gs('preview_video_size') * 1024;

        $validationRule = [
            'title'         => 'required',
            'description'   => 'required',
            'category'      => 'required',
            'price'         => $priceValidation,
            'price_cl'      => $priceValidation,

            'preview_file'  => [
                ($category->file_type === 'audio' && !$id) ? 'required' : 'nullable',
                new FileTypeValidate($formattedFileTypes),
                "max:" . ($maxSizeMB * 1024),
            ],

            'main_file'     => [$optional, new FileTypeValidate(['zip'])],
            'demo_url'      => [
                $category->file_type === 'audio' ? 'nullable' : 'required',
                'url',
            ],
            // 'preview_video' => [
            //     ($category->file_type === 'video' && !$id) ? 'required' : 'nullable',
            //     new FileTypeValidate(['mp4', 'avi', 'mov']),
            //     'mimetypes:video/*',
            //     "max:$maxPreviewVideoSize",
            // ],
            'preview_video' => [
                'nullable',
                'url',   // url validation enough
                'max:255'
            ],
            'is_free'       => 'nullable|in:0,1',
            
            // Additional services validation
            'reskin_price'  => 'nullable|numeric|min:0',
            'publish_price' => 'nullable|numeric|min:0',
            'store_optimization_price' => 'nullable|numeric|min:0',
        ];

        if ($request->is_free == Status::YES && !gs('free_item')) {
            $notify[] = ['error', 'Free item upload is currently disabled'];
            return back()->withNotify($notify);
        }

        $form = Form::where('id', $subcategory->form_id)->where('act', 'subcategory_attributes')->first();

        $allValidationRule = $validationRule;
        $formProcessor     = null;
        if ($form) {
            $formProcessor      = new FormProcessor();
            $formValidationRule = $formProcessor->valueValidation(@$form->form_data);
            $allValidationRule  = array_merge($allValidationRule, $formValidationRule);
        }

        $request->validate($allValidationRule);

        if ($id && $request->hasFile('main_file') && $product->product_updated == Status::PRODUCT_UPDATE_PENDING) {
            $notify[] = ['error', 'You have a pending submission'];
            return back()->withNotify($notify);
        }

        if (gs('free_item')) {
            $product->is_free = $isFree ? Status::ENABLE : Status::DISABLE;
        }

        $product->title = $request->title;
        $purifier       = new \HTMLPurifier();

        if ($request->changelog) {
            if (!gs('changelog')) {
                $notify[] = ['error', 'Changelog option is currently disabled'];
                return back()->withNotify($notify);
            }

            if (isset($request->changelog)) {
                foreach ($request->changelog as $changelog) {
                    if (!empty($changelog['heading']) && !empty($changelog['description'])) {
                        Changelog::updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'heading'    => $changelog['heading'],
                            ],
                            [
                                'description' => htmlspecialchars_decode($purifier->purify($changelog['description'])),
                            ]
                        );
                    }
                }
            }
        }
        if ($request->hasFile('main_file')) {
            $this->uploadMainFile($request, $product, $id);
        }

        if ($request->hasFile('screenshots')) {
            $this->uploadScreenshot($request, $product, $id);
        }

        if ($request->hasFile('thumbnail')) {
            $this->uploadThumbnail($request, $product, $id);
        }

        if ($request->hasFile('preview_image')) {
            $this->uploadPreviewImage($request, $product, $id);
        }

        if (
            $request->hasFile('preview_file') &&
            ($extension = strtolower($request->file('preview_file')->getClientOriginalExtension())) &&
            in_array($extension, $category->preview_file_types ?? [])
        ) {
            $this->uploadPreviewFile($request, $product);
        }

        if (
            $request->hasFile('preview_file') &&
            in_array($request->file('preview_file')->getClientOriginalExtension(), ['jpg', 'png', 'jpeg']) &&
            in_array($request->file('preview_file')->getClientOriginalExtension(), $category->preview_file_types)
        ) {
            $this->uploadPreviewFile($request, $product, true);
        }

        // if ($request->hasFile('preview_video')) {
        //     $this->uploadPreviewVideo($request, $product, $id);
        // }
        $product->preview_video = $request->input('preview_video');

        $attributeInfo = [];
        if ($formProcessor) {
            $attributeInfo = $formProcessor->processFormData($request, $form->form_data);
        }

        $faqItems = [];
        $faqQuestions = $request->input('faq_question', []);
        $faqAnswers = $request->input('faq_answer', []);
        if (is_array($faqQuestions) && is_array($faqAnswers)) {
            foreach ($faqQuestions as $index => $question) {
                $questionText = trim($question);
                $answerText = trim($faqAnswers[$index] ?? '');

                if ($questionText && $answerText) {
                    $faqItems[] = [
                        'question' => strip_tags($questionText),
                        'answer' => strip_tags($answerText),
                    ];
                }
            }
        }

        if ($faqItems) {
            $attributeInfo['faq_items'] = $faqItems;
        } elseif (isset($attributeInfo['faq_items'])) {
            unset($attributeInfo['faq_items']);
        }

        $product->tags            = $request->tags;
        $product->description     = htmlspecialchars_decode($purifier->purify($request->description));
        // Optional per-product SEO fields
        $product->meta_title       = $request->input('meta_title') ? strip_tags($request->input('meta_title')) : null;
        $product->meta_description = $request->input('meta_description') ? strip_tags($request->input('meta_description')) : null;
        $product->category_id     = $category->id;
        $product->sub_category_id = $subcategory->id;
        $product->price           = $request->price ?? 0;
        $product->price_cl        = $request->price_cl ?? 0;
        $product->demo_url        = $request->demo_url;
        $product->attribute_info  = $attributeInfo;
        
        // Save additional services prices
        $product->reskin_price = $request->reskin_price ?? 0;
        $product->publish_price = $request->publish_price ?? 0;
        $product->store_optimization_price = $request->store_optimization_price ?? 0;
        
        $product->save();

        if ($request->message) {
            $activity             = new Activity();
            $activity->user_id    = auth()->id();
            $activity->message    = $request->message;
            $activity->product_id = $product->id;
            $activity->save();
        }

        if ($request->hasFile('main_file')) {
            $notify[] = ['info', 'Your submission is under review'];
        }

        $notify[] = ['success', 'Product information saved successfully'];

        return back()->withNotify($notify);
    }

    public function productActivities($slug) {
        $pageTitle = 'Activity Log';
        $product   = Product::where('status', '!=', Status::PRODUCT_HARD_REJECTED)->countComment()->where('slug', $slug)->firstOrFail();

        abort_if($product->user_id != auth()->id(), 404);
        $activities = $product->activities()->with(['user', 'reviewer'])->paginate(getPaginate());

        return view('Template::user.product.activities', compact('pageTitle', 'product', 'activities'));
    }

    public function replyActivity(Request $request, $productId) {
        $request->validate([
            'message' => 'required',
        ]);

        $activity             = new Activity();
        $activity->message    = $request->message;
        $activity->product_id = $productId;
        $activity->user_id    = auth()->id();
        $activity->save();

        $notify[] = ['success', 'Your message submitted successfully'];
        return back()->withNotify($notify);
    }

    private function uploadScreenshot($request, &$product, $id) {
        try {
            $slug          = $product->slug;
            $zipPath       = $request->file('screenshots')->path();
            $extractedPath = getFilePath('screenshots') . '/' . $slug . '/screenshots';

            $zip = new \ZipArchive;
            $zip->open($zipPath);
            $invalidFile = false;

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename      = $zip->getNameIndex($i);
                $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);

                if (!in_array($fileExtension, ['png', 'jpg', 'jpeg']) || strpos($filename, '/') != false) {
                    $invalidFile = true;
                    break;
                }
            }

            if ($invalidFile) {
                $notify[] = ['error', 'You have to upload images only'];
                return back()->withInput($request->all())->withNotify($notify);
            }

            fileManager()->makeDirectory($extractedPath);

            if ($id && is_dir($extractedPath)) {
                fileManager()->removeDirectory($extractedPath);
            }

            $zip->extractTo($extractedPath);
        } catch (\Exception $exp) {
            $notify[] = ['error', 'Couldn\'t extract and upload your screenshots'];
            return back()->withNotify($notify);
        }
    }

    private function uploadThumbnail($request, &$product, $id) {
        try {
            $slug               = $product->slug;
            $product->thumbnail = fileUploader(
                $request->thumbnail,
                getFilePath('productThumbnail') . '/' . $slug,
                getFileSize('productThumbnail'),
                $product->thumbnail ?? null
            );
        } catch (\Exception $exp) {
            $notify[] = ['error', 'Couldn\'t upload your preview image'];
            return back()->withInput($request->all())->withNotify($notify);
        }
    }

    private function uploadPreviewImage($request, &$product) {
        try {
            $slug                   = $product->slug;
            $product->preview_image = fileUploader(
                $request->preview_image,
                getFilePath('productPreview') . '/' . $slug,
                getFileSize('productPreview'),
                $product->preview_image ?? null
            );
            $product->inline_preview_image = fileUploader(
                $request->preview_image,
                getFilePath('productInlinePreview') . '/' . $slug,
                getFileSize('productInlinePreview'),
                $product->inline_preview_image ?? null
            );
        } catch (\Exception $exp) {
            $notify[] = ['error', 'Couldn\'t upload your preview image'];
            return back()->withInput($request->all())->withNotify($notify);
        }
    }

    private function uploadPreviewFile($request, &$product, $image = null) {
        try {
            if ($image) {
                $slug                   = $product->slug;
                $product->preview_image = fileUploader(
                    $request->preview_file,
                    getFilePath('productPreview') . '/' . $slug,
                    getFileSize('productPreview'),
                    $product->preview_image ?? null
                );
                $product->inline_preview_image = fileUploader(
                    $request->preview_file,
                    getFilePath('productInlinePreview') . '/' . $slug,
                    getFileSize('productInlinePreview'),
                    $product->inline_preview_image ?? null
                );
            } else {
                $slug               = $product->slug;
                $fileUploader       = new FileUploader();
                $fileUploader->path = getFilePath('previewFile') . '/' . $slug;
                $fileUploader->file = $request->preview_file;
                $fileUploader->upload();
                $product->audio_temp_file = $fileUploader->fileName;
            }
        } catch (\Exception $exp) {
            $notify[] = ['error', 'Couldn\'t upload your file'];
            return back()->withInput($request->all())->withNotify($notify);
        }
    }
    private function uploadMainFile($request, &$product, $id) {
        try {
            $slug = $product->slug;
            if ($id) {
                $product->product_updated = Status::PRODUCT_UPDATE_PENDING;
            }

            $fileUploader       = new FileUploader();
            $fileUploader->path = getFilePath('productFile') . '/' . $slug;
            $fileUploader->file = $request->main_file;
            $fileUploader->upload();

            $product->temp_file = $fileUploader->fileName;
        } catch (\Exception $exp) {
            $notify[] = ['error', 'Couldn\'t upload your file'];
            return back()->withInput($request->all())->withNotify($notify);
        }
    }

    private function uploadPreviewVideo($request, &$product, $id) {
        try {
            $slug = $product->slug;
            if ($id) {
                $product->product_updated = Status::PRODUCT_UPDATE_PENDING;
            }

            if ($product->preview_video) {
                $oldFilePath = getFilePath('previewVideo') . '/' . $slug . '/' . $product->preview_video;
                if (file_exists($oldFilePath)) {
                    @unlink($oldFilePath);
                }
            }

            $fileUploader       = new FileUploader();
            $fileUploader->path = getFilePath('previewVideo') . '/' . $slug;
            $fileUploader->file = $request->preview_video;
            $fileUploader->upload();

            $product->preview_video = $fileUploader->fileName;
        } catch (\Exception $exp) {
            $notify[] = ['error', 'Couldn\'t upload your preview video'];
            return back()->withInput($request->all())->withNotify($notify);
        }
    }

    public function commenting($slug) {
        $product = Product::where('slug', $slug)->where('user_id', auth()->id())->firstOrFail();

        $product->comment_disable = !$product->comment_disable;
        $product->save();
        $message  = $product->comment_disable ? 'Comments have been disabled' : 'Comments have been enabled';
        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }
}
