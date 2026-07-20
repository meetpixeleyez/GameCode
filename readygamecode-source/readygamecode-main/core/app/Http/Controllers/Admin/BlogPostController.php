<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BlogPostController extends Controller
{
    public function index()
    {
        $pageTitle = 'Blog Posts';
        $posts = BlogPost::with('category')->orderByDesc('id')->paginate(20);
        $categories = BlogCategory::orderBy('name')->get();
        return view('admin.blog.posts.index', compact('pageTitle','posts','categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required','max:200'],
            'slug' => ['nullable','max:220', Rule::unique('blog_posts','slug')->ignore($request->id)],
            'blog_category_id' => ['nullable','exists:blog_categories,id'],
            'body' => ['required'],
            'excerpt' => ['nullable','max:500'],
            'cover_image' => ['nullable','image','mimes:jpg,jpeg,png','max:2048'],
            'is_published' => ['nullable','in:0,1']
        ]);

        try {
            $post = $request->id ? BlogPost::findOrFail($request->id) : new BlogPost();
            $post->fill($request->only('blog_category_id','title'));
            // Auto-generate slug if not provided
            $post->slug = $request->slug ? slug($request->slug) : slug($request->title);
            $post->excerpt = $request->excerpt;
            // Allow HTML formatting in body (headings, bullet points, etc.)
            $post->body = $request->body;
            $post->is_published = $request->boolean('is_published');
            $post->published_at = $post->is_published ? now() : null;

            if ($request->hasFile('cover_image')) {
                // Use dedicated blog cover path
                $uploadPath = getFilePath('blogCover');
                if (!file_exists($uploadPath)) {
                    @mkdir($uploadPath, 0755, true);
                }
                try {
                    $fileName = fileUploader($request->cover_image, $uploadPath);
                    $post->cover_image = $fileName;
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Image upload failed: '.$exp->getMessage()];
                    return back()->withInput()->withNotify($notify);
                }
            }

            $post->save();
        } catch (\Throwable $e) {
            \Log::error('Blog post save failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $notify[] = ['error', 'Failed to save post: '.$e->getMessage()];
            return back()->withInput()->withNotify($notify);
        }

        $notify[] = ['success', 'Post saved'];
        return back()->withNotify($notify);
    }

    public function delete($id)
    {
        $post = BlogPost::findOrFail($id);
        $post->delete();
        $notify[] = ['success', 'Post deleted'];
        return back()->withNotify($notify);
    }
}


