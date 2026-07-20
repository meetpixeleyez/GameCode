<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\ReportedReviewsAttachment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    public function index($authorId = 0)
    {
        $pageTitle = 'Reviews';
        $reviews = Review::latest()->searchable(['review', 'user:username', 'product:title'])->filter(['is_reported'])->dateFilter()->with(['product']);
        if ($authorId) $reviews->where('author_id', $authorId);
        $reviews = $reviews->paginate(getPaginate());
        return view('admin.review.index', compact('pageTitle', 'reviews'));
    }

    public function details($reviewId)
    {
        $pageTitle = 'Review Report Details';
        $review   = Review::where('id', $reviewId)->with(['product'])->firstOrFail();
        return view('admin.review.details', compact('pageTitle', 'review'));
    }

    public function download($attachmentId)
    {
        $attachment = ReportedReviewsAttachment::find(decrypt($attachmentId));
        if (!$attachment) {
            abort(404);
        }

        $file = $attachment->attachment;
        $path = getFilePath('reviewReport');
        $fullPath = $path . '/' . $file;
        if (!file_exists($fullPath)) {
            $notify[] = ['error', 'Attachment not found'];
            return back()->withNotify($notify);
        }
        $title = pathinfo($file, PATHINFO_FILENAME);
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $mimetype = mime_content_type($fullPath);
        header('Content-Disposition: attachment; filename="' . $title . '.' . $ext . '";');
        header("Content-Type: " . $mimetype);
        return readfile($fullPath);
    }

    public function show($id)
    {
        $review = Review::where('is_reported', Status::YES)->findOrFail($id);
        $review->is_reported = 0;
        $review->save();

        if ($review->reportDetails) {
            if ($review->reportDetails->attachments->count() > 0) {
                $path = getFilePath('reviewReport');
                foreach ($review->reportDetails->attachments as $attachment) {
                    try {
                        fileManager()->removeFile($path . '/' . $attachment->attachment);
                        $attachment->delete();
                    } catch (\Exception $e) {
                        Log::error('Error deleting review attachment: ' . $e->getMessage());
                    }
                }
            }
            $review->reportDetails->delete();
        }

        $notify[] = ['success', 'Review shown successfully'];
        return to_route('admin.review.index')->withNotify($notify);
    }

    public function  destroy($id)
    {

        $review              = Review::findOrFail($id);
        $product             = $review->product;
        $review->delete();

        $user               = User::findOrFail($product->user_id);
        $user->total_review = $user->reviews()->count();
        $user->avg_rating   = $user->reviews()->avg('rating');
        $user->save();

        $product->total_review = $product->reviews()->count();
        $product->avg_rating   = $product->reviews()->avg('rating');
        $product->save();


        $rating = Rating::where('value', '>=', @$product->avg_rating ?? 0)->first() ?? Rating::orderBy('value', 'desc')->first();

        if ($rating) {
            $rating->product_count -= 1;
            if ($rating->product_count == 0) $rating->product_count = 0;
            $rating->save();
        }

        if ($review->reportDetails) {
            if ($review->reportDetails->attachments->count() > 0) {
                $path = getFilePath('reviewReport');
                foreach ($review->reportDetails->attachments as $attachment) {
                    fileManager()->removeFile($path . '/' . $attachment->attachment);
                    $attachment->delete();
                }
            }
            $review->reportDetails->delete();
        }

        $notify[] = ['success', 'Review deleted successfully'];
        return redirect()->route('admin.review.index')->with('notify', $notify);
    }
}
