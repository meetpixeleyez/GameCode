<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $pageTitle = 'Blog Categories';
        $categories = BlogCategory::orderBy('name')->paginate(20);
        return view('admin.blog.categories.index', compact('pageTitle','categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required','max:120'],
            'slug' => ['required','max:160', Rule::unique('blog_categories','slug')->ignore($request->id)],
            'is_active' => ['nullable','in:0,1']
        ]);

        $cat = $request->id ? BlogCategory::findOrFail($request->id) : new BlogCategory();
        $cat->name = $request->name;
        $cat->slug = slug($request->slug);
        $cat->is_active = $request->boolean('is_active');
        $cat->save();

        $notify[] = ['success', 'Category saved'];
        return back()->withNotify($notify);
    }

    public function delete($id)
    {
        $cat = BlogCategory::findOrFail($id);
        $cat->delete();
        $notify[] = ['success', 'Category deleted'];
        return back()->withNotify($notify);
    }
}


