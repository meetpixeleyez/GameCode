<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller {
    public function index() {
        $pageTitle = 'Cart';
        $sessionId = request()->session()->getId();
        $cartItems = [];

        if (auth()->check()) {
            $cartItems = Cart::where('user_id', auth()->id())->with('product')->get();
        } else {
            $cartItems = Cart::where('session_id', $sessionId)->with('product')->get();
        }

        $isCoupon = Coupon::where('status', Status::YES)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->exists();

        // Only update coupon for authenticated users
        if (auth()->check()) {
            Cart::where('user_id', auth()->id())->where('coupon_id', session()->get('coupon_id'))->update(['coupon_id' => 0, 'discount' => 0]);
        }

        session()->forget('coupon_total');
        session()->forget('coupon_id');

        return view('Template::cart', compact('pageTitle', 'cartItems', 'isCoupon'));
    }

    // public function store(Request $request) {
    //     $request->validate([
    //         'license'    => 'required',
    //         'product_id' => 'required|exists:products,id',
    //     ]);

    //     if (!in_array($request->license, [1, 2])) {
    //         return response()->json(['status' => 'error', 'message' => 'Invalid license']);
    //     }

    //     $cartItems   = $request->session()->get('cart', []);
    //     $product     = Product::approved()->find($request->product_id);
    //     $newCartItem = $this->getDataForCart($product, null, null, $request);

    //     if (!$newCartItem) {
    //         $response = ['status' => 'error', 'message' => 'Item already added to cart'];
    //         return $request->ajax() ? response()->json($response) : back()->withNotify([$response]);
    //     }

    //     $cartItems[] = $newCartItem;

    //     $request->session()->put('cart', $cartItems);

    //     $response = ['status' => 'success', 'message' => 'Item added to cart'];
    //     return $request->ajax() ? response()->json($response) : back()->withNotify([$response]);
    // }
    
    public function store(Request $request) {
    $request->validate([
        'license'    => 'required',
        'product_id' => 'required|exists:products,id',
    ]);

    if (!in_array($request->license, [1, 2])) {
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid license'
        ]);
    }

    $cartItems   = $request->session()->get('cart', []);
    $product     = Product::approved()->find($request->product_id);
    $newCartItem = $this->getDataForCart($product, null, null, $request);

    if (!$newCartItem) {
        return response()->json([
            'status' => 'error',
            'message' => 'Item already added to cart',
            'redirect_url' => route('cart.index')
        ]);
    }

    // Calculate cart quantity
    if (auth()->check()) {
        $cartQty = Cart::where('user_id', auth()->id())->count();
    } else {
        $sessionId = request()->session()->getId();
        $cartQty = Cart::where('session_id', $sessionId)->count();
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Item added to cart',
        'cartQty' => $cartQty
    ]);
}



    public function delete($productId) {
        if (auth()->check()) {
            $cart = Cart::where('product_id', $productId)->where('user_id', auth()->id())->first();
        } else {
            $sessionId = request()->session()->getId();
            $cart      = Cart::where('product_id', $productId)->where('session_id', $sessionId)->first();
        }

        if (!$cart) {
            $status  = 'error';
            $message = 'Item not found';
        } else {
            $cart->delete();
            $status  = 'success';
            $message = 'Item removed from cart';
        }

        $response = ['status' => $status, 'message' => $message];
        return request()->ajax() ? response()->json($response) : back()->withNotify([$response]);
    }

    private function getDataForCart($product, $license = null, $isExtended = 0, $request = null) {

        $productId         = $product->id;
        $license           = request()->license ?? Status::PERSONAL_LICENSE;
        $isPersonalLicense = $license == Status::PERSONAL_LICENSE;

        $existingCartItem = Cart::where('product_id', $productId);

        if (auth()->check()) {
            $existingCartItem->where('user_id', auth()->id());
        } else {
            $sessionId = $request->session()->getId();
            $existingCartItem->where('session_id', $sessionId);
        }

        $existingCartItem = $existingCartItem->first();

        if ($existingCartItem) {
            return false;
        }

        if ($isPersonalLicense) {
            $price = $product->price;
            if ($product->campaign_product_price[0]) {
                $price = $product->campaign_product_price[1] - $product->personalBuyerFee();
            }
        } else {
            $price = $product->price_cl;
            if ($product->campaign_product_commercial_price[0]) {
                $price = $product->campaign_product_commercial_price[1] - $product->commercialBuyerFee();
            }
        }

        $cart                  = new Cart();
        $cart->product_id      = $product->id;
        $cart->title           = $product->title;
        $cart->category_id     = $product->category_id;
        $cart->category        = @$product->category->name;
        $cart->license         = $license;
        $cart->is_extended     = $isExtended ?? 0;
        $cart->extended_amount = $cart->is_extended ? $product->twelveMonthExtendedFee() : 0;
        $cart->price           = $price;
        $cart->buyer_fee       = $isPersonalLicense ? $product->personalBuyerFee() : $product->commercialBuyerFee();
        $cart->quantity        = 1;

        // Handle additional services
        $cart->reskin_selected = $request->reskin_selected ?? 0;
        $cart->publish_selected = $request->publish_selected ?? 0;
        $cart->store_optimization_selected = $request->store_optimization_selected ?? 0;

        $cart->user_id    = auth()->check() ? auth()->id() : null;
        $cart->session_id = request()->session()->getId();
        $cart->save();

        return $cart->toArray();
    }

    public function collectionToCart($collectionId) {
        $collection = ProductCollection::findOrFail($collectionId);
        $products   = $collection->products;

        $cartItems = request()->session()->get('cart', []);

        foreach ($products as $product) {
            $newCartItem = $this->getDataForCart($product);
            $cartItems[] = $newCartItem;

            request()->session()->put('cart', $cartItems);
        }

        $notify[] = ['success', 'Items added to cart'];
        return back()->withNotify($notify);
    }

    public function toggleExtended($id) {
        $cart = Cart::where('user_id', auth()->id())->with('product')->find($id);

        if (!$cart) {
            return response()->json(['status' => 'error', 'message' => 'Item not found']);
        }

        $cart->is_extended     = !$cart->is_extended;
        $cart->extended_amount = $cart->is_extended ? $cart->product->twelveMonthExtendedFee() : 0;
        $cart->save();

        Cart::where('user_id', auth()->id())->where('coupon_id', session()->get('coupon_id'))->update(['coupon_id' => 0, 'discount' => 0]);
        session()->forget('coupon_total');
        session()->forget('coupon_id');

        return response()->json(['status' => 'success', 'message' => 'Cart item updated']);
    }

    public function toggleAdditionalService(Request $request, $id) {
        $cart = Cart::where('user_id', auth()->id())->with('product')->find($id);

        if (!$cart) {
            return response()->json(['status' => 'error', 'message' => 'Item not found']);
        }

        $service = $request->service;
        $selected = $request->selected;

        switch ($service) {
            case 'reskin':
                $cart->reskin_selected = $selected;
                break;
            case 'publish':
                $cart->publish_selected = $selected;
                break;
            case 'store_optimization':
                $cart->store_optimization_selected = $selected;
                break;
            default:
                return response()->json(['status' => 'error', 'message' => 'Invalid service']);
        }

        $cart->save();

        // Clear coupon when cart changes
        Cart::where('user_id', auth()->id())->where('coupon_id', session()->get('coupon_id'))->update(['coupon_id' => 0, 'discount' => 0]);
        session()->forget('coupon_total');
        session()->forget('coupon_id');

        return response()->json([
            'status' => 'success', 
            'message' => 'Cart item updated',
            'additional_services_price' => $cart->additional_services_price,
            'total_price' => $cart->total_price
        ]);
    }

    public function applyCoupon(Request $request) {
        $request->validate([
            'coupon_code' => 'required|string|max:40',
            'subtotal'    => 'required|numeric|gt:0',
            'cart_ids'    => 'required',
        ]);

        $couponCode = $request->coupon_code;
        $subtotal   = $request->subtotal;
        $user       = auth()->user();
        $cartIds    = $request->cart_ids;

        $coupon = Coupon::where('code', $couponCode)
            ->where('status', Status::YES)
            ->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => trans('Coupon not found yet!'),
            ]);
        }

        if (!($coupon->start_date <= now() && $coupon->end_date >= now())) {
            return response()->json([
                'success' => false,
                'message' => trans('This coupon has been expired'),
            ]);
        }

        if ($subtotal < $coupon->minimum_spend) {
            return response()->json([
                'success' => false,
                'message' => trans('Coupon cannot be applied to this amount'),
            ]);
        }

        $totalUses = Order::where('coupon_id', $coupon->id)->count();
        if (!is_null($coupon->usage_limit_per_coupon) && $totalUses >= $coupon->usage_limit_per_coupon) {
            return response()->json([
                'success' => false,
                'message' => trans('This coupon has reached its usage limit'),
            ]);
        }

        $userUses = Order::where('coupon_id', $coupon->id)->where('user_id', $user->id)->count();
        if (!is_null($coupon->usage_limit_per_user) && $userUses >= $coupon->usage_limit_per_user) {
            return response()->json([
                'success' => false,
                'message' => trans('This coupon has reached its user usage limit'),
            ]);
        }

        $carts = Cart::whereIn('id', $cartIds)->get();

        foreach ($carts as $cart) {
            $cart->coupon_id = $coupon->id;

            if ($coupon->discount_type == Status::DISCOUNT_PERCENT) {
                $discount = ($cart->price + $cart->buyer_fee + $cart->extended_amount) * $coupon->amount / 100;
            } else {
                $discount = $coupon->amount;
            }

            $cart->discount = $discount;
            $cart->save();
        }

        $discount = $carts->sum('discount');
        Session::put('coupon_total', $discount);
        Session::put('coupon_id', $coupon->id);

        return response()->json([
            'success'  => true,
            'discount' => getAmount($discount),
            'message'  => trans('Coupon applied successfully.'),
        ]);
    }
}
