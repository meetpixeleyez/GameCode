<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = 'Blog';
        $query = BlogPost::with('category')->where('is_published', true)->orderByDesc('published_at');
        if ($search = trim((string)$request->q)) {
            $query->where(function($q) use ($search){
                $q->where('title','like',"%{$search}%")->orWhere('excerpt','like',"%{$search}%");
            });
        }
        if ($cat = $request->category) {
            $query->whereHas('category', fn($q)=>$q->where('slug',$cat));
        }
        $posts = $query->paginate(9)->withQueryString();
        $categories = BlogCategory::where('is_active', true)->orderBy('name')->get();
        return view('Template::blog.index', compact('pageTitle','posts','categories'));
    }

    public function show($slug)
    {
        $post = BlogPost::with('category')->where('slug', $slug)->where('is_published', true)->firstOrFail();
        $pageTitle = $post->title;
        $related = BlogPost::where('is_published', true)
            ->where('id','!=',$post->id)
            ->when($post->blog_category_id, fn($q)=>$q->where('blog_category_id',$post->blog_category_id))
            ->latest('published_at')->limit(3)->get();
        return view('Template::blog.show', compact('pageTitle','post','related'));
    }
}


