<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Comment;

class CommentController extends Controller
{
    public function index($authorId = 0, $commentId = 0)
    {
        $pageTitle = 'Comments';
        $comments  = Comment::where('review_id', 0)->searchable(['text', 'user:username', 'product:title'])->filter(['is_reported'])->dateFilter()->with(['user', 'product']);
        if ($authorId) {
            $comments = $comments->where('user_id', $authorId);
        }
        if ($commentId) {
            $comments = $comments->where('id', $commentId);
        }
        $comments = $comments->paginate(getPaginate());
        return view('admin.comment.index', compact('pageTitle', 'comments'));
    }

    public function details($commentId)
    {
        $pageTitle = 'Comment Report Details';
        $comment = Comment::where('id', $commentId)->with(['product'])->firstOrFail();
        return view('admin.comment.details', compact('pageTitle', 'comment'));
    }

    public function destroy($id)
    {
        $comment  = Comment::findOrFail($id);
        $comment->delete();
        $notify[] = ['success', 'Comment deleted'];
        return to_route('admin.comment.index')->withNotify($notify);
    }

    public function show($id)
    {
        $comment                = Comment::where('is_reported', Status::YES)->findOrFail($id);
        $comment->is_reported   = 0;
        $comment->report_reason = null;
        $comment->save();

        $notify[] = ['success', 'Comment shown successfully'];
        return to_route('admin.comment.index')->withNotify($notify);
    }

    public function repliesList($commentId)
    {
        $pageTitle = 'Replies';
        $comment   = Comment::findOrFail($commentId);
        $replies   = $comment->replies()->searchable(['text', 'user:username', 'product:title'])->with(['user', 'product'])->paginate(getPaginate());
        return view('admin.comment.reply.index', compact('pageTitle', 'replies'));
    }

    public function deleteReply($id)
    {
        $comment  = Comment::findOrFail($id);
        $comment->delete();
        $notify[] = ['success', 'Reply deleted'];
        return back()->withNotify($notify);
    }
}
