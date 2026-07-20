<?php

namespace App\Http\Controllers\Gateway\Paypal;

use App\Constants\Status;
use App\Models\Deposit;
use App\Http\Controllers\Gateway\PaymentController;
use App\Http\Controllers\Controller;
use App\Lib\CurlRequest;

class ProcessController extends Controller
{

    public static function process($deposit)
    {
        $general = gs();
        $paypalAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $val['cmd'] = '_xclick';
        $val['business'] = trim($paypalAcc->paypal_email);
        $val['cbt'] = $general->site_name;
        // Use deposit's method_currency (should match gateway configuration)
        $val['currency_code'] = strtoupper($deposit->method_currency);
        $val['quantity'] = 1;
        $val['item_name'] = "Payment To $general->site_name Account";
        $val['custom'] = "$deposit->trx";
        
        // Ensure amount is valid - use final_amount, fallback to amount + charge
        $amount = $deposit->final_amount;
        if ($amount <= 0) {
            $amount = $deposit->amount + $deposit->charge;
        }
        
        // Ensure amount is at least 0.01 (PayPal minimum)
        if ($amount < 0.01) {
            $amount = $deposit->amount;
        }
        
        $val['amount'] = round($amount, 2);
        
        // Log for debugging
        \Log::info('PayPal payment processing', [
            'deposit_trx' => $deposit->trx,
            'deposit_amount' => $deposit->amount,
            'deposit_charge' => $deposit->charge,
            'deposit_final_amount' => $deposit->final_amount,
            'paypal_amount' => $val['amount'],
            'currency_code' => $val['currency_code']
        ]);
        $val['return'] = $deposit->success_url;
        $val['cancel_return'] = $deposit->failed_url;
        $val['notify_url'] = route('ipn.' . $deposit->gateway->alias);
        $send['val'] = $val;
        $send['view'] = 'user.payment.redirect';
        $send['method'] = 'post';
        // $send['url'] = 'https://www.sandbox.paypal.com/'; // use for sandbod text
        $send['url'] = 'https://www.paypal.com/cgi-bin/webscr';
        return json_encode($send);
    }

    public function ipn()
    {
        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2)
                $myPost[$keyval[0]] = urldecode($keyval[1]);
        }

        $req = 'cmd=_notify-validate';
        foreach ($myPost as $key => $value) {
            $value = urlencode(stripslashes($value));
            $req .= "&$key=$value";
            $details[$key] = $value;
        }

        // $paypalURL = "https://ipnpb.sandbox.paypal.com/cgi-bin/webscr?"; // use for sandbox text
        $paypalURL = "https://ipnpb.paypal.com/cgi-bin/webscr?";
        $url = $paypalURL . $req;
        $response = CurlRequest::curlContent($url);

        if ($response == "VERIFIED") {
            $deposit = Deposit::where('trx', $_POST['custom'])->orderBy('id', 'DESC')->first();
            $deposit->detail = $details;
            $deposit->save();

            if ($_POST['mc_gross'] == round($deposit->final_amount, 2) && $deposit->status == Status::PAYMENT_INITIATE) {
                PaymentController::userDataUpdate($deposit);
            }
        }
    }
}
