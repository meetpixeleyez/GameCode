<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\User;

class ManageAuthorController extends Controller
{
    public function list()
    {
        $pageTitle = 'Authors';
        $authors   = User::active()->where('is_author', Status::YES)->searchable(['username', 'email'])->latest();
        $authors   = $authors->paginate(getPaginate());
        return view('admin.author.list', compact('pageTitle', 'authors'));
    }

    public function data($id)
    {
        $pageTitle = 'Author Data';
        $user      = User::findOrFail($id);
        return view('admin.users.author_data', compact('pageTitle', 'user'));
    }

    public function toggleFeature($id)
    {
        $user = User::findOrFail($id);

        if ($user->is_author_featured == Status::AUTHOR_FEATURED) {
            $user->is_author_featured = Status::AUTHOR_NOT_FEATURED;
            $user->save();
            $notify[] = ['success', 'Featured status removed successfully'];
        } else {
            User::where('is_author_featured', Status::AUTHOR_FEATURED)
                ->update(['is_author_featured' => Status::AUTHOR_NOT_FEATURED]);

            $user->is_author_featured = Status::AUTHOR_FEATURED;
            $user->save();
            $notify[] = ['success', 'Featured status changed successfully'];
        }

        return back()->withNotify($notify);
    }
}
