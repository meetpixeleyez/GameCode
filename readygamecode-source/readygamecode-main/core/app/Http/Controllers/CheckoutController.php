<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\AuthorLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CheckoutController extends Controller
{
    public function index()
    {
        $pageTitle = 'Checkout';
        
        // Set checkout return URL in session for login/register redirects
        if (!auth()->check()) {
            session()->put('checkout_return_url', route('checkout.index'));
        }
        
        // Get cart items (supports both guest and authenticated users)
        if (auth()->check()) {
            $cartItems = Cart::where('user_id', auth()->id())->with('product')->get();
        } else {
            $sessionId = session()->getId();
            $cartItems = Cart::where('session_id', $sessionId)->with('product')->get();
        }

        if (count($cartItems) == 0) {
            $notify[] = ['error', 'No items in your cart'];
            return redirect()->route('cart.index')->withNotify($notify);
        }

        // Calculate totals
        $subtotal = 0;
        foreach ($cartItems as $cartItem) {
            $additionalServicesPrice = ($cartItem->reskin_selected ? $cartItem->product->reskin_price : 0) + 
                                      ($cartItem->publish_selected ? $cartItem->product->publish_price : 0) + 
                                      ($cartItem->store_optimization_selected ? $cartItem->product->store_optimization_price : 0);
            $subtotal += $cartItem->price + $cartItem->buyer_fee + $cartItem->extended_amount + $additionalServicesPrice;
        }

        $discount = Session::get('coupon_total', 0);
        $total = $subtotal - $discount;

        // Get social login credentials for Google
        $socialiteCredentials = gs('socialite_credentials');
        
        // Get enabled payment gateways (PayPal and Razorpay only)
        // Use same query as PaymentController which works
        $gatewayCurrency = \App\Models\GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', \App\Constants\Status::ENABLE);
        })->with('method')->orderby('name')->get();
        
        // Filter for PayPal and Razorpay only
        $paypalGateway = null;
        $razorpayGateway = null;
        
        foreach ($gatewayCurrency as $gateway) {
            $code = strtolower($gateway->method->code ?? '');
            $alias = strtolower($gateway->method->alias ?? '');
            
            // Check for PayPal (including Express, Standard, SDK)
            if (strpos($code, 'paypal') !== false || strpos($alias, 'paypal') !== false ||
                strpos($code, 'express') !== false || strpos($alias, 'express') !== false) {
                $paypalGateway = $gateway;
            }
            
            if (strpos($code, 'razorpay') !== false || strpos($alias, 'razorpay') !== false || 
                strpos($code, 'razor') !== false || strpos($alias, 'razor') !== false) {
                $razorpayGateway = $gateway;
            }
        }
        
        // Debug: Log gateway info
        \Log::info('Checkout gateway lookup', [
            'total_gateways' => $gatewayCurrency->count(),
            'paypal_found' => $paypalGateway ? true : false,
            'razorpay_found' => $razorpayGateway ? true : false,
            'paypal_method_code' => $paypalGateway ? $paypalGateway->method_code : null,
            'razorpay_method_code' => $razorpayGateway ? $razorpayGateway->method_code : null,
        ]);
        
        return view('Template::checkout', compact('pageTitle', 'cartItems', 'subtotal', 'total', 'discount', 'socialiteCredentials', 'paypalGateway', 'razorpayGateway'));
    }

    public function processCheckout(Request $request)
    {
        // This method handles step progression
        if (!auth()->check()) {
            session()->put('checkout_return_url', route('checkout.index'));
            return response()->json(['redirect' => route('checkout.index')]);
        }

        $cartItems = Cart::where('user_id', auth()->id())->with('product')->get();

        if (count($cartItems) == 0) {
            return response()->json(['error' => 'No items in your cart'], 400);
        }

        // Check if user is trying to purchase their own products
        $countOwnProduct = Product::where('user_id', auth()->id())->whereIn('id', $cartItems->pluck('product_id')->toArray())->count();
        if ($countOwnProduct) {
            return response()->json(['error' => 'You cannot purchase your own products'], 400);
        }

        // Check if user profile is complete
        $user = auth()->user();
        if ($user->profile_complete != Status::YES) {
            return response()->json(['profile_incomplete' => true, 'message' => 'Please complete your profile']);
        }

        return response()->json(['success' => true, 'step' => 'payment']);
    }

    public function payment()
    {
        $pageTitle = 'Payment';
        
        if (!auth()->check()) {
            session()->put('checkout_return_url', route('checkout.payment'));
            return redirect()->route('checkout.index');
        }

        $cartItems = Cart::where('user_id', auth()->id())->with('product')->get();

        if (count($cartItems) == 0) {
            $notify[] = ['error', 'No items in your cart'];
            return redirect()->route('cart.index')->withNotify($notify);
        }

        $countOwnProduct = Product::where('user_id', auth()->id())->whereIn('id', $cartItems->pluck('product_id')->toArray())->count();
        if ($countOwnProduct) {
            $notify[] = ['error', 'You cannot purchase your own products'];
            return redirect()->route('cart.index')->withNotify($notify);
        }

        $subtotal = 0;
        foreach ($cartItems as $cartItem) {
            $additionalServicesPrice = ($cartItem->reskin_selected ? $cartItem->product->reskin_price : 0) + 
                                      ($cartItem->publish_selected ? $cartItem->product->publish_price : 0) + 
                                      ($cartItem->store_optimization_selected ? $cartItem->product->store_optimization_price : 0);
            $subtotal += $cartItem->price + $cartItem->buyer_fee + $cartItem->extended_amount + $additionalServicesPrice;
        }

        $discount = Session::get('coupon_total', 0);
        $total = $subtotal - $discount;

        // Get only PayPal and Razorpay gateways
        $gatewayCurrency = \App\Models\GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE)
                 ->whereIn('code', ['paypal', 'razorpay']);
        })->with('method')->orderby('name')->get();

        return view('Template::checkout_payment', compact('pageTitle', 'cartItems', 'subtotal', 'total', 'discount', 'gatewayCurrency'));
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'gateway' => 'required',
        ]);

        if (!auth()->check()) {
            return response()->json(['error' => 'Please login to continue'], 401);
        }

        $cartItems = Cart::where('user_id', auth()->id())->with('product')->get();

        if (count($cartItems) == 0) {
            return response()->json(['error' => 'No items in your cart'], 400);
        }

        $countOwnProduct = Product::where('user_id', auth()->id())->whereIn('id', $cartItems->pluck('product_id')->toArray())->count();
        if ($countOwnProduct) {
            return response()->json(['error' => 'You cannot purchase your own products'], 400);
        }

        // Validate gateway - find by method_code and check if it's PayPal or Razorpay
        $gateway = \App\Models\GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $request->gateway)->with('method')->first();

        if (!$gateway) {
            return response()->json(['error' => 'Invalid payment gateway selected'], 400);
        }
        
        // Verify it's PayPal or Razorpay by checking code/alias
        $code = strtolower($gateway->method->code ?? '');
        $alias = strtolower($gateway->method->alias ?? '');
        
        $isPaypal = strpos($code, 'paypal') !== false || strpos($alias, 'paypal') !== false ||
                    strpos($code, 'express') !== false || strpos($alias, 'express') !== false;
        $isRazorpay = strpos($code, 'razorpay') !== false || strpos($alias, 'razorpay') !== false || 
                      strpos($code, 'razor') !== false || strpos($alias, 'razor') !== false;
        
        if (!$isPaypal && !$isRazorpay) {
            return response()->json(['error' => 'Only PayPal and Razorpay are supported'], 400);
        }

        // Use gateway's currency (don't use request currency or site default)
        // Get the gateway currency directly from the gatewayCurrency record
        $gate = GatewayCurrency::whereHas('method', function ($g) {
            $g->where('status', Status::ENABLE);
        })->where('method_code', $request->gateway)->with('method')->first();
        
        if (!$gate) {
            return response()->json(['error' => 'Invalid gateway selected'], 400);
        }
        
        // Use the gateway's configured currency
        $currency = $gate->currency;
        
        // Calculate discount
        $discount = Session::get('coupon_total', 0);
        
        // Create order first
        $order = $this->createOrder($cartItems);
        
        // Store order TRX in session for thank you page (before payment processing)
        session()->put('order_trx', $order->trx);
        session()->put('order_amount', $order->amount);
        
        // Check deposit limits
        if ($gate->min_amount > $order->amount || $gate->max_amount < $order->amount) {
            return response()->json(['error' => 'Amount must be between ' . showAmount($gate->min_amount) . ' and ' . showAmount($gate->max_amount)], 400);
        }
        
        // Calculate charges
        $charge = $gate->fixed_charge + ($order->amount * $gate->percent_charge / 100);
        $payable = $order->amount + $charge;
        
        // Calculate final amount with rate
        // If rate is 0 or 1, use payable directly (no conversion needed)
        if ($gate->rate > 0 && $gate->rate != 1) {
            $finalAmount = $payable * $gate->rate;
        } else {
            // No rate conversion needed
            $finalAmount = $payable;
        }
        
        // Ensure final_amount is valid and not zero
        if ($finalAmount <= 0) {
            $finalAmount = $order->amount;
        }
        
        // Ensure minimum 2 decimal places for PayPal
        $finalAmount = round($finalAmount, 2);
        
        // Log for debugging
        \Log::info('Payment processing', [
            'order_amount' => $order->amount,
            'charge' => $charge,
            'payable' => $payable,
            'gateway_rate' => $gate->rate,
            'final_amount' => $finalAmount,
            'gateway_code' => $gate->method_code,
            'gateway_alias' => $gate->method->alias ?? 'unknown',
            'currency' => $currency,
            'method_currency' => strtoupper($currency)
        ]);
        
        // Create deposit
        $deposit = new Deposit();
        $deposit->user_id = auth()->id();
        $deposit->order_id = $order->id;
        $deposit->method_code = $gate->method_code;
        $deposit->method_currency = strtoupper($currency);
        $deposit->amount = round($order->amount, 2);
        $deposit->charge = round($charge, 2);
        $deposit->rate = $gate->rate > 0 ? $gate->rate : 1;
        $deposit->final_amount = $finalAmount;
        $deposit->btc_amount = 0;
        $deposit->btc_wallet = "";
        $deposit->trx = getTrx();
        $deposit->success_url = route('checkout.thank.you');
        $deposit->failed_url = route('checkout.index');
        $deposit->save();
        
        session()->put('Track', $deposit->trx);
        
        // Process gateway directly
        $dirName = $gate->method->alias;
        $processController = 'App\\Http\\Controllers\\Gateway\\' . $dirName . '\\ProcessController';
        
        if (!class_exists($processController)) {
            return response()->json(['error' => 'Payment gateway processor not found'], 400);
        }
        
        $gatewayData = $processController::process($deposit);
        $gatewayData = json_decode($gatewayData);
        
        if (isset($gatewayData->error)) {
            return response()->json(['error' => $gatewayData->message], 400);
        }
        
        // Handle redirect for gateways like Mollie that redirect directly
        if (isset($gatewayData->redirect) && isset($gatewayData->redirect_url)) {
            return response()->json([
                'success' => true,
                'redirect_url' => $gatewayData->redirect_url
            ]);
        }
        
        // For gateways that show a payment form (Razorpay, PayPal)
        // Return payment form data and redirect to confirmation page
        return response()->json([
            'success' => true,
            'redirect_url' => route('user.deposit.confirm')
        ]);
    }

    public function thankYou()
    {
        $pageTitle = 'Order Confirmation';

        $order = null;
        if (session('order_trx')) {
            $order = \App\Models\Order::where('trx', session('order_trx'))->with('orderItems.product')->first();
            // Clear session data
            session()->forget('order_trx');
            session()->forget('order_amount');
        }

        return view('Template::checkout_thank_you', compact('pageTitle', 'order'));
    }
    
    private function createOrder($cartItems)
    {
        $discount = Session::get('coupon_total', 0);
        $amount = collect($cartItems)->sum('price');
        if ($discount) {
            $amount -= $discount;
        }

        $extendedAmount = collect($cartItems)->sum('extended_amount');
        $buyerFees = collect($cartItems)->sum('buyer_fee');
        
        // Add additional services prices
        $additionalServicesAmount = collect($cartItems)->sum(function($cartItem) {
            $total = 0;
            if ($cartItem->reskin_selected && $cartItem->product) {
                $total += $cartItem->product->reskin_price;
            }
            if ($cartItem->publish_selected && $cartItem->product) {
                $total += $cartItem->product->publish_price;
            }
            if ($cartItem->store_optimization_selected && $cartItem->product) {
                $total += $cartItem->product->store_optimization_price;
            }
            return $total;
        });

        $amount += $extendedAmount;
        $amount += $buyerFees;
        $amount += $additionalServicesAmount;

        $couponId = collect($cartItems)->first()->coupon_id ?? null;

        $order = new Order();
        $order->user_id = auth()->id();
        $order->amount = $amount;
        $order->discount = $discount ?? '0';
        $order->trx = getTrx();
        $order->coupon_id = $couponId;
        $order->save();

        foreach ($cartItems as $cartItem) {
            $author = $cartItem->product->author;
            $authorLevel = $author->authorLevels()->orderBy('minimum_earning', 'desc')->first();
            if (!$authorLevel) {
                $authorLevel = AuthorLevel::active()->orderBy('minimum_earning')->first();
            }

            $sellerFee = @$authorLevel->fee ?? 0;
            $sellerFee = ($sellerFee / 100) * $cartItem->price;

            $orderItem = new OrderItem();
            $orderItem->user_id = $order->user_id;
            $orderItem->order_id = $order->id;
            $orderItem->purchase_code = getPurchaseCode();
            $orderItem->product_id = $cartItem->product_id;
            $orderItem->is_extended = $cartItem->is_extended;
            $orderItem->extended_amount = $cartItem->is_extended ? $cartItem->extended_amount : 0;
            $orderItem->product_price = $cartItem->price;
            $orderItem->buyer_fee = $cartItem->buyer_fee;
            $orderItem->seller_fee = $sellerFee;
            $orderItem->quantity = $cartItem->quantity;
            $orderItem->license = $cartItem->license;
            
            // Add additional services
            $orderItem->reskin_selected = $cartItem->reskin_selected;
            $orderItem->publish_selected = $cartItem->publish_selected;
            $orderItem->store_optimization_selected = $cartItem->store_optimization_selected;
            
            // Calculate additional services amount
            $additionalServicesAmount = 0;
            if ($cartItem->reskin_selected && $cartItem->product) {
                $additionalServicesAmount += $cartItem->product->reskin_price;
            }
            if ($cartItem->publish_selected && $cartItem->product) {
                $additionalServicesAmount += $cartItem->product->publish_price;
            }
            if ($cartItem->store_optimization_selected && $cartItem->product) {
                $additionalServicesAmount += $cartItem->product->store_optimization_price;
            }
            
            $orderItem->seller_earning = ($cartItem->price - ($sellerFee + $cartItem->discount)) + $cartItem->extended_amount + $additionalServicesAmount;
            $orderItem->discount = $cartItem->discount;
            $orderItem->save();
        }

        return $order;
    }
}