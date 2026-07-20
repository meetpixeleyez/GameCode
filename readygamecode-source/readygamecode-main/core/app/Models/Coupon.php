<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;

class Coupon extends Model
{

    use GlobalStatus;

    protected $fillable = ['code', 'discount', 'type', 'valid_from', 'valid_until', 'usage_limit', 'usage_count', 'is_active'];

    public function isValid()
    {
        return $this->status == 1; 
    }

    public function getCouponTypeAttribute()
    {
        if ($this->discount_type == 1) {
            return 'Fixed';
        } else {
            return 'Percentage';
        }
    }
}
