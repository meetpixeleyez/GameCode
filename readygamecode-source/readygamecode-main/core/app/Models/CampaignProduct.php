<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class CampaignProduct extends Model {
    protected $fillable = ['campaign_id', 'product_id', 'user_id', 'discount_percentage', 'status'];

    public function campaign() {
        return $this->belongsTo(Campaign::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function cartItems() {
        return $this->belongsTo(Cart::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function personalBuyerFee() {
        return $this->category->personal_buyer_fee;
    }

    public function commercialBuyerFee() {
        return $this->category->commercial_buyer_fee;
    }

    public function statusBadge(): Attribute {
        return new Attribute(
            get: fn() => $this->statusBadgeData(),
        );
    }

    public function statusBadgeData() {
        $html = '';
        if ($this->status == Status::CAMPAIGN_PRODUCT_PENDING) {
            $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
        } else if ($this->status == Status::CAMPAIGN_PRODUCT_APPROVED) {
            $html = '<span class="badge badge--success">' . trans('Approved') . '</span>';
        } else if ($this->status == Status::CAMPAIGN_PRODUCT_REJECTED) {
            $html = '<span><span class="badge badge--danger">' . trans('Rejected') . '</span></span>';
        } else if ($this->status == Status::CAMPAIGN_PRODUCT_EXPIRED) {
            $html = '<span><span class="badge badge--danger">' . trans('Expired') . '</span></span>';
        }
        return $html;
    }

    public function scopeApproved($query) {
        return $query->where('status', Status::CAMPAIGN_PRODUCT_APPROVED);
    }
}
