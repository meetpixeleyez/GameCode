<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BlogPost;
use App\Models\BlogCategory;

class CreateInitialBlogSeeder extends Seeder
{
    public function run(): void
    {
        $site = gs();
        $siteName = $site->site_name ?? 'Our Marketplace';

        // Ensure a default category exists (optional)
        $category = BlogCategory::firstOrCreate(
            ['slug' => 'news'],
            ['name' => 'News', 'is_active' => true]
        );

        $title = 'Welcome to ' . $siteName;
        $slug  = slug($title);

        BlogPost::firstOrCreate(
            ['slug' => $slug],
            [
                'blog_category_id' => $category->id,
                'title'            => $title,
                'excerpt'          => 'An introduction to ' . $siteName . ' — what we offer and how to get started.',
                'body'             => '<p>Thanks for visiting <strong>' . e($siteName) . '</strong>! Here you\'ll find high‑quality source codes and helpful resources.\n</p><p>What you can do here:</p><ul><li>Explore products with clear licensing</li><li>Add optional services like reskin, publish & store optimization</li><li>Secure checkout with multiple gateways</li></ul><p>Follow our blog for updates, tips and new releases.</p>',
                'is_published'     => true,
                'published_at'     => now(),
            ]
        );
    }
}


