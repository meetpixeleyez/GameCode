<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Frontend;
use App\Models\Language;
use App\Models\BlogPost;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductView;
use App\Models\Rating;
use App\Models\Review;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class SiteController extends Controller
{
    public function index()
    {
        $reference = @$_GET['reference'];
        if ($reference) {
            session()->put('reference', $reference);
        }

        $pageTitle   = 'Home';
        $sections    = Page::where('tempname', activeTemplate())->where('slug', '/')->first();
        $seoContents = $sections->seo_content;
        $seoImage    = @$seoContents->image ? getImage(getFilePath('seo') . '/' . @$seoContents->image, getFileSize('seo')) : null;

        return view('Template::home', compact('pageTitle', 'sections', 'seoContents', 'seoImage'));
    }

    public function pages($slug)
    {
        $page        = Page::where('tempname', activeTemplate())->where('slug', $slug)->firstOrFail();
        $pageTitle   = $page->name;
        $sections    = $page->secs;
        $seoContents = $page->seo_content;
        $seoImage    = @$seoContents->image ? getImage(getFilePath('seo') . '/' . @$seoContents->image, getFileSize('seo')) : null;
        return view('Template::pages', compact('pageTitle', 'sections', 'seoContents', 'seoImage'));
    }

    public function products()
    {
        $pageTitle = "Products";
        $request   = request();
        $query     = Product::approved()->allActive()->where('is_free', Status::DISABLE);

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereJsonContains("tags", $search)
                    ->orWhere("title", "Like", "%" . $search . "%")
                    ->orWhereHas('author', function ($qq) use ($search) {
                        $qq->where("username", "Like", "%" . $search . "%");
                    });
            });
        }

        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->category && $request->category != 'all') {
            $query->where('category_id', $request->category);
        }

        if ($request->sub_category) {
            $query->where('sub_category_id', $request->sub_category);
        }

        if ($request->rating && $request->rating != 'all') {
            $query->where('avg_rating', $request->rating);
        }

        if ($request->date_range && $request->date_range != 'all') {
            $dateRange = $request->date_range;
            $query->where('created_at', '>=', now()->subDays($dateRange));
        }

        if ($request->sort_by) {
            $sortBy = $request->sort_by;
            $query->when($sortBy == 'best_selling', function ($query) {
                $query->orderByDesc('total_sold');
            })->when($sortBy == 'best_rated', function ($query) {
                $query->orderByDesc('avg_rating');
            })->when($sortBy == 'new_item', function ($query) {
                $query->orderByDesc('created_at');
            });
        }

        $products = $query->with(['reviews', 'users', 'author', 'campaignProduct', 'category'])->orderBy('id', 'desc')->paginate(getPaginate(12));

        $productsAnyDate   = Product::approved()->count();
        $productsLastYear  = Product::approved()->where('created_at', '>=', Carbon::now()->subYear())->count();
        $productsLastMonth = Product::approved()->where('created_at', '>=', Carbon::now()->subMonth())->count();
        $productsLastWeek  = Product::approved()->where('created_at', '>=', Carbon::now()->subWeek())->count();
        $productsLastDay   = Product::approved()->where('created_at', '>=', Carbon::now()->subDay())->count();

        $ratings    = Rating::get();
        $categories = Category::active()->get();

        if (request()->category) {
            $category    = Category::active()->where('id', request()->category)->first();
            $seoContents = @$category->seo_content;
            $seoImage    = @$seoContents->image ? getImage(getFilePath('category_seo') . '/' . @$seoContents->image, getFileSize('category_seo')) : null;
        } else {
            $seoContents = null;
            $seoImage    = null;
        }

        return view('Template::products', compact(
            'pageTitle',
            'categories',
            'products',
            'ratings',
            'productsAnyDate',
            'productsLastYear',
            'productsLastMonth',
            'productsLastWeek',
            'productsLastDay',
            'seoContents',
            'seoImage'
        ));
    }

    public function freeProducts()
    {
        $pageTitle  = "Products";
        $categories = Category::active()
            ->with([
                'products' => function ($query) {
                    $query->approved()->allActive()->where('is_free', Status::ENABLE)->orderBy('id', 'desc');
                },
                'products.author',
                'products.users',
            ])
            ->get();
        $products = Product::with('author')->searchable(['title'])->approved()->allActive()->where('is_free', Status::ENABLE)->latest()->paginate(getPaginate(16));
        return view('Template::free_products', compact('pageTitle', 'categories', 'products'));
    }

    public function productDetails($slug)
    {
        $product = Product::with('author')->countComment()->where(['slug' => $slug])->firstOrFail();

        if (in_array($product->status, [Status::PRODUCT_PERMANENT_DOWN, Status::PRODUCT_HARD_REJECTED])) {
            abort(404);
        }

        abort_if(!$product->my_product && $product->status != Status::PRODUCT_APPROVED, 404);

        $author                  = $product->author;
        $pageTitle               = $product->title;
        $seoContents['keywords'] = Frontend::where('data_keys', 'seo.data')->first('data_values')?->data_values->keywords;

        // $seoContents['social_title']       = $product->title;
        // $seoContents['description']        = strLimit(strip_tags($product->description), 150);
        // $seoContents['social_description'] = strLimit(strip_tags($product->description), 150);
        // Prefer per-product meta if provided, otherwise fallback to title/description
        $seoContents['social_title']       = $product->meta_title ?: $product->title;
        $seoContents['description']        = $product->meta_description ? strLimit(strip_tags($product->meta_description), 155) : strLimit(strip_tags($product->description), 155);
        $seoContents['social_description'] = $product->meta_description ? strLimit(strip_tags($product->meta_description), 155) : strLimit(strip_tags($product->description), 155);
        $seoContents['image']              = getImage(getFilePath('productPreview') . '/' . productFilePath($product, 'preview_image'), getFileSize('productPreview'));
        $seoContents['image_size']         = getFileSize('productPreview');

        if ($product->user_id != auth()->id()) {

            $view = ProductView::where('product_id', $product->id)
                ->whereDate('views_date', today())
                ->first();

            if ($view) {
                $view->increment('views');
            } else {
                $productView             = new ProductView();
                $productView->product_id = $product->id;
                $productView->views      = 1;
                $productView->views_date = today();
                $productView->save();
            }
        }
        
        // ensure $seoContents is an object for the shared partial which expects object access
        $seoContents = (object)$seoContents;

        return view('Template::product_details', compact('pageTitle', 'product', 'author', 'seoContents'));
    }

    public function sitemap()
    {
        $lastModified = now()->toDateString();

        $urls = collect([
            ['loc' => url('/'), 'lastmod' => $lastModified, 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => route('products'), 'lastmod' => $lastModified, 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => route('free.products'), 'lastmod' => $lastModified, 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => route('blog.index'), 'lastmod' => $lastModified, 'changefreq' => 'daily', 'priority' => '0.8'],
            ['loc' => route('contact'), 'lastmod' => $lastModified, 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('cookie.policy'), 'lastmod' => $lastModified, 'changefreq' => 'monthly', 'priority' => '0.5'],
        ]);

        Page::where('tempname', activeTemplate())
            ->where('slug', '<>', '/')
            ->get(['slug', 'updated_at'])
            ->each(function ($page) use ($urls) {
                $urls->push([
                    'loc' => url($page->slug),
                    'lastmod' => optional($page->updated_at)->toDateString() ?? now()->toDateString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.7',
                ]);
            });

        Frontend::where('data_keys', 'policy_pages.element')
            ->get(['slug', 'updated_at'])
            ->each(function ($policy) use ($urls) {
                $urls->push([
                    'loc' => route('policy.pages', $policy->slug),
                    'lastmod' => optional($policy->updated_at)->toDateString() ?? now()->toDateString(),
                    'changefreq' => 'yearly',
                    'priority' => '0.5',
                ]);
            });

        Product::approved()->allActive()->select('slug', 'updated_at')->orderByDesc('updated_at')->get()->each(function ($product) use ($urls) {
            $urls->push([
                'loc' => route('product.details', $product->slug),
                'lastmod' => optional($product->updated_at)->toDateString() ?? now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ]);
        });

        BlogPost::where('is_published', true)->select('slug', 'updated_at')->orderByDesc('updated_at')->get()->each(function ($post) use ($urls) {
            $urls->push([
                'loc' => route('blog.show', $post->slug),
                'lastmod' => optional($post->updated_at)->toDateString() ?? now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ]);
        });

        $urls = $urls->unique('loc')->values();

        return response()->view('sitemap.index', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }

    public function productReviews($slug)
    {
        $product = Product::with(['author'])->countComment()->where(['slug' => $slug])->firstOrFail();
        $reviews = Review::where(['product_id' => $product->id])->with(['user', 'replies', 'category']);
        abort_if(($product->status == !Status::PRODUCT_APPROVED && !$product->my_product) || $reviews->count() < gs('min_reviews'), 404);

        $reviewId = request()->review_id;
        if ($reviewId) {
            $reviews->where('id', $reviewId);
        }

        $reviews   = $reviews->paginate(getPaginate());
        $pageTitle = 'Reviews';
        return view('Template::product_reviews', compact('pageTitle', 'product', 'reviews'));
    }

    public function productComments($slug)
    {
        $product = Product::with(['author'])->countComment()->where(['slug' => $slug])->firstOrFail();

        abort_if($product->status == !Status::PRODUCT_APPROVED && !$product->my_product, 404);

        $commentId = request()->comment_id;
        $comments  = Comment::where(['product_id' => $product->id, 'parent_id' => 0, 'review_id' => 0])
            ->when($commentId, function ($query) use ($commentId) {
                $query->where('id', $commentId);
            })
            ->with(['user', 'user.orderItems', 'product', 'replies' => function ($replyQuery) {
                $replyQuery->with('user');
            }])->paginate(getPaginate());
        $pageTitle = 'Comments';

        return view('Template::product_comments', compact('pageTitle', 'product', 'comments'));
    }

    public function productChangelog($slug)
    {
        $product = Product::with(['author'])->countComment()->where(['slug' => $slug])->firstOrFail();

        // Check various conditions before proceeding
        abort_if(
            $product->status != Status::PRODUCT_APPROVED // Ensure the product is approved
                && !$product->my_product // Allow if it's the user's product
                || !gs('changelog') // Global setting for changelog must be enabled
                || $product->product_updated == Status::PRODUCT_UPDATE_PENDING // No pending updates
                || count($product->changelogs) == 0, // Product must have changelogs
            404
        );

        $pageTitle = 'Changelog';

        return view('Template::product_changelog', compact('pageTitle', 'product'));
    }

    public function contact()
    {
        $pageTitle   = "Contact Us";
        $user        = auth()->user();
        $sections    = Page::where('tempname', activeTemplate())->where('slug', 'contact')->first();
        $seoContents = $sections->seo_content;
        $seoImage    = @$seoContents->image ? getImage(getFilePath('seo') . '/' . @$seoContents->image, getFileSize('seo')) : null;
        return view('Template::contact', compact('pageTitle', 'user', 'sections', 'seoContents', 'seoImage'));
    }

    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name'    => 'required',
            'email'   => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required',
        ]);

        $request->session()->regenerateToken();

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $random = getNumber();

        $ticket           = new SupportTicket();
        $ticket->user_id  = auth()->id() ?? 0;
        $ticket->name     = $request->name;
        $ticket->email    = $request->email;
        $ticket->priority = Status::PRIORITY_MEDIUM;

        $ticket->ticket     = $random;
        $ticket->subject    = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status     = Status::TICKET_OPEN;
        $ticket->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->title     = 'A new contact message has been submitted';
        $adminNotification->click_url = urlPath('admin.ticket.view', $ticket->id);
        $adminNotification->save();

        $message                    = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message           = $request->message;
        $message->save();

        $notify[] = ['success', 'Ticket created successfully!'];

        return to_route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }

    public function policyPages($slug)
    {
        $policy      = Frontend::where('slug', $slug)->where('data_keys', 'policy_pages.element')->firstOrFail();
        $pageTitle   = $policy->data_values->title;
        $seoContents = $policy->seo_content;
        $seoImage    = @$seoContents->image ? frontendImage('policy_pages', $seoContents->image, getFileSize('seo'), true) : null;
        return view('Template::policy', compact('policy', 'pageTitle', 'seoContents', 'seoImage'));
    }

    public function changeLanguage($lang = null)
    {
        $language = Language::where('code', $lang)->first();
        if (!$language) {
            $lang = 'en';
        }

        session()->put('lang', $lang);
        return back();
    }

    public function cookieAccept()
    {
        Cookie::queue('gdpr_cookie', gs('site_name'), 43200);
    }

    public function cookiePolicy()
    {
        $cookieContent = Frontend::where('data_keys', 'cookie.data')->first();
        abort_if($cookieContent->data_values->status != Status::ENABLE, 404);
        $pageTitle = 'Cookie Policy';
        $cookie    = Frontend::where('data_keys', 'cookie.data')->first();
        return view('Template::cookie', compact('pageTitle', 'cookie'));
    }

    public function placeholderImage($size = null)
    {
        $imgWidth  = explode('x', $size)[0];
        $imgHeight = explode('x', $size)[1];
        $text      = $imgWidth . '×' . $imgHeight;
        $fontFile  = realpath('assets/font/solaimanLipi_bold.ttf');
        $fontSize  = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if ($imgHeight < 100 && $fontSize > 30) {
            $fontSize = 30;
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill    = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bgFill);
        $textBox    = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function maintenance()
    {
        $pageTitle = 'Maintenance Mode';
        if (gs('maintenance_mode') == Status::DISABLE) {
            return to_route('home');
        }
        $maintenance = Frontend::where('data_keys', 'maintenance.data')->first();
        return view('Template::maintenance', compact('pageTitle', 'maintenance'));
    }

    public function incrementClick()
    {
        $ad = Advertisement::find(request('id'));
        if ($ad) {
            $ad->click += 1;
            $ad->save();
        }
        return response()->json(['status' => 'Success', 'message' => 'Advertisement Clicked!']);
    }
}
