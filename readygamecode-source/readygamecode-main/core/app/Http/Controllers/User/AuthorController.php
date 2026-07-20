<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FileUploader;
use App\Lib\FormProcessor;
use App\Models\AdminNotification;
use App\Models\Campaign;
use App\Models\CampaignProduct;
use App\Models\Comment;
use App\Models\Download;
use App\Models\Form;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductCollection;
use App\Models\Rating;
use App\Models\RefundActivity;
use App\Models\RefundRequest;
use App\Models\ReportedReview;
use App\Models\ReportedReviewsAttachment;
use App\Models\Review;
use App\Models\ReviewCategory;
use App\Models\Transaction;
use App\Models\User;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthorController extends Controller {
    protected $files;
    protected $allowedExtension = ['jpg', 'png', 'jpeg', 'pdf', 'doc', 'docx'];

    public function showProfile($username = null) {
        $author      = User::active()->where("username", $username)->firstOrFail();
        $collections = ProductCollection::public()->where('user_id', $author->id)->paginate(getPaginate());
        $pageTitle   = 'Profile';
        return view('Template::user.profile', compact('author', 'pageTitle', 'collections'));
    }

    public function portfolio($username = null) {
        abort_if(!$username, 404);

        $sortBy    = request()->sort_by;
        $orderBy   = request()->order_by ?? 'title';
        $pageTitle = 'Portfolio';
        $author    = User::active()->author()->where("username", $username)->firstOrFail();
        $products  = $author->products()->searchable(['title'])->with('author', 'users');

        // if author is not the visitor
        if ($author->id != auth()->id()) {
            $products = $products->approved();
        }

        if ($author->id == auth()->id()) {
            $products->whereNotIn('status', [Status::PRODUCT_PERMANENT_DOWN, Status::PRODUCT_HARD_REJECTED]);
        }

        if ($orderBy) {
            $direction = 'desc';
            if ($orderBy == 'title') {
                $direction = 'asc';
            }

            $products->orderBy($orderBy, $direction);
        }

        $products = $products->paginate(getPaginate(18));

        return view('Template::user.portfolio.portfolio', compact('pageTitle', 'author', 'products'));
    }

    public function followers($author = null) {
        $pageTitle = 'Followers';
        $author    = $author ? User::where('username', $author)->firstOrFail() : auth()->user();
        $followers = $author->followers()->paginate(getPaginate());

        return view('Template::user.followers', compact('pageTitle', 'followers', 'author'));
    }

    public function following($author = null) {
        $pageTitle  = 'Following';
        $author     = $author ? User::where('username', $author)->firstOrFail() : auth()->user();
        $followings = $author->follows()->paginate(getPaginate(10));

        return view('Template::user.following', compact('pageTitle', 'author', 'followings'));
    }

    public function hiddenItems() {
        $pageTitle = 'Hidden Items';
        $status    = request()->status;
        $author    = auth()->user();
        $products  = Product::where('user_id', $author->id)->orderBy("id")->whereIn('status', [Status::PRODUCT_DOWN, Status::PRODUCT_SOFT_REJECTED, Status::PRODUCT_PENDING]);

        if (!is_null($status) && $status != 1) {
            $products->where('status', $status);
        }

        $products = $products->paginate(getPaginate());

        return view('Template::user.hidden_items', compact('pageTitle', 'author', 'products'));
    }

    public function uploadProduct() {
        $pageTitle = 'Upload Product';
        return view('Template::user.upload_product', compact('pageTitle'));
    }

    public function refunds() {
        $pageTitle               = 'Refunds';
        $author                  = auth()->user();
        $refundRequests          = $author->refundRequests()->with('orderItem.product')->get();
        $submittedRefundRequests = $author->submittedRefundRequests()->with('orderItem.product')->get();
        $refundRequests          = $refundRequests->merge($submittedRefundRequests)->sortBy('status');

        return view('Template::user.refunds', compact('pageTitle', 'author', 'refundRequests'));
    }

    public function refundActivity($id) {
        $pageTitle               = 'Refund Activity';
        $author                  = auth()->user();
        $refundRequests          = $author->refundRequests()->with('orderItem')->get();
        $submittedRefundRequests = $author->submittedRefundRequests()->with('orderItem')->get();
        $refundRequests          = $refundRequests->merge($submittedRefundRequests);
        $refundRequest           = $refundRequests->find($id);
        abort_if(!$refundRequest, 404);

        $refundActivites = $refundRequest->activites()->with(['seller', 'buyer'])->get();

        return view('Template::user.refund_activity', compact('pageTitle', 'author', 'refundRequest', 'refundActivites'));
    }

    public function refundActivityReply(Request $request, $id) {

        $request->validate([
            'message' => 'required',
        ]);

        $refundRequest               = RefundRequest::findOrFail($id);
        $activity                    = new RefundActivity();
        $activity->refund_request_id = $id;
        $activity->message           = $request->message;
        $activity->buyer_id          = $refundRequest->buyer_id == auth()->id() ? $refundRequest->buyer_id : null;
        $activity->seller_id         = $refundRequest->user_id == auth()->id() ? $refundRequest->user_id : null;
        $activity->save();

        $notify = ['success', 'Your message has been submitted'];
        return back()->withNotify($notify);
    }

    public function earning() {
        $pageTitle = 'Earning';
        $author    = auth()->user();

        $recentSales = $author->soldItems()->with(['order' => function ($query) {
            $query->paid();
        }])->latest()->with('product')->limit(10)->get();

        $soldItems = $author->soldItems()->where("is_refunded", Status::NO)->with(['order' => function ($query) {
            $query->paid();
        }]);

        $todayEarningQuery = (clone $soldItems)->whereDate('order_items.created_at', now());
        $todayEarning      = (clone $todayEarningQuery)->sum('seller_earning');
        $thisWeekEarning   = (clone $soldItems)->whereBetween('order_items.created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('seller_earning');
        $thisMonthEarning  = (clone $soldItems)->whereMonth('order_items.created_at', now()->month)->sum('seller_earning');
        $thisYearEarning   = (clone $soldItems)->whereYear('order_items.created_at', now()->year)->sum('seller_earning');
        $totalEarning      = (clone $soldItems)->sum('seller_earning');

        return view('Template::user.earning', compact('pageTitle', 'author', 'recentSales', 'todayEarning', 'thisWeekEarning', 'thisMonthEarning', 'thisYearEarning', 'totalEarning'));
    }

    public function download() {
        $pageTitle        = 'Purchased Item';
        $orderBy          = request()->order_by;
        $reviewCategories = ReviewCategory::active()->get();
        $purchasedItems   = auth()->user()->purchasedItems()->whereHas('order', function ($query) {
            $query->paid();
        })
            ->with(['product', 'product.reviews' => function ($query) {
                $query->where('user_id', auth()->id());
            }])
            ->latest()->searchable(['product:title', 'purchase_code'])->where('is_refunded', Status::NO);

        $purchasedItems = $purchasedItems->paginate(getPaginate());

        return view('Template::user.download', compact('pageTitle', 'purchasedItems', 'reviewCategories'));
    }

    public function purchaseDetails($username, $productId, $orderItemId) {
        if (!$productId) {
            $notify[] = ['error', 'Product not found'];
            return back()->withNotify($notify);
        }

        if (auth()->user()->username !== $username) {
            $notify[] = ['error', 'Unauthorized access'];
            return back()->withNotify($notify);
        }

        $purchasedDetails = auth()->user()->purchasedItems()
            ->whereHas('order', function ($query) {
                $query->paid();
            })
            ->where('product_id', $productId)
            ->where('id', $orderItemId)
            ->first();

        if (!$purchasedDetails) {
            $notify[] = ['error', 'Purchase details not found'];
            return back()->withNotify($notify);
        }

        $pageTitle = 'Purchase Details - ' . ($purchasedDetails->product->title ?? '');

        return view('Template::user.product.purchase_details', compact('pageTitle', 'purchasedDetails'));
    }

    public function freeDownload() {
        $pageTitle        = 'Free Items';
        $orderBy          = request()->order_by;
        $reviewCategories = ReviewCategory::active()->get();

        $download = Download::where('user_id', auth()->id())->pluck('product_id');

        $downloadedItems = Product::where('is_free', Status::ENABLE)->whereIn('id', $download)
            ->with(['reviews' => function ($query) {
                $query->where('user_id', auth()->id());
            }])
            ->latest()->paginate(getPaginate());

        return view('Template::user.free_download', compact('pageTitle', 'downloadedItems', 'reviewCategories'));
    }

    public function saveReview(Request $request, $productId) {
        $request->validate([
            'review'          => 'required',
            'rating'          => 'required|min:1|max:5|integer',
            'purchase_code'   => 'required',
            'review_category' => 'required',
        ]);
        try {
            $purchasedProducts = auth()->user()->purchasedItems()->pluck('product_id')->toArray();
            $product           = Product::approved()->whereIn('id', $purchasedProducts)->findOrFail($productId);
            $user              = User::findOrFail($product->user_id);
            $review            = Review::where(['product_id' => $productId, 'user_id' => auth()->id()])->first();
            $isNewReview       = false;
            if ($review) {
                $comments = Comment::where('product_id', $productId)
                    ->where('review_id', $review->id)
                    ->get();
                foreach ($comments as $comment) {
                    $this->deleteCommentAndReplies($comment);
                }
            } else {
                $review      = new Review();
                $isNewReview = true;
                $rating      = Rating::where('value', '>=', $product->avg_rating)->first();
                if (!$rating) {
                    $rating = Rating::orderBy('value', 'desc')->first();
                }
                if (!$rating) {
                    $rating = new Rating();
                }
                $rating->product_count++;
                $rating->save();
            }
            $review->user_id            = auth()->id();
            $review->product_id         = $productId;
            $review->author_id          = $product->user_id;
            $review->review_category_id = $request->review_category;
            $review->review             = $request->review;
            $review->rating             = $request->rating;
            $review->save();
            $user->total_review = $user->reviews()->count();
            $user->avg_rating   = $user->reviews()->avg('rating');
            $user->save();
            $product->total_review = $product->reviews()->count();
            $product->avg_rating   = $product->reviews()->avg('rating');
            $product->save();
            $author = $product->author;
            if ($isNewReview && @$author->email_settings->buyer_review_notification) {
                $data = [
                    'purchase_code'   => $request->purchase_code,
                    'review'          => $request->review,
                    'rating'          => displayRating($request->rating),
                    'review_category' => ReviewCategory::find($request->review_category)->name,
                    'link'            => route('product.reviews', ['slug' => $product->slug, 'review_id' => $review->id]),
                ];
                notify($author, 'BUYER_REVIEW', $data);
            }
            $notify[] = ['success', 'Your review submitted successfully'];
            return back()->withNotify($notify);
        } catch (\Exception $e) {
            $notify[] = ['error', $e->getMessage()];
            return back()->withNotify($notify);
        }
    }

    public function freeSaveReview(Request $request, $productId) {
        $request->validate([
            'review'          => 'required',
            'rating'          => 'required|min:1|max:5|integer',
            'review_category' => 'required',
        ]);

        try {
            $purchasedProducts = auth()->user()->downloadItems()->pluck('product_id')->toArray();

            $product     = Product::approved()->whereIn('id', $purchasedProducts)->findOrFail($productId);
            $user        = User::findOrFail($product->user_id);
            $review      = Review::where(['product_id' => $productId, 'user_id' => auth()->id()])->first();
            $isNewReview = false;
            if ($review) {
                $comments = Comment::where('product_id', $productId)
                    ->where('review_id', $review->id)
                    ->get();
                foreach ($comments as $comment) {
                    $this->deleteCommentAndReplies($comment);
                }
            } else {
                $review      = new Review();
                $isNewReview = true;
                $rating      = Rating::where('value', '>=', $product->avg_rating)->first();
                if (!$rating) {
                    $rating = Rating::orderBy('value', 'desc')->first();
                }
                if (!$rating) {
                    $rating = new Rating();
                }
                $rating->product_count++;
                $rating->save();
            }
            $review->user_id            = auth()->id();
            $review->product_id         = $productId;
            $review->author_id          = $product->user_id;
            $review->review_category_id = $request->review_category;
            $review->review             = $request->review;
            $review->rating             = $request->rating;
            $review->save();
            $user->total_review = $user->reviews()->count();
            $user->avg_rating   = $user->reviews()->avg('rating');
            $user->save();
            $product->total_review = $product->reviews()->count();
            $product->avg_rating   = $product->reviews()->avg('rating');
            $product->save();
            $author = $product->author;
            if ($isNewReview && @$author->email_settings->buyer_review_notification) {
                $data = [
                    'purchase_code'   => $request->purchase_code,
                    'review'          => $request->review,
                    'rating'          => displayRating($request->rating),
                    'review_category' => ReviewCategory::find($request->review_category)->name,
                    'link'            => route('product.reviews', ['slug' => $product->slug, 'review_id' => $review->id]),
                ];
                notify($author, 'BUYER_REVIEW', $data);
            }
            $notify[] = ['success', 'Your review submitted successfully'];
            return back()->withNotify($notify);
        } catch (\Exception $e) {
            $notify[] = ['error', $e->getMessage()];
            return back()->withNotify($notify);
        }
    }

    private function deleteCommentAndReplies(Comment $comment) {
        foreach ($comment->replies as $reply) {
            $this->deleteCommentAndReplies($reply);
        }
        $comment->delete();
    }

    public function saveComment(Request $request, $productId) {
        $request->validate([
            'text' => 'required',
        ]);

        $product = Product::approved()->find($productId);

        if (gs('comment_disable') && @$product->comment_disable == Status::ENABLE) {
            $notify[] = ['error', 'Comments are currently disabled for this product'];
            return back()->withNotify($notify);
        }

        if ($request->parent_id) {
            $parentComment = Comment::find($request->parent_id);
            if (!$parentComment || $parentComment->is_reported) {
                $notify[] = ['error', 'You cannot reply to this comment'];
                return back()->withNotify($notify);
            }
        }

        $comment               = new Comment();
        $comment->user_id      = auth()->id();
        $comment->product_id   = $productId;
        $comment->text         = $request->text;
        $comment->author_reply = $product->author->id === auth()->id();
        $comment->parent_id    = $request->parent_id ?? 0;
        $comment->save();

        $author = $product->author;

        if (@$author->email_settings->comment_notification) {
            notify($author, 'COMMENTED', [
                'author'       => $author->username,
                'product_name' => $product->title,
                'comment'      => $comment->text,
                'username'     => auth()->user()->username,
                'url'          => route('product.comments', ['slug' => $product->slug, 'comment_id' => $comment->id]),
            ]);
        }

        $notify[] = ['success', 'Your comment submitted successfully'];
        return back()->withNotify($notify);
    }

    public function commentList() {
        $pageTitle  = 'Comment List';
        $author     = auth()->user();
        $notReplied = request()->not_replied;
        $comments   = $author->comments()->searchable(['text', 'product:title'])->where('parent_id', 0)->where('review_id', 0)->with('product', 'user');

        if ($notReplied) {
            $comments->withCount('replies')->having('replies_count', 0);
        }
        $comments = $comments->latest()->paginate(getPaginate());
        return view('Template::user.comments.index', compact('pageTitle', 'comments', 'author'));
    }

    public function repliesList($commentId) {
        $pageTitle = 'Reply List';
        $author    = auth()->user();
        $comment   = Comment::with('replies')->findOrFail($commentId);
        $replies   = $comment->replies()->paginate(getPaginate());
        return view('Template::user.comments.replies.index', compact('pageTitle', 'comment', 'replies', 'author'));
    }

    public function deleteReply($id) {
        $comment = Comment::findOrFail($id);
        $comment->delete();
        $notify[] = ['success', 'Reply deleted successfully'];
        return back()->withNotify($notify);
    }

    public function reportComment(Request $request, $id) {
        $request->validate([
            'report_reason' => 'required|max:255',
        ]);

        $comment = Comment::whereHas('product', function ($query) {
            $query->where('user_id', auth()->id());
        })->findOrFail($id);

        $comment->is_reported   = Status::YES;
        $comment->report_reason = $request->report_reason;
        $comment->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = auth()->id();
        $adminNotification->title     = 'Comment reported';
        $adminNotification->click_url = urlPath('admin.comment.index', [$comment->user_id, $comment->id]);
        $adminNotification->save();

        $notify[] = ['success', 'Comment reported successfully'];
        return back()->withNotify($notify);
    }

    public function reportReview(Request $request) {
        $request->validate($this->validation());

        $review = Review::whereHas('product', function ($query) {
            $query->where('user_id', auth()->id());
        })->findOrFail($request->review_id);
        $review->is_reported = 1;
        $review->save();

        $details              = new ReportedReview();
        $details->review_id   = $request->review_id;
        $details->description = $request->description;
        $details->save();

        if ($request->hasFile('attachments')) {
            $this->storeReportAttachments($details->id, $request->file('attachments'));
        }

        $notify[] = ['success', 'Review reported successfully'];
        return back()->withNotify($notify);
    }

    public function reviewList() {
        $pageTitle  = 'Reviews';
        $author     = auth()->user();
        $reviews    = $author->reviews()->searchable(['review', 'product:title'])->with('product', 'user');
        $notReplied = request()->not_replied;
        $reviews    = $reviews->latest()->paginate(getPaginate());
        return view('Template::user.reviews.index', compact('pageTitle', 'reviews', 'author'));
    }

    public function referralList() {
        abort_if((!gs('referral')), 404);

        $pageTitle = 'Referral';
        $author    = auth()->user();
        $referrals = $author->referrals()->searchable(['username'])->latest()->paginate(getPaginate());

        $referral['total'] = Transaction::where([['remark', 'referral_commission'], ['user_id', $author->id]])->sum('amount');

        $referral['today'] = Transaction::where([['remark', 'referral_commission'], ['user_id', $author->id]])->whereDate('created_at', today())->sum('amount');

        $referral['week'] = Transaction::where([['remark', 'referral_commission'], ['user_id', $author->id]])->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount');

        $referral['month'] = Transaction::where([
            ['remark', 'referral_commission'],
            ['user_id', $author->id],
        ])->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount');

        $referralEarnings = Transaction::where([['remark', 'referral_commission'], ['user_id', $author->id]])->searchable(['trx'])->latest()->paginate(getPaginate());

        return view('Template::user.referrals.index', compact('pageTitle', 'referrals', 'referral', 'referralEarnings', 'author'));
    }

    public function favorites() {
        $pageTitle        = 'Favorites';
        $orderBy          = request()->order_by;
        $favoriteProducts = auth()->user()->favoriteProducts();

        if ($orderBy) {
            $favoriteProducts->orderBy($orderBy);
        }

        $favoriteProducts = $favoriteProducts->get();
        return view('Template::user.favorites', compact('pageTitle', 'favoriteProducts'));
    }

    public function collections() {
        $pageTitle   = 'Collections';
        $sortBy      = request()->sort_by;
        $orderColumn = $sortBy == 'date' ? 'created_at' : 'name';
        $collections = auth()->user()->collections()->orderBy($orderColumn)->with('products', 'user')->paginate(getPaginate());
        return view('Template::user.collection.list', compact('pageTitle', 'collections'));
    }

    public function collectionDetails($username, $id) {
        $pageTitle  = 'Collection Details';
        $collection = ProductCollection::findOrFail($id);
        return view('Template::user.collection.details', compact('pageTitle', 'collection'));
    }

    public function storeCollection(Request $request) {
        $collectionImageSize = explode('x', getFileSize('productCollection'));
        $collectionImageW    = $collectionImageSize[0];
        $collectionImageH    = $collectionImageSize[1];

        $request->validate([
            'name'  => [
                'required',
                Rule::unique('product_collections')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                }),
            ],
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png']), "dimensions:width=$collectionImageW,height=$collectionImageH"],
        ]);

        $collection            = new ProductCollection();
        $collection->user_id   = auth()->id();
        $collection->is_public = $request->is_public ?? 0;
        $collection->name      = $request->name;

        if ($request->hasFile('image')) {
            try {
                $collection->image = fileUploader($request->image, getFilePath('productCollection'), getFileSize('productCollection'));
            } catch (\Exception $e) {
                $notify[] = ['success', 'Could not upload image'];
                return back()->withNotify($notify);
            }
        }

        $collection->save();

        if (request()->ajax()) {
            return response()->json(['status' => 'success', 'collection' => $collection]);
        } else {
            $notify[] = ['success', "Collection added successfully"];
            return back()->withNotify($notify);
        }
    }

    public function updateCollection(Request $request, $id) {
        $collectionImageSize = explode('x', getFileSize('productCollection'));
        $collectionImageW    = $collectionImageSize[0];
        $collectionImageH    = $collectionImageSize[1];

        $request->validate([
            'name'  => 'required',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png']), "dimensions:width=$collectionImageW,height=$collectionImageH"],
        ]);

        $collection            = ProductCollection::where('user_id', auth()->id())->findOrFail($id);
        $collection->user_id   = auth()->id();
        $collection->is_public = $request->is_public ?? 0;
        $collection->name      = $request->name;
        if ($request->hasFile('image')) {
            try {
                $collection->image = fileUploader($request->image, getFilePath('productCollection'), getFileSize('productCollection'), $collection->image);
            } catch (\Exception $e) {
                $notify[] = ['error', 'Could not upload image'];
                return back()->withNotify($notify);
            }
        }
        $collection->save();

        $notify[] = ['success', 'Collection updated successfully'];
        return back()->withNotify($notify);
    }

    public function deleteCollection($id) {
        $collection = ProductCollection::where('user_id', auth()->id())->find($id);

        if (!$collection) {
            $notify[] = ['error', 'Collection not found'];
            return back()->withNotify($notify);
        }

        try {
            fileManager()->removeFile($collection->image);
        } catch (\Exception $e) {
            $notify[] = ['error', 'Could not delete image'];
            return back()->withNotify($notify);
        }

        $collection->delete();

        $notify[] = ['success', 'Collection deleted successfully'];
        return back()->withNotify($notify);
    }

    public function storeProductsToCollection(Request $request, $productId) {

        $product = Product::allActive()->find($productId);
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'No product found', 'data' => []]);
        }

        $product->collections()->sync($request->collection_id);
        return response()->json(['status' => 'success', 'data' => $product->collections]);
    }

    public function getProductsCollections($id) {
        $product = Product::allActive()->find($id);
        $data    = $product->collections->pluck('id')->toArray();
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function toggleFavorite(Request $request) {
        $request->validate([
            'product_id' => 'required',
        ]);

        $product = Product::approved()->find($request->product_id);

        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found']);
        }

        $message = $product->users->contains(auth()->id()) ? 'Product removed from favorite' : 'Product added to favorite';

        $product->users()->toggle(auth()->id());

        return response()->json(['status' => 'success', 'message' => $message]);
    }

    public function removeFavorite(Request $request) {
        $request->validate([
            'product_id' => 'required',
        ]);

        $product = Product::allActive()->approved()->find($request->product_id);
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found']);
        }

        $product->users()->detach(auth()->id());
        return response()->json(['status' => 'success', 'message' => 'Product removed from favorites']);
    }

    public function sells() {
        $author    = auth()->user();
        $pageTitle = 'All Sale';
        $soldItems = $author->soldItems()->where('is_refunded', Status::NO);
        $orders    = $author->orders();

        $search   = request()->search;
        $allSales = $soldItems->searchable(['purchase_code', 'product:title'])->latest()->with('product')->paginate(getPaginate(10));

        return view('Template::user.statement', compact('pageTitle', 'allSales', 'author'));
    }

    public function follow(User $user) {
        $isFollowing = auth()->user()->follows->contains($user->id);
        $message     = $isFollowing ? 'You have unfollowed ' . $user->fullname : $message     = 'You are now following ' . $user->fullname;
        $notify[]    = ['success', $message];

        auth()->user()->follows()->toggle($user->id);

        return back()->withNotify($notify);
    }

    public function saveSettings(Request $request) {
        $user          = auth()->user();
        $thumbnailSize = explode('x', getFileSize('authorThumbnail'));
        $coverImgSize  = explode('x', getFileSize('authorCoverImg'));

        $request->validate([
            'username'  => 'required|unique:users,username,' . $user->id,
            'firstname' => 'required',
            'lastname'  => 'required',
            'avatar'    => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png']), "dimensions:width=$thumbnailSize[0],height=$thumbnailSize[1]"],
            'cover_img' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png']), "dimensions:width=$coverImgSize[0],height=$coverImgSize[1]"],
        ]);

        $user->username              = $request->username;
        $user->firstname             = $request->firstname;
        $user->lastname              = $request->lastname;
        $purifier                    = new \HTMLPurifier();
        $user->bio                   = htmlspecialchars_decode($purifier->purify($request->bio));
        $user->social_media_settings = $request->social_media_settings;
        $emailSettings               = $request->email_settings ?? [];

        foreach ($emailSettings as $key => $value) {
            $emailSettings[$key] = (bool) $value;
        }

        $user->email_settings = $emailSettings;

        $user->address = $request->address;
        $user->city    = $request->city;
        $user->zip     = $request->zip;

        if ($request->hasFile('avatar')) {
            try {
                $user->avatar = fileUploader(
                    $request->file('avatar'),
                    getFilePath('authorThumbnail'),
                    getFileSize('authorThumbnail'),
                    $user->avatar
                );
            } catch (\Exception $e) {
                $notify[] = ['error', 'Could not upload your avatar'];
                return back()->withNotify($notify);
            }
        }

        if ($request->hasFile('cover_img')) {
            try {
                $user->cover_img = fileUploader(
                    $request->file('cover_img'),
                    getFilePath('authorCoverImg'),
                    getFileSize('authorCoverImg'),
                    $user->cover_img
                );
            } catch (\Exception $e) {
                $notify[] = ['error', 'Could not upload your cover image'];
                return back()->withNotify($notify);
            }
        }

        $user->save();
        $notify[] = ['success', 'Settings saved successfully'];
        return back()->withNotify($notify);
    }

    public function checkout() {
        $pageTitle = 'Checkout';
        return view('Template::user.checkout', compact('pageTitle'));
    }

    public function sendMailToAuthor(Request $request, $authorId) {
        $request->validate([
            'email'   => 'required|email',
            'message' => 'required',
        ]);

        $author = User::active()->where('id', '!=', auth()->id())->findOrFail($authorId);
        $email  = $request->email;

        notify($author, 'MAIL_TO_AUTHOR', [
            'email'    => $email,
            'username' => auth()->user()->username,
            'message'  => $request->message,
        ], ['email']);

        $notify[] = ['success', 'Your email has been sent'];
        return back()->withNotify($notify);
    }

    public function reviewReply(Request $request, $productId, $reviewId) {
        $request->validate([
            'reply' => 'required',
        ]);

        $product = Product::approved()->allActive()->findOrFail($productId);

        if (gs('comment_disable') && $product->comment_disable == Status::ENABLE) {
            $notify[] = ['error', 'Comments are currently disabled for this product'];
            return back()->withNotify($notify);
        }

        $comment               = new Comment();
        $comment->user_id      = auth()->id();
        $comment->product_id   = $productId;
        $comment->text         = $request->reply;
        $comment->author_reply = $product->author->id === auth()->id();
        $comment->parent_id    = $request->parent_id ?? 0;
        $comment->review_id    = $reviewId;
        $comment->save();

        $notify[] = ['success', 'Your reply submitted successfully'];
        return back()->withNotify($notify);
    }

    public function downloadProduct($purchaseCode) {
        $user      = auth()->user();
        $orderItem = $user->purchasedItems()->where('purchase_code', $purchaseCode)->first();
        $product   = @$orderItem->product;

        $user = auth()->user();
        abort_if(($user->id != @$orderItem->user_id || @$orderItem->is_refunded), 404);

        $fileUploader       = new FileUploader();
        $fileUploader->path = getFilePath('productFile') . '/' . $product->slug;
        $fileUploader->file = $product->file;
        return $fileUploader->downloadFile($product, $orderItem);
    }

    public function downloadFreeProduct($slug) {
        $user    = auth()->user();
        $product = Product::where('slug', $slug)->where('is_free', Status::ENABLE)->firstOrFail();

        $download = Download::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($download) {
            $download->increment('download_count');
        } else {

            $download                 = new Download();
            $download->user_id        = $user->id;
            $download->product_id     = $product->id;
            $download->download_count = 1;
            $download->save();
        }

        $fileUploader       = new FileUploader();
        $fileUploader->path = getFilePath('productFile') . '/' . $product->slug;
        $fileUploader->file = $product->file;
        return $fileUploader->downloadFile($product);
    }

    public function refundRequest(Request $request, $purchaseCode) {
        $request->validate([
            'reason' => 'required',
        ]);

        $orderItem = OrderItem::where('purchase_code', $purchaseCode)->where('is_refunded', Status::NO)->whereHas('order', function ($query) {
            $query->where('user_id', auth()->id());
        })->firstOrFail();

        $refundRequest = RefundRequest::whereHas('orderItem', function ($query) use ($purchaseCode) {
            $query->where('purchase_code', $purchaseCode);
        })->first();

        $refundRequest                = new RefundRequest();
        $refundRequest->user_id       = $orderItem->product->user_id;
        $refundRequest->buyer_id      = auth()->id();
        $refundRequest->order_item_id = $orderItem->id;
        $refundRequest->reason        = $request->reason;
        $refundRequest->amount        = $orderItem->product_price + $orderItem->buyer_fee + $orderItem->extended_amount;
        $refundRequest->save();

        $author = User::active()->findOrFail($refundRequest->user_id);
        notify($author, 'NEW_REFUND_REQUEST', [
            'refundable_amount' => showAmount($orderItem->seller_earning, currencyFormat: false),
            'buyer'             => auth()->user()->username,
            'purchase_code'     => $orderItem->purchase_code,
            'reason'            => $refundRequest->reason,
        ], ['email']);

        $notify[] = ['success', 'Your refund request is under review'];
        return back()->withNotify($notify);
    }

    public function acceptRefundRequest(Request $request, $refundId) {
        $refundRequest         = RefundRequest::where('user_id', auth()->id())->findOrFail($refundId);
        $refundRequest->status = Status::APPROVED_REFUND;
        $refundRequest->save();

        $buyer                  = $refundRequest->buyer;
        $orderItem              = $refundRequest->orderItem;
        $orderItem->is_refunded = Status::YES;
        $orderItem->save();

        $product             = $orderItem->product;
        $product->total_sold = max(0, $product->total_sold - 1);
        $product->save();

        $author = $orderItem->product->author;
        $author->balance -= $orderItem->seller_earning;
        $author->total_sold = max(0, $author->total_sold - 1);
        $author->save();

        // seller's transaction
        $sellerTransaction               = new Transaction();
        $sellerTransaction->user_id      = $author->id;
        $sellerTransaction->amount       = $orderItem->seller_earning;
        $sellerTransaction->trx_type     = '-';
        $sellerTransaction->trx          = getTrx();
        $sellerTransaction->details      = 'Refund Amount Subtracted';
        $sellerTransaction->remark       = 'refund';
        $sellerTransaction->post_balance = $author->balance;
        $sellerTransaction->save();

        // buyer's transaction
        $buyer = $refundRequest->buyer;
        $buyer->balance += $refundRequest->amount;
        $buyer->save();

        $buyerTransaction               = new Transaction();
        $buyerTransaction->user_id      = $buyer->id;
        $buyerTransaction->amount       = $refundRequest->amount;
        $buyerTransaction->trx_type     = '+';
        $buyerTransaction->trx          = getTrx();
        $buyerTransaction->details      = 'Refund Amount Added';
        $buyerTransaction->remark       = 'refund';
        $buyerTransaction->post_balance = $buyer->balance;
        $buyerTransaction->save();

        notify($buyer, 'REFUND_APPROVED', [
            'purchase_code' => @$refundRequest->orderItem->purchase_code,
            'buyer'         => $buyer->username,
            'author'        => $author->username,
            'amount'        => $refundRequest->amount,
            'message'       => $request->message ?? 'Your refund was approved',
        ]);

        $notify[] = ['success', 'Refund request accepted successfully'];
        return back()->withNotify($notify);
    }

    public function rejectRefundRequest(Request $request, $refundId) {
        $refundRequest         = RefundRequest::where('user_id', auth()->id())->findOrFail($refundId);
        $buyer                 = $refundRequest->buyer;
        $refundRequest->status = Status::REJECTED_REFUND;
        $refundRequest->save();

        notify($buyer, 'REFUND_REJECTED', [
            'purchase_code' => @$refundRequest->orderItem->purchase_code,
            'buyer'         => $buyer->username,
            'author'        => @auth()->user()->username,
            'message'       => @$request->message ?? 'Your refund was rejected',
        ]);

        $notify[] = ['success', 'You rejected the refund request'];
        return back()->withNotify($notify);
    }

    public function authorInfoForm() {
        if (auth()->user()->is_author == Status::YES) {
            $notify[] = ['error', 'You are already an author'];
            return to_route('user.home')->withNotify($notify);
        }

        $pageTitle = 'Author Information';
        return view('Template::user.author.form', compact('pageTitle'));
    }

    public function authorInfoFormSubmit(Request $request) {
        $form     = Form::where('act', 'author_info')->first();
        $formData = @$form->form_data;

        if (!$formData) {
            $notify[] = ['error', 'Form not found'];
            return back()->withNotify($notify);
        }

        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $authorInfo        = $formProcessor->processFormData($request, $formData);
        $user              = auth()->user();
        $user->author_info = $authorInfo;
        $user->is_author   = Status::YES;
        $user->save();
        return to_route('user.home');
    }

    protected function storeReportAttachments($reportId, $attachments) {
        $path = getFilePath('reviewReport');

        foreach ($attachments as $file) {
            try {
                $attachment                      = new ReportedReviewsAttachment();
                $attachment->reported_reviews_id = $reportId;
                $attachment->attachment          = fileUploader($file, $path);
                $attachment->save();
            } catch (\Exception $exp) {
                $notify[] = ['error', 'File could not upload'];
                return $notify;
            }
        }

        return true;
    }

    protected function validation() {
        $this->files = request()->file('attachments');

        return [
            'review_id'     => 'required|integer|exists:reviews,id',
            'description'   => 'required|string|max:500',
            'attachments.*' => [
                'file',
                function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (!in_array($ext, $this->allowedExtension)) {
                        $fail("Only png, jpg, jpeg, pdf, doc, docx files are allowed.");
                    }
                },
            ],
            'attachments'   => 'array|max:5',
        ];
    }

    public function license() {
        $pageTitle = 'Verify License';
        return view('Template::user.license.index', compact('pageTitle'));
    }

    public function verify(Request $request) {
        $validator = Validator::make($request->all(), [
            'purchase_code' => 'required|size:23|exists:order_items,purchase_code',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $userId       = auth()->id();
        $purchaseCode = $request->purchase_code;
        $orderItem    = OrderItem::where('purchase_code', $purchaseCode)
            ->withWhereHas('product', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->first();

        if (!$orderItem) {
            return response()->json([
                'error' => trans('No records found for the given purchase code or this is not your product'),
            ]);
        }
        $html = view('Template::partials.purchase_details', compact('orderItem'))->render();

        return response()->json(['html' => $html]);
    }

    public function campaign() {
        $pageTitle = 'Manage Campaign Product';
        $author    = auth()->user();

        $campaign = Campaign::active()->where('end_date', '>', now())->latest()->first();

        if (!$campaign) {
            $notify[] = ['error', 'No active campaign found'];
            return back()->withNotify($notify);
        }

        $submittedProductIds = CampaignProduct::where('campaign_id', $campaign->id)
            ->where('user_id', $author->id)
            ->pluck('product_id')
            ->toArray();

        $products = Product::approved()->where('is_free', Status::NO)->where('user_id', $author->id)
            ->whereNotIn('id', $submittedProductIds)
            ->latest()
            ->paginate(getPaginate());

        return view('Template::user.campaign.index', compact('pageTitle', 'products', 'campaign'));
    }

    public function submitProducts(Request $request) {
        $campaign                = Campaign::active()->where('end_date', '>', now())->findOrFail($request->campaign_id);
        $campaignMinimumDiscount = getAmount($campaign->discount_min);
        $campaignMaximumDiscount = getAmount($campaign->discount_max);
        $request->validate([
            'campaign_id'           => 'required|exists:campaigns,id',
            'discount_percentage.*' => "nullable|numeric|min:{$campaignMinimumDiscount}|max:{$campaignMaximumDiscount}",
            'selected_products'     => 'required|array|min:1',
            'selected_products.*'   => 'exists:products,id',
        ], [
            'selected_products.required' => 'Please select at least one product',
            'selected_products.array'    => 'Invalid data format for selected products',
            'selected_products.min'      => 'You must select at least one product',
            'discount_percentage.*.min'  => "The discount percentage must be at least {$campaignMinimumDiscount}%.",
            'discount_percentage.*.max'  => "The discount percentage cannot exceed {$campaignMaximumDiscount}%.",
        ]);

        foreach ($request->selected_products as $productId) {

            $product = Product::where('id', $productId)->approved()->where('user_id', auth()->id())->exists();
            if (!$product) {
                continue;
            }
            $campaignProduct = CampaignProduct::where('campaign_id', $campaign->id)
                ->where('product_id', $productId)
                ->where('user_id', auth()->id())
                ->first();

            if (!$campaignProduct) {
                $campaignProduct              = new CampaignProduct();
                $campaignProduct->campaign_id = $campaign->id;
                $campaignProduct->product_id  = $productId;
                $campaignProduct->user_id     = auth()->id();
            }

            $campaignProduct->discount_percentage = $request->discount_percentage[$productId] ?? 0;
            $campaignProduct->status              = Status::CAMPAIGN_PRODUCT_PENDING;

            $campaignProduct->save();
        }

        $notify[] = ['success', 'Products submitted for review'];
        return back()->withNotify($notify);
    }
    public function viewProducts() {
        $pageTitle        = 'View Products';
        $author           = auth()->user();
        $campaign         = Campaign::active()->where('end_date', '>', now())->first();
        $campaignProducts = CampaignProduct::where('campaign_id', $campaign->id)
            ->where('user_id', $author->id)
            ->paginate(getPaginate());
        return view('Template::user.campaign.view_products', compact('pageTitle', 'campaignProducts'));
    }
}
