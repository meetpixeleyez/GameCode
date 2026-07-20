<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Product extends Model {
    protected $fillable = [
        'title', 'description', 'meta_title ', 'meta_description', 'category_id', 'sub_category_id', 'price', 'price_cl',
        'demo_url', 'attribute_info', 'tags', 'is_free', 'preview_video',
        'reskin_price', 'publish_price', 'store_optimization_price'
    ];
    
    protected $casts = ['attribute_info' => 'object', 'tags' => 'object'];

    protected static $trendingProducts = null;
    public function getMyProductAttribute() {
        return auth()->id() == $this->getAttribute('user_id');
    }

    public function scopeFeatured($query) {
        return $query->where('is_featured', Status::ENABLE);
    }

    public function isTrending() {
        if (is_null(self::$trendingProducts)) {
            self::$trendingProducts = ProductView::selectRaw('product_id, SUM(views) as total_views')
                ->whereBetween('views_date', [now()->subDays(7), now()])
                ->groupBy('product_id')
                ->orderBy('total_views', 'desc')
                ->limit(gs('trending_count'))
                ->pluck('product_id')
                ->toArray();
        }

        return in_array($this->id, self::$trendingProducts);
    }

    public function author() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePending($query) {
        return $query->where('status', Status::PRODUCT_PENDING);
    }

    public function scopeCountComment($query) {
        return $query->withCount([
            'comments' => function ($query) {
                $query->where('review_id', 0)->where('parent_id', 0);
            },
        ]);
    }

    public function scopeAllActive($query) {
        $query->whereHas('category', function ($q) {
            $q->active();
        })->whereHas('subcategory', function ($q) {
            $q->active();
        })->whereHas('author', function ($q) {
            $q->active();
        });
    }

    public function scopeApproved($query) {
        return $query->where('status', Status::PRODUCT_APPROVED);
    }

    public function scopeHardRejected($query) {
        return $query->where('status', Status::PRODUCT_HARD_REJECTED);
    }

    public function scopeSoftRejected($query) {
        return $query->where('status', Status::PRODUCT_SOFT_REJECTED);
    }

    public function scopeDown($query) {
        return $query->where('status', Status::PRODUCT_DOWN);
    }

    public function scopeFileUpdated($query) {
        return $query->where('product_updated', 1);
    }

    public function scopeUpdatePending($query) {
        return $query('product_updated', Status::PRODUCT_UPDATE_PENDING);
    }

    public function scopeUpdateApproved($query) {
        return $query('product_updated', Status::PRODUCT_UPDATE_APPROVED);
    }

    public function scopeUpdateSoftRejected($query) {
        return $query('product_updated', Status::PRODUCT_UPDATE_SOFT_REJECT);
    }

    public function scopeUpdateHardRejected($query) {
        return $query('product_updated', Status::PRODUCT_UPDATE_HARD_REJECT);
    }

    public function scopePermanentDown($query) {
        return $query->where('status', Status::PRODUCT_PERMANENT_DOWN);
    }
    public function scopeWaiting($query) {
        return $query->whereIn('status', [Status::PRODUCT_PENDING, Status::PRODUCT_UPDATE_PENDING]);
    }

    public function changelogs() {
        return $this->hasMany(Changelog::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function subCategory() {
        return $this->belongsTo(SubCategory::class);
    }

    public function orderItems() {
        return $this->hasMany(OrderItem::class)->latest();
    }

    public function productData() {
        return $this->hasMany(ProductData::class, 'product_id', 'id');
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function activities() {
        return $this->hasMany(Activity::class);
    }

    public function users() {
        return $this->belongsToMany(User::class);
    }

    public function collections() {
        return $this->belongsToMany(ProductCollection::class, 'collection_product');
    }

    public function rejections() {
        return $this->hasMany(Rejection::class);
    }

    public function downloads() {
        return $this->hasMany(Download::class, 'product_id');
    }

    public function getDownloadCountAttribute() {
        return $this->downloads()->count();
    }

    public function screenshots() {
        $slug          = $this->slug;
        $extractedPath = getFilePath('screenshots') . '/' . $slug . '/screenshots';

        if (!is_dir($extractedPath)) {
            return [];
        }

        $files = File::allFiles($extractedPath);

        return collect($files)->map(function ($file) use ($extractedPath) {
            return $extractedPath . '/' . $file->getRelativePathname();
        });
    }

    public function updateStatusBadge(): Attribute {
        return new Attribute(function () {
            $html = '';
            if ($this->product_updated == Status::PRODUCT_UPDATE_PENDING) {
                $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
            } else if ($this->product_updated == Status::PRODUCT_UPDATE_APPROVED) {
                $html = '<span class="badge badge--success">' . trans('Approved') . '</span>';
            } else if ($this->product_updated == Status::PRODUCT_UPDATE_SOFT_REJECT) {
                $html = '<span class="badge badge--warning">' . trans('Soft Rejected') . '</span>';
            } else if ($this->product_updated == Status::PRODUCT_UPDATE_HARD_REJECT) {
                $html = '<span class="badge badge--danger">' . trans('Hard Rejected') . '</span>';
            } else if ($this->product_updated == Status::PRODUCT_NO_UPDATE) {
                $html = '<span class="badge bg-secondary">' . trans('No Update') . '</span>';
            }
            return $html;
        });
    }

    public function statusBadge(): Attribute {
        return new Attribute(function () {
            $html = '';
            if ($this->status == Status::PRODUCT_PENDING) {
                $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
            } else if ($this->status == Status::PRODUCT_APPROVED) {
                $html = '<span class="badge badge--success">' . trans('Approved') . '</span>';
            } else if ($this->status == Status::PRODUCT_SOFT_REJECTED) {
                $html = '<span class="badge badge--warning">' . trans('Soft Rejected') . '</span>';
            } else if ($this->status == Status::PRODUCT_HARD_REJECTED) {
                $html = '<span class="badge badge--danger">' . trans('Hard Rejected') . '</span>';
            } else if ($this->status == Status::PRODUCT_DOWN) {
                $html = '<span class="badge badge--warning">' . trans('Soft Disabled') . '</span>';
            } else if ($this->status == Status::PRODUCT_PERMANENT_DOWN) {
                $html = '<span class="badge badge--danger">' . trans('Permanent Disabled') . '</span>';
            }
            return $html;
        });
    }

    public function campaigns() {
        return $this->BelongsToMany(Campaign::class);
    }

    public function campaignProduct() {
        return $this->belongsTo(CampaignProduct::class, 'id', 'product_id')->where('status', Status::CAMPAIGN_PRODUCT_APPROVED);
    }

    public function getCampaignProductPriceAttribute() {
        $campaignProduct = $this->campaignProduct()
            ->whereHas('campaign', function ($query) {
                $query->where('status', Status::CAMPAIGN_ACTIVE)
                    ->where('end_date', '>', now());
            })
            ->first();

        $personalBuyerFee = $this->personalBuyerFee();
        if ($campaignProduct && $campaignProduct->discount_percentage > 0) {
            return [true, ($this->price + $personalBuyerFee) - (($this->price + $personalBuyerFee) * ($campaignProduct->discount_percentage / 100))];
        }
        return [false];
    }

    public function getCampaignProductCommercialPriceAttribute() {
        $campaignProduct = $this->campaignProduct()
            ->whereHas('campaign', function ($query) {
                $query->where('status', Status::CAMPAIGN_ACTIVE)
                    ->where('end_date', '>', now());
            })
            ->first();

        $commercialBuyerFee = $this->commercialBuyerFee();
        if ($campaignProduct && $campaignProduct->discount_percentage > 0) {
            return [true, ($this->price_cl + $commercialBuyerFee) - (($this->price_cl + $commercialBuyerFee) * ($campaignProduct->discount_percentage / 100))];
        }
        return [false];
    }

    public function productPrice($type) {
        if ($type == 'commercial') {
            return $this->price_cl + $this->category->commercial_buyer_fee;
        } else {
            return $this->price + $this->category->personal_buyer_fee;
        }

    }
    public function personalBuyerFee() {
        return $this->category->personal_buyer_fee;
    }

    public function commercialBuyerFee() {
        return $this->category->commercial_buyer_fee;
    }

    public function twelveMonthExtendedFee() {
        return $this->category->twelve_month_extended_fee;
    }

    // Method to get total additional services price
    public function getTotalAdditionalServicesPriceAttribute() {
        return ($this->reskin_price ?? 0) + ($this->publish_price ?? 0) + ($this->store_optimization_price ?? 0);
    }
}
