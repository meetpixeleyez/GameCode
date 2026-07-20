<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthorController extends Controller {

    public function productsAll(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|exists:users,username',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $username = $request->username;

        $user = User::where('username', $username)->first();

        if (!$user) {
            $notify[] = 'User not found';
            return responseError('not_found', $notify);
        }

        $products = Product::latest('id')->where('user_id', $user->id)->get();

        if ($products->isEmpty()) {
            $notify[] = 'No products found';
            return responseError('no_found', $notify);
        }

        $notify[] = 'Author products fetched successfully';
        return responseSuccess('author_products', $notify, [
            'products' => $products,
        ]);
    }

    public function productsDetail(Request $request) {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $productId = request()->product_id;

        $product = Product::find($productId);

        if (!$product) {
            $notify[] = 'Product not found';
            return responseError('not_found', $notify);
        }

        $imagePath    = getFilePath('productPreview');
        $thumbnail    = getFilePath('productThumbnail');
        $videoPreview = getFilePath('previewVideo');

        $notify[] = 'Product fetched successfully';
        return responseSuccess('product_detail', $notify, [
            'image_path'    => $imagePath,
            'thumbnail'     => $thumbnail,
            'video_preview' => $videoPreview,
            'product'       => $product,
        ]);
    }
}
