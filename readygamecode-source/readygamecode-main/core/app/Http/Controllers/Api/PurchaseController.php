<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller {
    public function verifyPurchasedCode(Request $request) {
        $apiKeyValue = $request->header('apikey');

        $apiKey = ApiKey::where('key', $apiKeyValue)->where('status', Status::ACTIVE_KEY)->first();

        if (!$apiKey) {
            return response()->json([
                'status'      => 'error',
                'status_code' => 403,
                'message'     => ['error' => ['Invalid API key']],
            ]);
        }

        $validator = Validator::make($request->all(), [
            'purchase_code' => ['required', 'size:23'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'      => 'error',
                'status_code' => 422,
                'message'     => ['error' => $validator->errors()->all()],
            ]);
        }

        $item = OrderItem::where('purchase_code', $request->purchase_code)->whereHas('product', function ($q) use ($apiKey) {
            $q->whereHas("author", function ($productQuery) use ($apiKey) {
                $productQuery->where('user_id', $apiKey->user_id)->where('is_author', Status::YES)->active();
            });
        })->first();

        if ($item) {
            return response()->json([
                'status'      => 'success',
                'status_code' => 200,
                'message'     => ['success' => ['Purchase code matched']],
            ]);
        }

        return response()->json([
            'status'      => 'error',
            'status_code' => 404,
            'message'     => ['error' => ["Purchase code doesn't match"]],
        ]);
    }
}
