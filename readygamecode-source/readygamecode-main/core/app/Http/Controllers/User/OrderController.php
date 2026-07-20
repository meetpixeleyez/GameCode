<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ReviewCategory;
use Illuminate\Support\Facades\Request;

class OrderController extends Controller {
    public function orderList() {
        $pageTitle = 'Purchase History';
        $author    = auth()->user();
        $orders    = $author->orders()->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.orders.list', compact('pageTitle', 'orders', 'author'));
    }

    public function store(Request $request) {
        $cartItems = auth()->user()->cartItems;

        if (count($cartItems) == 0) {
            $notify[] = ['error', 'No items in your cart'];
            return back()->withNotify($notify);
        }

        $countOwnProduct = Product::where('user_id', auth()->id())->whereIn('id', $cartItems->pluck('product_id')->toArray())->count();

        if ($countOwnProduct) {
            $notify[] = ['error', 'you cannot purchase your product'];
            return back()->withNotify($notify);
        }

        return redirect()->route('user.deposit.index');
    }

    public function details($orderId) {
        $orderBy          = request()->order_by;
        $reviewCategories = ReviewCategory::active()->get();
        $order            = Order::where('user_id', auth()->id())->where('id', $orderId)->where('payment_status', Status::PAYMENT_SUCCESS)->first();

        if (!$order) {
            $notify[] = ['error', 'Payment process do not completed yet'];
            return back()->withNotify($notify);
        }

        $pageTitle      = 'Purchased Item: ' . $order->trx;
        $purchasedItems = OrderItem::where('order_id', $order->id)->latest('id')->searchable(['product:title', 'purchase_code'])->where('is_refunded', Status::NO)->paginate(getPaginate());

        return view('Template::user.download', compact('pageTitle', 'purchasedItems', 'reviewCategories'));
    }
}
