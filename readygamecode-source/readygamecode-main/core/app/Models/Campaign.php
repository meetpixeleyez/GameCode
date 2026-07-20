<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model {
    use GlobalStatus;
    protected $fillable = ['name', 'discount_min', 'discount_max', 'start_date', 'end_date'];

    public function products() {
        return $this->belongsToMany(Product::class);
    }

    public function campaignsProducts() {
        return $this->belongsToMany(CampaignProduct::class);
    }
    
    public function campaignsItem() {
        return $this->hasMany(CampaignProduct::class, 'campaign_id');
    }

    public function scopeActive($query) {
        return $query->where('status', Status::CAMPAIGN_ACTIVE);
    }

    public function scopePending($query) {
        return $query->where('status', Status::CAMPAIGN_DISABLED);
    }

    public function scopeExpired($query) {
        return $query->where('status', Status::CAMPAIGN_EXPIRED);
    }
}
