<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function reportDetails()
    {
        return $this->hasOne(ReportedReview::class, 'review_id');
    }

    public function attachments()
    {
        return $this->hasMany(ReportedReviewsAttachment::class, 'reported_reviews_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(ReviewCategory::class, 'review_category_id', 'id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'review_id');
    }
}
