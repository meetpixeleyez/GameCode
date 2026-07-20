<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model {
    protected $fillable = [
        'product_id', 'user_id', 'session_id', 'title', 'category_id', 'category',
        'license', 'is_extended', 'extended_amount', 'price', 'seller_fee', 'buyer_fee',
        'quantity', 'coupon_id', 'discount', 'reskin_selected', 'publish_selected', 'store_optimization_selected'
    ];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function campaignProduct() {
        return $this->belongsTo(CampaignProduct::class, 'id', 'product_id')->where('status', Status::CAMPAIGN_PRODUCT_APPROVED);
    }

    // New methods for additional services
    public function getAdditionalServicesPriceAttribute() {
        $total = 0;
        if ($this->reskin_selected && $this->product) {
            $total += $this->product->reskin_price;
        }
        if ($this->publish_selected && $this->product) {
            $total += $this->product->publish_price;
        }
        if ($this->store_optimization_selected && $this->product) {
            $total += $this->product->store_optimization_price;
        }
        return $total;
    }

    public function getTotalPriceAttribute() {
        return $this->price + $this->buyer_fee + $this->extended_amount + $this->additional_services_price;
    }
}
