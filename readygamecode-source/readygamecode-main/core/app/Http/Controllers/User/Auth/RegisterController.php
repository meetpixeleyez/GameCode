<?php

namespace App\Http\Controllers\User\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\Intended;
use App\Models\AdminNotification;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{

    use RegistersUsers;

    public function __construct()
    {
        parent::__construct();
    }

    public function showRegistrationForm()
    {
        $pageTitle = "Register";
        if(gs('registration')){
            Intended::identifyRoute();
            return view('Template::user.auth.register', compact('pageTitle'));
        }else{
            return view('Template::user.auth.registration_disabled', compact('pageTitle'));
        }
    }


    protected function validator(array $data)
    {

        $passwordValidation = Password::min(6);

        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $agree = 'nullable';
        if (gs('agree')) {
            $agree = 'required';
        }

        $validate     = Validator::make($data, [
            'firstname' => 'required',
            'lastname'  => 'required',
            'email'     => 'required|string|email|unique:users',
            'password'  => ['required', 'confirmed', $passwordValidation],
            'captcha'   => 'sometimes|required',
            'agree'     => $agree
        ],[
            'firstname.required'=>'The first name field is required',
            'lastname.required'=>'The last name field is required'
        ]);

        return $validate;
    }

    public function register(Request $request)
    {
        if (!gs('registration')) {
            $notify[] = ['error', 'Registration not allowed'];
            $redirect = session()->has('checkout_return_url') ? redirect()->route('checkout.index') : back();
            return $redirect->withNotify($notify);
        }
        
        try {
            $this->validator($request->all())->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // If validation fails and coming from checkout, redirect back to checkout
            if (session()->has('checkout_return_url')) {
                return redirect()->route('checkout.index')->withErrors($e->errors())->withInput();
            }
            throw $e;
        }

        $request->session()->regenerateToken();

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            $redirect = session()->has('checkout_return_url') ? redirect()->route('checkout.index') : back();
            return $redirect->withNotify($notify);
        }

        // Store current session ID before registration for cart transfer
        $currentSessionId = session()->getId();
        session()->put('pre_register_session_id', $currentSessionId);

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }



    protected function create(array $data)
    {
        $referBy = session()->get('reference');
        if ($referBy) {
            $referUser = User::where('username', $referBy)->first();
        } else {
            $referUser = null;
        }

        //User Create
        $user            = new User();
        $user->email     = strtolower($data['email']);
        $user->firstname = $data['firstname'];
        $user->lastname  = $data['lastname'];
        $user->password  = Hash::make($data['password']);
        $user->ref_by    = $referUser ? $referUser->id : 0;
        $user->kv = gs('kv') ? Status::NO : Status::YES;
        $user->ev = gs('ev') ? Status::NO : Status::YES;
        $user->sv = gs('sv') ? Status::NO : Status::YES;
        $user->ts = Status::DISABLE;
        $user->tv = Status::ENABLE;
        $user->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'New member registered';
        $adminNotification->click_url = urlPath('admin.users.detail', $user->id);
        $adminNotification->save();


        //Login Log Create
        $ip        = getRealIP();
        $exist     = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin();

        if ($exist) {
            $userLogin->longitude    = $exist->longitude;
            $userLogin->latitude     = $exist->latitude;
            $userLogin->city         = $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country      = $exist->country;
        } else {
            $info                    = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude    = @implode(',', $info['long']);
            $userLogin->latitude     = @implode(',', $info['lat']);
            $userLogin->city         = @implode(',', $info['city']);
            $userLogin->country_code = @implode(',', $info['code']);
            $userLogin->country      = @implode(',', $info['country']);
        }

        $userAgent          = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip = $ip;

        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os      = @$userAgent['os_platform'];
        $userLogin->save();


        return $user;
    }

    public function checkUser(Request $request){
        $exist['data'] = false;
        $exist['type'] = null;
        if ($request->email) {
            $exist['data'] = User::where('email',$request->email)->exists();
            $exist['type'] = 'email';
            $exist['field'] = 'Email';
        }
        if ($request->mobile) {
            $exist['data'] = User::where('mobile',$request->mobile)->where('dial_code',$request->mobile_code)->exists();
            $exist['type'] = 'mobile';
            $exist['field'] = 'Mobile';
        }
        if ($request->username) {
            $exist['data'] = User::where('username',$request->username)->exists();
            $exist['type'] = 'username';
            $exist['field'] = 'Username';
        }
        return response($exist);
    }

    public function registered(Request $request, $user)
    {
        // Ensure cart is transferred to the user BEFORE any redirect
        $this->transferCartItemsToUser($user);

        $user->tv = $user->ts == Status::VERIFIED ? Status::UNVERIFIED : Status::VERIFIED;
        $user->save();
        $ip = getRealIP();
        $exist = UserLogin::where('user_ip',$ip)->first();
        $userLogin = new UserLogin();
        if ($exist) {
            $userLogin->longitude =  $exist->longitude;
            $userLogin->latitude =  $exist->latitude;
            $userLogin->city =  $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country =  $exist->country;
        }else{
            $info = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude =  @implode(',',$info['long']);
            $userLogin->latitude =  @implode(',',$info['lat']);
            $userLogin->city =  @implode(',',$info['city']);
            $userLogin->country_code = @implode(',',$info['code']);
            $userLogin->country =  @implode(',', $info['country']);
        }

        $userAgent = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip =  $ip;

        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os = @$userAgent['os_platform'];
        $userLogin->save();

        // Check for checkout return URL first - even if profile not complete
        $checkoutReturnUrl = session()->get('checkout_return_url');
        if ($checkoutReturnUrl) {
            session()->forget('checkout_return_url');
            session()->put('checkout', true);
            return redirect($checkoutReturnUrl);
        }

        // If profile is not complete and no checkout, send user to complete profile
        if (!$user->profile_complete) {
            return to_route('user.data');
        }

        $redirection = Intended::getRedirection();
        return $redirection ? $redirection : to_route('user.home');
    }

    private function transferCartItemsToUser($user)
    {
        // Get the session ID from before registration
        $preRegisterSessionId = session()->get('pre_register_session_id');
        
        if (!$preRegisterSessionId) {
            return;
        }
        
        // Get cart items from the pre-registration session
        $sessionCartItems = \App\Models\Cart::where('session_id', $preRegisterSessionId)->get();
        
        foreach ($sessionCartItems as $cartItem) {
            // Check if user already has this product in cart
            $existingCartItem = \App\Models\Cart::where('user_id', $user->id)
                ->where('product_id', $cartItem->product_id)
                ->first();
            
            if ($existingCartItem) {
                // Update existing cart item with session data
                $existingCartItem->update([
                    'reskin_selected' => $cartItem->reskin_selected,
                    'publish_selected' => $cartItem->publish_selected,
                    'store_optimization_selected' => $cartItem->store_optimization_selected,
                    'is_extended' => $cartItem->is_extended,
                    'extended_amount' => $cartItem->extended_amount,
                ]);
                // Delete the session cart item
                $cartItem->delete();
            } else {
                // Transfer session cart item to user
                $cartItem->update([
                    'user_id' => $user->id,
                    'session_id' => null,
                ]);
            }
        }
        
        // Clear the pre-registration session ID
        session()->forget('pre_register_session_id');
    }

}
