<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ManageCouponsController extends Controller {
    public function index() {
        $pageTitle      = "All Coupons";
        $expiredCoupons = Coupon::where('end_date', '<', now())
            ->where('status', Status::ENABLE)
            ->get();

        foreach ($expiredCoupons as $coupon) {
            $coupon->status = Status::EXPIRE;
            $coupon->save();
        }

        $coupons = Coupon::searchable(['name'])->orderByDesc('id')->paginate(getPaginate());
        return view('admin.coupons.index', compact('pageTitle', 'coupons'));
    }

    public function create() {
        $pageTitle = "Create New Coupon";
        $now       = Carbon::now();
        return view('admin.coupons.create', compact('pageTitle', 'now'));
    }

    public function save(Request $request, $id) {

        $request->validate([
            "name"                     => 'required|string|max:40',
            "code"                     => 'required|string|max:20',
            "discount_type"            => 'required|integer|between:1,2',
            "amount"                   => 'required|numeric|gte:0',
            "start_date"               => 'required|date|before_or_equal:end_date',
            "end_date"                 => 'required|date|after_or_equal:start_date',
            "minimum_spend"            => 'required|numeric|min:0',
            "usage_limit_per_coupon"   => 'nullable|integer',
            "usage_limit_per_customer" => 'nullable|integer',

        ]);

        if ($id == 0) {
            $coupon   = new Coupon();
            $notify[] = ['success', 'Coupon Created Successfully'];
        } else {
            $coupon   = Coupon::findOrFail($id);
            $notify[] = ['success', 'Coupon Updated Successfully'];
        }

        $startDate = date('Y-m-d', strtotime($request->start_date));
        $endDate   = date('Y-m-d', strtotime($request->end_date));

        $coupon->name                   = $request->name;
        $coupon->code                   = $request->code;
        $coupon->discount_type          = $request->discount_type;
        $coupon->amount                 = $request->amount;
        $coupon->description            = $request->description;
        $coupon->start_date             = $startDate;
        $coupon->end_date               = $endDate;
        $coupon->minimum_spend          = $request->minimum_spend;
        $coupon->usage_limit_per_coupon = $request->usage_limit_per_coupon;
        $coupon->usage_limit_per_user   = $request->usage_limit_per_customer;

        $coupon->save();

        return redirect()->back()->withNotify($notify);
    }

    public function edit($id) {
        $coupon    = Coupon::findOrFail($id);
        $pageTitle = "Edit Coupon";
        return view('admin.coupons.create', compact('pageTitle', 'coupon'));
    }

    public function status($id) {
        return Coupon::changeStatus($id);
    }
}
