<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\GoogleAuthenticator;
use App\Models\DeviceToken;
use App\Models\Form;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function home()
    {
        $pageTitle            = 'Dashboard';
        $author               = auth()->user();

        $unRepliedComments    = $author->comments()->where('review_id', 0)->where('parent_id', 0)->withCount('replies')->having('replies_count', 0)->count();
        $unRepliedReviews     = $author->reviews()->withCount('replies')->having('replies_count', 0)->count();

        $softRejectedProducts = $author->products->where('status', Status::PRODUCT_SOFT_REJECTED)->count();
        $pendingProducts      = $author->products->where('status', Status::PRODUCT_PENDING)->count();
        $downProducts         = $author->products->where('status', Status::PRODUCT_DOWN)->count();

        $query       = $author->soldItems();
        $saleAmount  = $author->soldItems()->sum('seller_earning');

        $recentSales = $author->soldItems()->with(['order' => function ($query) {
            $query->where('payment_status', Status::PAYMENT_SUCCESS);
        }])->latest()->with('product')->limit(10)->get();

        $purchases           = $author->orderItems()->where('is_refunded', Status::NO);
        $totalPurchaseAmount = $purchases->sum('product_price') + $purchases->sum('buyer_fee') + $purchases->sum('extended_amount');
        return view('Template::user.dashboard', compact('saleAmount','pageTitle','recentSales','author','unRepliedComments','softRejectedProducts','unRepliedReviews','pendingProducts','downProducts','totalPurchaseAmount'));
    }

    public function depositHistory(Request $request)
    {
        $pageTitle = 'Deposit History';
        $deposits = auth()->user()->deposits()->searchable(['trx'])->with(['gateway'])->orderBy('id','desc')->paginate(getPaginate());
        return view('Template::user.deposit_history', compact('pageTitle', 'deposits'));
    }

    public function show2faForm()
    {
        $ga = new GoogleAuthenticator();
        $user = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . gs('site_name'), $secret);
        $pageTitle = '2FA Security';
        return view('Template::user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'key' => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user,$request->code,$request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts = Status::ENABLE;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator activated successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }

    public function disable2fa(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);

        $user = auth()->user();
        $response = verifyG2fa($user,$request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts = Status::DISABLE;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }

    public function transactions()
    {
        $pageTitle = 'Transactions';
        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');

        $transactions = Transaction::where('user_id',auth()->id())->searchable(['trx'])->filter(['trx_type','remark'])->orderBy('id','desc')->paginate(getPaginate());

        return view('Template::user.transactions', compact('pageTitle','transactions','remarks'));
    }

    public function kycForm()
    {
        if (auth()->user()->kv == Status::KYC_PENDING) {
            $notify[] = ['error','Your KYC is under review'];
            return to_route('user.home')->withNotify($notify);
        }
        if (auth()->user()->kv == Status::KYC_VERIFIED) {
            $notify[] = ['error','You are already KYC verified'];
            return to_route('user.home')->withNotify($notify);
        }
        $pageTitle = 'KYC Form';
        $form = Form::where('act','kyc')->first();
        return view('Template::user.kyc.form', compact('pageTitle','form'));
    }

    public function kycData()
    {
        $user = auth()->user();
        $pageTitle = 'KYC Data';
        abort_if($user->kv == Status::VERIFIED,403);
        return view('Template::user.kyc.info', compact('pageTitle','user'));
    }

    public function kycSubmit(Request $request)
    {
        $form = Form::where('act','kyc')->firstOrFail();
        $formData = $form->form_data;
        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $user = auth()->user();
        foreach (@$user->kyc_data ?? [] as $kycData) {
            if ($kycData->type == 'file') {
                fileManager()->removeFile(getFilePath('verify').'/'.$kycData->value);
            }
        }
        $userData = $formProcessor->processFormData($request, $formData);
        $user->kyc_data = $userData;
        $user->kyc_rejection_reason = null;
        $user->kv = Status::KYC_PENDING;
        $user->save();

        $notify[] = ['success','KYC data submitted successfully'];
        return to_route('user.home')->withNotify($notify);

    }

    public function userData()
    {
        $user = auth()->user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }

        $pageTitle  = 'User Data';
        $info       = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = @implode(',', $info['code']);
        $countries  = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('Template::user.user_data', compact('pageTitle', 'user', 'countries', 'mobileCode'));
    }

    public function userDataSubmit(Request $request)
    {

        $user = auth()->user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }

        $countryData  = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes  = implode(',', array_column($countryData, 'dial_code'));
        $countries    = implode(',', array_column($countryData, 'country'));

        // Make username and mobile conditionally required based on whether user already has them
        $validationRules = [
            'country_code' => 'required|in:' . $countryCodes,
            'country'      => 'required|in:' . $countries,
        ];
        
        // Only require mobile_code if user doesn't have one (they need to set it)
        if (!$user->mobile || !$user->dial_code) {
            $validationRules['mobile_code'] = 'required|in:' . $mobileCodes;
        }
        
        // Only require username if user doesn't have one
        if (!$user->username) {
            $validationRules['username'] = 'required|unique:users|min:6';
        }
        
        // Only require mobile if user doesn't have one
        if (!$user->mobile) {
            $validationRules['mobile'] = ['required','regex:/^([0-9]*)$/',Rule::unique('users')->where('dial_code',$request->mobile_code)];
        }
        
        // If user already has mobile_code, use it from user record if not provided
        if ($user->dial_code && !$request->has('mobile_code')) {
            $request->merge(['mobile_code' => $user->dial_code]);
        }
        
        try {
            $request->validate($validationRules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // If AJAX request, return JSON error response
            if (request()->wantsJson() || request()->ajax() || request()->header('X-Requested-With') == 'XMLHttpRequest') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }


        // Validate and set username only if provided and user doesn't have one
        if ($request->has('username') && !$user->username) {
            if (preg_match("/[^a-z0-9_]/", trim($request->username))) {
                if (request()->wantsJson() || request()->ajax() || request()->header('X-Requested-With') == 'XMLHttpRequest') {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Username can contain only small letters, numbers and underscore.',
                        'errors' => ['username' => ['No special character, space or capital letters in username.']]
                    ], 422);
                }
                $notify[] = ['info', 'Username can contain only small letters, numbers and underscore.'];
                $notify[] = ['error', 'No special character, space or capital letters in username.'];
                return back()->withNotify($notify)->withInput($request->all());
            }
            $user->username = $request->username;
        }

        $user->country_code = $request->country_code;
        
        // Only update mobile if provided and user doesn't have one
        if ($request->has('mobile') && !$user->mobile) {
            $user->mobile = $request->mobile;
            $user->dial_code = $request->mobile_code;
        }

        // Update address fields if provided
        if ($request->has('address')) {
            $user->address = $request->address;
        }
        if ($request->has('city')) {
            $user->city = $request->city;
        }
        if ($request->has('state')) {
            $user->state = $request->state;
        }
        if ($request->has('zip')) {
            $user->zip = $request->zip;
        }
        if ($request->has('country')) {
            $user->country_name = $request->country;
        }

        $user->profile_complete = Status::YES;
        $user->save();

        // Check if request is from checkout (AJAX request)
        if (request()->wantsJson() || request()->ajax() || request()->header('X-Requested-With') == 'XMLHttpRequest') {
            // Return JSON response for AJAX requests (from checkout page)
            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'profile_complete' => true,
                'redirect' => null // Don't redirect, let Vue handle the step change
            ]);
        }

        // After completing profile during checkout, return to payment if set
        $checkoutReturnUrl = session()->get('checkout_return_url');
        if ($checkoutReturnUrl) {
            session()->forget('checkout_return_url');
            return redirect($checkoutReturnUrl);
        }

        return to_route('user.home');
    }


    public function addDeviceToken(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()->all()];
        }

        $deviceToken = DeviceToken::where('token', $request->token)->first();

        if ($deviceToken) {
            return ['success' => true, 'message' => 'Already exists'];
        }

        $deviceToken          = new DeviceToken();
        $deviceToken->user_id = auth()->user()->id;
        $deviceToken->token   = $request->token;
        $deviceToken->is_app  = Status::NO;
        $deviceToken->save();

        return ['success' => true, 'message' => 'Token saved successfully'];
    }

    public function downloadAttachment($fileHash)
    {
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title = slug(gs('site_name')).'- attachments.'.$extension;
        try {
            $mimetype = mime_content_type($filePath);
        } catch (\Exception $e) {
            $notify[] = ['error','File does not exists'];
            return back()->withNotify($notify);
        }
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

}
