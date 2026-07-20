<?php

namespace App\Http\Controllers\Gateway;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\Referral;
use App\Models\AdminNotification;
use App\Models\AuthorLevel;
use App\Models\Cart;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller {
    public function deposit(Request $request) {
        $discount = Session::get('coupon_total');
        if ($discount) {
            $amount = getCartAmount(auth()->user()->cartItems) - $discount;
        } else {
            $amount = getCartAmount(auth()->user()->cartItems);
        }

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('name')->get();

        $pageTitle = 'Deposit Methods';
        return view('Template::user.payment.deposit', compact('gatewayCurrency', 'pageTitle', 'amount'));
    }

    public function depositInsert(Request $request) {
        // Get gateway from session if not provided in request (from checkout flow)
        $gateway = $request->gateway ?? session()->get('selected_gateway');
        $currency = $request->currency ?? session()->get('selected_currency');
        
        $request->validate([
            'gateway'  => 'required',
        ]);

        // If currency is required for the gateway, validate it
        if ($gateway !== 'wallet' && !$currency) {
            $notify[] = ['error', 'Currency is required for this payment method'];
            return back()->withNotify($notify);
        }

        $discount = Session::get('coupon_total');

        $user    = auth()->user();
        $balance = $user->balance;

        $cartItems = $user->cartItems;

        if ($gateway == 'wallet') {
            $amount = $discount ? (getCartAmount($cartItems) - $discount) : getCartAmount($cartItems);

            if ($amount > $balance) {
                $notify[] = ['error', 'Insufficient balance'];
                return back()->withNotify($notify);
            }
        }

        $order = $this->createOrder($cartItems);

        // Store order TRX in session for thank you page (before payment processing)
        session()->put('order_trx', $order->trx);
        session()->put('order_amount', $order->amount);

        if ($gateway == 'wallet') {
            return $this->orderFromAccountBalance($order);
        }

        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $gateway)->where('currency', $currency)->first();

        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        if ($gate->min_amount > $order->amount || $gate->max_amount < $order->amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        $charge      = $gate->fixed_charge + ($order->amount * $gate->percent_charge / 100);
        $payable     = $order->amount + $charge;
        $finalAmount = $payable * $gate->rate;

        $data                  = new Deposit();
        $data->user_id         = $user->id;
        $data->order_id        = $order->id;
        $data->method_code     = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount          = $order->amount;
        $data->charge          = $charge;
        $data->rate            = $gate->rate;
        $data->final_amount    = $finalAmount;
        $data->btc_amount      = 0;
        $data->btc_wallet      = "";
        $data->trx             = getTrx();
        
        // Redirect to checkout thank you page after successful payment
        $data->success_url     = route('checkout.thank.you');
        $data->failed_url      = route('checkout.index');
        $data->save();
        session()->put('Track', $data->trx);
        
        // Clear checkout session data
        session()->forget('selected_gateway');
        session()->forget('selected_currency');
        
        return to_route('user.deposit.confirm');
    }

    private function orderFromAccountBalance($order) {
        // Store order TRX in session for thank you page (for wallet payment)
        session()->put('order_trx', $order->trx);
        session()->put('order_amount', $order->amount);
        
        $amount = $order->amount;

        $order->payment_status = Status::PAYMENT_SUCCESS;
        $order->save();

        $buyer = $order->user;
        $buyer->balance -= $amount;
        $buyer->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $buyer->id;
        $transaction->amount       = $amount;
        $transaction->post_balance = $buyer->balance;
        $transaction->charge       = 0;
        $transaction->trx_type     = '-';
        $transaction->details      = 'New Item Purchase';
        $transaction->trx          = $order->trx;
        $transaction->remark       = 'purchase';
        $transaction->save();

        $authorTransactions   = [];
        $totalGetSellerAmount = 0;

        foreach ($order->orderItems as $orderItem) {
            // Calculate additional services amount for seller earning
            $additionalServicesAmount = 0;
            if ($orderItem->reskin_selected && $orderItem->product) {
                $additionalServicesAmount += $orderItem->product->reskin_price;
            }
            if ($orderItem->publish_selected && $orderItem->product) {
                $additionalServicesAmount += $orderItem->product->publish_price;
            }
            if ($orderItem->store_optimization_selected && $orderItem->product) {
                $additionalServicesAmount += $orderItem->product->store_optimization_price;
            }
            
            $sellerEarning       = ($orderItem->product_price + $orderItem->extended_amount + $additionalServicesAmount) - $orderItem->discount;
            $author              = $orderItem->product->author;
            $author->balance    += $sellerEarning;
            $author->total_sold += 1;
            $author->save();

            $product = $orderItem->product;
            $product->total_sold += 1;
            $product->save();

            // give seller amount
            $authorTransactions[] = [
                'user_id'      => $author->id,
                'trx_type'     => '+',
                'trx'          => $order->trx,
                'remark'       => "new_sale",
                'details'      => 'Sale amount added',
                'amount'       => $sellerEarning,
                'post_balance' => $author->balance,
                'created_at'   => now(),
            ];

            // subtract seller fee
            $author->balance -= $orderItem->seller_fee;
            $excludingSellerFee = $sellerEarning - $orderItem->seller_fee;
            $totalGetSellerAmount += $excludingSellerFee;
            $author->total_sold_amount += $excludingSellerFee; // excluding seller fee

            $author->save();

            if ($orderItem->seller_fee > 0) {
                $authorTransactions[] = [
                    'user_id'      => $author->id,
                    'trx_type'     => '-',
                    'trx'          => $order->trx,
                    'remark'       => 'seller_fee',
                    'details'      => 'Seller fee subtracted',
                    'amount'       => $orderItem->seller_fee,
                    'post_balance' => $author->balance,
                    'created_at'   => now(),
                ];
            }

            $authorLevels = AuthorLevel::active()->where('minimum_earning', '<=', $author->total_sold_amount)->pluck('id')->toArray();
            $author->authorLevels()->sync($authorLevels);
        }

        Transaction::insert($authorTransactions);
        session()->forget('cart');
        session()->forget('Track');
        Cart::where('user_id', $order->user_id)->delete();

        // Referral Commission
        if (gs('referral') && $buyer->ref_by) {
            Referral::processReferralCommission($buyer, $totalGetSellerAmount, $order);
        }

        $notify[] = ['success', 'Order Completed Successfully'];
        return redirect()->route('checkout.thank.you')->withNotify($notify);
    }

    public function appDepositConfirm($hash) {
        try {
            $id = decrypt($hash);
        } catch (\Exception $ex) {
            abort(404);
        }
        $data = Deposit::where('id', $id)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->firstOrFail();
        $user = User::findOrFail($data->user_id);
        auth()->login($user);
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }

    public function depositConfirm() {

        $track   = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->with('gateway')->firstOrFail();

        if ($deposit->method_code >= 1000) {
            return to_route('user.deposit.manual.confirm');
        }

        $dirName = $deposit->gateway->alias;
        $new     = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);

        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return back()->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if (@$data->session) {
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';
        return view("Template::$data->view", compact('data', 'pageTitle', 'deposit'));
    }

    public static function userDataUpdate($deposit, $isManual = null) {
        if ($deposit->status == Status::PAYMENT_INITIATE || $deposit->status == Status::PAYMENT_PENDING) {
            $deposit->status = Status::PAYMENT_SUCCESS;
            $deposit->save();

            $user = User::find($deposit->user_id);
            $user->balance += $deposit->amount;
            $user->save();

            $methodName = $deposit->methodName();

            $transaction               = new Transaction();
            $transaction->user_id      = $deposit->user_id;
            $transaction->amount       = $deposit->amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge       = $deposit->charge;
            $transaction->trx_type     = '+';
            $transaction->details      = 'Payment for Purchase Via ' . $methodName;
            $transaction->trx          = $deposit->trx;
            $transaction->remark       = 'payment';
            $transaction->save();

            if ($deposit->order_id) {
                $order                 = $deposit->order;
                $order->payment_status = Status::PAYMENT_SUCCESS;
                $order->save();

                $user->balance -= $deposit->amount;
                $user->save();

                $transaction               = new Transaction();
                $transaction->user_id      = $deposit->user_id;
                $transaction->amount       = $order->amount;
                $transaction->post_balance = $user->balance;
                $transaction->charge       = 0;
                $transaction->trx_type     = '-';
                $transaction->details      = 'Payment for Purchase Item';
                $transaction->trx          = $deposit->trx;
                $transaction->remark       = 'purchase';
                $transaction->save();

                $authorTransactions   = [];
                $totalGetSellerAmount = 0;

                foreach ($order->orderItems as $orderItem) {
                    $author           = $orderItem->product->author;
                    
                    // Calculate additional services amount for seller earning
                    $additionalServicesAmount = 0;
                    if ($orderItem->reskin_selected && $orderItem->product) {
                        $additionalServicesAmount += $orderItem->product->reskin_price;
                    }
                    if ($orderItem->publish_selected && $orderItem->product) {
                        $additionalServicesAmount += $orderItem->product->publish_price;
                    }
                    if ($orderItem->store_optimization_selected && $orderItem->product) {
                        $additionalServicesAmount += $orderItem->product->store_optimization_price;
                    }
                    
                    $sellerEarning    = ($orderItem->product_price + $orderItem->extended_amount + $additionalServicesAmount) - $orderItem->discount;
                    $author->balance += $sellerEarning;
                    $author->save();

                    $product = $orderItem->product;
                    $product->total_sold += 1;
                    $product->save();

                    // give seller amount
                    $authorTransactions[] = [
                        'user_id'      => $author->id,
                        'trx_type'     => '+',
                        'trx'          => $order->trx,
                        'remark'       => 'new_sale',
                        'details'      => 'Sale Amount Added',
                        'amount'       => $sellerEarning,
                        'post_balance' => $author->balance,
                    ];

                    // cut seller fee
                    $author->balance -= $orderItem->seller_fee;
                    $excludingSellerFee = $sellerEarning - $orderItem->seller_fee;
                    $totalGetSellerAmount += $excludingSellerFee;
                    $author->total_sold_amount += $excludingSellerFee; // excluding seller fee

                    $author->save();

                    if ($orderItem->seller_fee > 0) {
                        $authorTransactions[] = [
                            'user_id'      => $author->id,
                            'trx_type'     => '-',
                            'trx'          => $order->trx,
                            'remark'       => 'seller_fee',
                            'details'      => 'Seller Fee Subtracted',
                            'amount'       => $orderItem->seller_fee,
                            'post_balance' => $author->balance,
                        ];
                    }

                    $author->total_sold += 1;
                    $author->save();

                    $authorLevels = AuthorLevel::active()->where('minimum_earning', '<=', $author->total_sold_amount)->pluck('id')->toArray();
                    $author->authorLevels()->sync($authorLevels);
                }

                Transaction::insert($authorTransactions);
                session()->forget('cart');
                session()->forget('Track');
                Cart::where('user_id', $deposit->user_id)->delete();
                Session::flash('coupon_total', 'coupon_id');

                // Referral Commission
                if (gs('referral') && $user->ref_by) {
                    Referral::processReferralCommission($user, $totalGetSellerAmount, $order);
                }
            }

            if (!$isManual) {
                $adminNotification            = new AdminNotification();
                $adminNotification->user_id   = $user->id;
                $adminNotification->title     = 'Payment successful via ' . $methodName;
                $adminNotification->click_url = urlPath('admin.deposit.successful');
                $adminNotification->save();
            }

            notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                'method_name'     => $methodName,
                'method_currency' => $deposit->method_currency,
                'method_amount'   => showAmount($deposit->final_amount, currencyFormat: false),
                'amount'          => showAmount($deposit->amount, currencyFormat: false),
                'charge'          => showAmount($deposit->charge, currencyFormat: false),
                'rate'            => showAmount($deposit->rate, currencyFormat: false),
                'trx'             => $deposit->trx,
                'post_balance'    => showAmount($user->balance),
            ]);
        }
    }

    public function manualDepositConfirm() {
        $track = session()->get('Track');
        $data  = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        if ($data->method_code > 999) {
            $pageTitle = 'Confirm Payment';
            $method    = $data->gatewayCurrency();
            $gateway   = $method->method;
            return view('Template::user.payment.manual', compact('data', 'pageTitle', 'method', 'gateway'));
        }
        abort(404);
    }

    public function manualDepositUpdate(Request $request) {
        $track = session()->get('Track');
        $data  = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        $gatewayCurrency = $data->gatewayCurrency();
        $gateway         = $gatewayCurrency->method;
        $formData        = $gateway->form->form_data;

        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);

        $data->detail = $userData;
        $data->status = Status::PAYMENT_PENDING;
        $data->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $data->user->id;
        $adminNotification->title     = 'Deposit request from ' . $data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
        $adminNotification->save();

        notify($data->user, 'DEPOSIT_REQUEST', [
            'method_name'     => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount'   => showAmount($data->final_amount, currencyFormat: false),
            'amount'          => showAmount($data->amount, currencyFormat: false),
            'charge'          => showAmount($data->charge, currencyFormat: false),
            'rate'            => showAmount($data->rate, currencyFormat: false),
            'trx'             => $data->trx,
        ]);

        $notify[] = ['success', 'Your deposit request has been taken'];
        return to_route('user.deposit.history')->withNotify($notify);
    }

    /**
     * Create a new order
     * @param Collection $cartItems
     * @return Order
     */
    private function createOrder($cartItems) {
        $discount = Session::get('coupon_total');
        $amount   = collect($cartItems)->sum('price');
        if ($discount) {
            $amount -= $discount;
        }

        $extendedAmount = collect($cartItems)->sum('extended_amount');
        $buyerFees      = collect($cartItems)->sum('buyer_fee');
        
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

        $couponId = collect($cartItems)->first()->coupon_id;

        $order            = new Order();
        $order->user_id   = auth()->user()->id;
        $order->amount    = $amount;
        $order->discount  = $discount ?? '0';
        $order->trx       = getTrx();
        $order->coupon_id = $couponId;
        $order->save();

        foreach ($cartItems as $cartItem) {
            $author      = $cartItem->product->author;
            $authorLevel = $author->authorLevels()->orderBy('minimum_earning', 'desc')->first();
            if (!$authorLevel) {
                $authorLevel = AuthorLevel::active()->orderBy('minimum_earning')->first();
            }

            $sellerFee = @$authorLevel->fee ?? 0;
            $sellerFee = ($sellerFee / 100) * $cartItem->price;

            $orderItem                  = new OrderItem();
            $orderItem->user_id         = $order->user_id;
            $orderItem->order_id        = $order->id;
            $orderItem->purchase_code   = getPurchaseCode();
            $orderItem->product_id      = $cartItem->product_id;
            $orderItem->is_extended     = $cartItem->is_extended;
            $orderItem->extended_amount = $cartItem->is_extended ? $cartItem->extended_amount : 0;
            $orderItem->product_price   = $cartItem->price;
            $orderItem->buyer_fee       = $cartItem->buyer_fee;
            $orderItem->seller_fee      = $sellerFee;
            $orderItem->quantity        = $cartItem->quantity;
            $orderItem->license         = $cartItem->license;
            
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
            
            $orderItem->seller_earning  = ($cartItem->price - ($sellerFee + $cartItem->discount)) + $cartItem->extended_amount + $additionalServicesAmount;
            $orderItem->discount        = $cartItem->discount;
            $orderItem->save();
        }

        return $order;
    }
}
