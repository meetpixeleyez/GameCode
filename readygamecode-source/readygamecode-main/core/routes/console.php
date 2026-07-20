<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewCategory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('products:set-random-sales {--min=5} {--max=20} {--dry-run}', function () {
    $min = (int) $this->option('min');
    $max = (int) $this->option('max');
    if ($min > $max) {
        [$min, $max] = [$max, $min];
    }

    $dryRun = (bool) $this->option('dry-run');
    $updated = 0;

    Product::query()->chunkById(100, function ($products) use (&$updated, $min, $max, $dryRun) {
        foreach ($products as $product) {
            $value = random_int($min, $max);
            if (!$dryRun) {
                $product->total_sold = $value;
                $product->save();
            }
            $updated++;
        }
    });

    if ($dryRun) {
        $this->info("Dry run complete. Would update {$updated} products with random sales between {$min} and {$max}.");
    } else {
        $this->info("Updated {$updated} products with random sales between {$min} and {$max}.");
    }
})->purpose('Set random total_sold for all products');

Artisan::command('reviews:add-foreign {--exclude-country=} {--min=1} {--max=3} {--rating-min=4} {--rating-max=5} {--dry-run}', function () {
    $exclude = $this->option('exclude-country');
    $minPer = (int) $this->option('min');
    $maxPer = (int) $this->option('max');
    $ratingMin = max(1, (int) $this->option('rating-min'));
    $ratingMax = min(5, (int) $this->option('rating-max'));

    if ($minPer > $maxPer) {
        [$minPer, $maxPer] = [$maxPer, $minPer];
    }
    if ($ratingMin > $ratingMax) {
        [$ratingMin, $ratingMax] = [$ratingMax, $ratingMin];
    }

    $dryRun = (bool) $this->option('dry-run');

    // Determine local country if not provided: most common among active users
    if (!$exclude) {
        $topCountry = User::active()
            ->select('country_code', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('country_code')
            ->groupBy('country_code')
            ->orderBy('cnt', 'desc')
            ->first();
        $exclude = $topCountry?->country_code;
        $this->info('Detected local country code: ' . ($exclude ?: 'none'));
    }

    // Eligible foreign users
    $eligibleUsersQuery = User::active()
        ->whereNotNull('country_code')
        ->whereNotNull('username');
    if ($exclude) {
        $eligibleUsersQuery->where('country_code', '!=', $exclude);
    }
    $eligibleUsers = $eligibleUsersQuery->select('id', 'username', 'country_name')->get();

    if ($eligibleUsers->isEmpty()) {
        $this->error('No eligible foreign users found. Aborting.');
        return;
    }

    $categories = ReviewCategory::active()->pluck('id')->all();

    $phrases = [
        'Solid experience — performs as advertised.',
        'Impressed with the quality and attention to detail.',
        'Easy to use and integrates smoothly.',
        'Great value; saved me a lot of time.',
        'Documentation is clear; setup was straightforward.',
        'Supportive author and responsive to questions.',
        'Works well on my stack without issues.',
        'Reliable performance across different environments.',
        'Exactly what I needed for my project.',
        'Sleek, efficient, and well-built.'
    ];

    $createdCount = 0;
    $productCount = 0;

    Product::approved()->chunkById(50, function ($products) use (&$createdCount, &$productCount, $eligibleUsers, $categories, $phrases, $minPer, $maxPer, $ratingMin, $ratingMax, $dryRun) {
        foreach ($products as $product) {
            $productCount++;
            $perProduct = random_int($minPer, $maxPer);

            // Shuffle eligible users and pick unique reviewers not yet reviewed
            $shuffled = $eligibleUsers->shuffle();
            $selected = [];
            foreach ($shuffled as $user) {
                if (count($selected) >= $perProduct) break;
                $exists = Review::where('product_id', $product->id)->where('user_id', $user->id)->exists();
                if (!$exists) {
                    $selected[] = $user;
                }
            }

            foreach ($selected as $user) {
                $rating = random_int($ratingMin, $ratingMax);
                $categoryId = $categories ? $categories[array_rand($categories)] : null;
                $text = $phrases[array_rand($phrases)];

                // Add a small country hint to the description for realism
                $description = $text . ' Reviewed from ' . ($user->country_name ?: 'abroad') . '.';

                if (!$dryRun) {
                    $review = new Review();
                    $review->user_id = $user->id;
                    $review->product_id = $product->id;
                    $review->author_id = $product->user_id;
                    $review->review_category_id = $categoryId;
                    $review->review = $description;
                    $review->rating = $rating;
                    $review->save();
                }
                $createdCount++;
            }

            // Recalculate aggregates for product and author
            if (!$dryRun && !empty($selected)) {
                $author = $product->author;
                $product->total_review = $product->reviews()->count();
                $product->avg_rating = $product->reviews()->avg('rating');
                $product->save();

                if ($author) {
                    $author->total_review = $author->reviews()->count();
                    $author->avg_rating = $author->reviews()->avg('rating');
                    $author->save();
                }
            }
        }
    });

    if ($dryRun) {
        $this->info("Dry run complete. Would add {$createdCount} foreign reviews across {$productCount} products.");
    } else {
        $this->info("Added {$createdCount} foreign reviews across {$productCount} products.");
    }
})->purpose('Generate realistic reviews from foreign (out-of-country) users');
