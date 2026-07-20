<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignProduct;
use Illuminate\Http\Request;

class CampaignController extends Controller {
    public function index() {
        $pageTitle = 'Campaigns';
        $campaigns = Campaign::latest()->searchable(['name'])->paginate(getPaginate());
        return view('admin.campaigns.index', compact('campaigns', 'pageTitle'));
    }

    public function create() {
        $pageTitle = "Create New Campaign";
        $isRunning = Campaign::active()->where('end_date', '>', now())->exists();
        if ($isRunning) {
            $notify[] = ['error', 'A campaign is already running. You can create one after it expires.'];
            return back()->withNotify($notify);
        }
        return view('admin.campaigns.create', compact('pageTitle'));
    }

    public function store(Request $request, $id) {
        $request->validate([
            'name'         => 'required|string|max:255',
            'discount_min' => 'required|numeric|min:0|max:100',
            'discount_max' => 'required|numeric|min:0|max:100|gt:discount_min',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after:start_date',
        ]);

        if ($id == 0) {
            $campaigns = new Campaign();
            $notify[]  = ['success', 'Campaign Created Successfully'];
        } else {
            $campaigns = Campaign::findOrFail($id);
            $notify[]  = ['success', 'Campaign Updated Successfully'];
        }

        $startDate = date('Y-m-d', strtotime($request->start_date));
        $endDate   = date('Y-m-d', strtotime($request->end_date));

        $campaigns->name         = $request->name;
        $campaigns->discount_min = $request->discount_min;
        $campaigns->discount_max = $request->discount_max;
        $campaigns->start_date   = $startDate;
        $campaigns->end_date     = $endDate;
        $campaigns->save();

        return to_route('admin.campaign.index')->withNotify($notify);
    }

    public function edit($id) {
        $campaign  = Campaign::where('status', '!=', Status::CAMPAIGN_EXPIRED)->findOrFail($id);
        $pageTitle = "Edit Campaign";
        return view('admin.campaigns.create', compact('pageTitle', 'campaign'));
    }

    public function status($id) {
        $campaign  = Campaign::where('status', '!=', Status::CAMPAIGN_EXPIRED)->findOrFail($id);
        $isRunning = Campaign::where('id', '!=', $id)->active()->where('end_date', '>', now())->exists();

        if ($isRunning) {
            $notify[] = ['error', 'A campaign is already running. You can change one after it expires.'];
            return back()->withNotify($notify);
        }

        if ($campaign->status == Status::CAMPAIGN_ACTIVE) {
            CampaignProduct::where('campaign_id', $id)->update(['status' => Status::CAMPAIGN_PRODUCT_PENDING]);
        }
        return Campaign::changeStatus($id);
    }

    public function showSubmittedProducts(Campaign $campaign) {
        $pageTitle         = 'Submitted Products for Campaign ' . '"' . $campaign->name . '"';
        $submittedProducts = CampaignProduct::where('campaign_id', $campaign->id)
            ->with(['product', 'user'])
            ->paginate(getPaginate());
        return view('admin.campaigns.submitted_products', compact('pageTitle', 'submittedProducts', 'campaign'));
    }

    public function approve($id) {
        $campaignProduct         = CampaignProduct::where('status', Status::CAMPAIGN_PRODUCT_PENDING)->findOrFail($id);
        $user                    = $campaignProduct->user;
        $campaignProduct->status = Status::CAMPAIGN_PRODUCT_APPROVED;
        $campaignProduct->save();

        notify($user, 'CAMPAIGN_PRODUCT_APPROVED', [
            'user_name'        => $user->username,
            'product_name'     => $campaignProduct->product->title,
            'product_price'    => showAmount($campaignProduct->product->price, currencyFormat: false),
            'discount_percent' => getAmount($campaignProduct->discount_percentage),
            'discount'         => ($campaignProduct->product->price * $campaignProduct->discount_percentage) / 100,
            'discount_price'   => $campaignProduct->product->price - (($campaignProduct->product->price * $campaignProduct->discount_percentage) / 100),
        ]);

        $notify[] = ['success', 'Campaign product has been approved'];
        return back()->withNotify($notify);
    }
    public function reject($id) {
        $campaignProduct         = CampaignProduct::where('status', Status::CAMPAIGN_PRODUCT_PENDING)->findOrFail($id);
        $user                    = $campaignProduct->user;
        $campaignProduct->status = Status::CAMPAIGN_PRODUCT_REJECTED;
        $campaignProduct->save();

        notify($user, 'CAMPAIGN_PRODUCT_REJECTED', [
            'user_name'        => $user->username,
            'product_name'     => $campaignProduct->product->title,
            'product_price'    => showAmount($campaignProduct->product->price, currencyFormat: false),
            'discount_percent' => getAmount($campaignProduct->discount_percentage),
            'discount'         => ($campaignProduct->product->price * $campaignProduct->discount_percentage) / 100,
            'discount_price'   => $campaignProduct->product->price - (($campaignProduct->product->price * $campaignProduct->discount_percentage) / 100),
        ]);

        $notify[] = ['success', 'Campaign product has been rejected'];
        return back()->withNotify($notify);
    }
}
