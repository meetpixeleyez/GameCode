<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\ApiKey;

class ApiKeyController extends Controller {

    public function index() {
        $pageTitle = 'Api Documentation';
        $user      = auth()->user();

        if (!$user->is_author) {
            $notify[] = ['error', 'Only author can allow to access'];
            return back()->withNotify($notify);
        }

        $apiKey = ApiKey::where('user_id', auth()->id())->where('status', Status::ACTIVE_KEY)->first();
        return view('Template::user.api_key', compact('pageTitle', 'apiKey'));
    }

    public function keyGenerate() {
        $user      = auth()->user();
        $latestKey = ApiKey::where('user_id', $user->id)->where('status', Status::ACTIVE_KEY)->first();

        if (@$latestKey) {
            $latestKey->status = Status::INACTIVE_KEY;
            $latestKey->save();
        }

        $newKey          = new ApiKey();
        $newKey->user_id = $user->id;
        $newKey->key     = generateApiKey();
        $newKey->status  = Status::ACTIVE_KEY;
        $newKey->save();

        $notify[] = ['success', 'API key generated successfully'];
        return back()->withNotify($notify);
    }
}
