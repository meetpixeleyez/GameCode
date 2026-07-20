<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Category;
use App\Models\Form;
use App\Models\ReviewCategory;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class CategoryController extends Controller {
    public function index() {
        $pageTitle  = 'Categories';
        $categories = Category::searchable(['name'])->paginate(getPaginate());
        return view('admin.product.categories', compact('pageTitle', 'categories'));
    }

    public function store(Request $request) {
        $request->validate([
            'name'                      => 'required|unique:categories,name',
            'image'                     => ['required', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'personal_buyer_fee'        => 'required|numeric|gte:0',
            'commercial_buyer_fee'      => 'required|numeric|gte:0',
            'twelve_month_extended_fee' => 'required|numeric|min:0',
            'file_type'                 => 'required',
            'file_size'                 => 'nullable|numeric|gt:0|required_if:file_type,audio',
            'preview_file_types'        => 'nullable|array|required_if:file_type,audio',
        ]);

        $category                            = new Category();
        $category->name                      = $request->name;
        $category->file_type                 = $request->file_type;
        $category->file_size                 = $request->file_size;
        $category->preview_file_types        = $request->preview_file_types;
        $category->personal_buyer_fee        = $request->personal_buyer_fee;
        $category->commercial_buyer_fee      = $request->commercial_buyer_fee;
        $category->twelve_month_extended_fee = $request->twelve_month_extended_fee;
        $category->save();

        if ($request->hasFile('image')) {
            try {
                $category->image = fileUploader($request->image, getFilePath('category'), getFileSize('category'));
                $category->save();
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $notify[] = ['success', 'Category created successfully.'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id) {
        $request->validate([
            'name'                      => 'required|unique:categories,name,' . $id,
            'personal_buyer_fee'        => 'required|numeric|gte:0',
            'commercial_buyer_fee'      => 'required|numeric|gte:0',
            'twelve_month_extended_fee' => 'required|numeric|min:0',
            'file_type'                 => 'required',
            'file_size'                 => 'nullable|numeric|gt:0|required_if:file_type,audio',
            'preview_file_types'        => 'nullable|array|required_if:file_type,audio',
        ]);

        $category                            = Category::findOrFail($id);
        $category->name                      = $request->name;
        $category->file_type                 = $request->file_type;
        $category->file_size                 = $request->file_size;
        $category->preview_file_types        = $request->preview_file_types;
        $category->personal_buyer_fee        = $request->personal_buyer_fee;
        $category->commercial_buyer_fee      = $request->commercial_buyer_fee;
        $category->twelve_month_extended_fee = $request->twelve_month_extended_fee;

        if ($request->hasFile('image')) {
            try {
                $old             = $category->image;
                $category->image = fileUploader($request->image, getFilePath('category'), getFileSize('category'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $category->save();

        $notify[] = ['success', 'Category updated successfully'];
        return back()->withNotify($notify);
    }

    public function toggleFeature($id) {
        Category::changeStatus($id, 'featured');
        $notify[] = ['success', 'Featured changed successfully'];
        return back()->withNotify($notify);
    }

    public function activeFeature($id) {
        Category::changeStatus($id, 'status');
        $notify[] = ['success', 'Status changed successfully'];
        return back()->withNotify($notify);
    }

    public function reviewCategories() {
        $pageTitle        = 'Review Categories';
        $reviewCategories = ReviewCategory::active()->paginate(getPaginate());
        return view('admin.category.review_categories', compact('pageTitle', 'reviewCategories'));
    }

    public function saveReviewCategories(Request $request, $id) {
        $request->validate([
            'name' => 'required',
        ]);

        if ($id) {
            $reviewCategory = ReviewCategory::findOrFail($id);
            $notification   = 'Review Category updated successfully';
        } else {
            $reviewCategory = new ReviewCategory();
            $notification   = 'Review Category added successfully';
        }

        $reviewCategory->name = $request->name;
        $reviewCategory->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function reviewCategoryStatus($id) {
        return ReviewCategory::changeStatus($id);
    }

    public function attributes($categoryId) {
        $pageTitle = 'Category Attributes';
        $category  = Category::findOrFail($categoryId);
        $form      = Form::where('id', $category->form_id)->where('act', 'category_attributes')->first();
        return view('admin.product.attribute_info', compact('pageTitle', 'form'));
    }

    public function saveAttributes($categoryId) {
        $formProcessor = new FormProcessor();
        $category      = Category::findOrFail($categoryId);
        $formExists    = Form::where('id', $category->form_id)->where('act', 'category_attributes')->exists();
        $form          = $formProcessor->generate('category_attributes', $formExists);

        $category->form_id = $form->id;
        $category->save();

        $notify[] = ['success', 'Form updated successfully'];
        return back()->withNotify($notify);
    }

    public function categorySeo($id) {
        $data      = Category::findOrFail($id);
        $pageTitle = 'SEO Configuration';

        return view('admin.product.category_seo', compact('pageTitle', 'data'));
    }

    public function categorySeoUpdate(Request $request, $id) {

        $request->validate([
            'image' => ['nullable', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
        ]);

        $data  = Category::findOrFail($id);
        $image = @$data->seo_content->image;
        if ($request->hasFile('image')) {
            try {
                $image = fileUploader($request->image, getFilePath('category_seo'), getFileSize('category_seo'), @$data->seo_content->image);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the image'];
                return back()->withNotify($notify);
            }
        }
        $data->seo_content = [
            'image'              => $image,
            'description'        => $request->description,
            'social_title'       => $request->social_title,
            'social_description' => $request->social_description,
            'keywords'           => $request->keywords,
        ];
        $data->save();

        $notify[] = ['success', 'SEO content updated successfully'];
        return back()->withNotify($notify);
    }
}
