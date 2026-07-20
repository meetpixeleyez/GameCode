@extends($activeTemplate . 'layouts.master')
@section('content')

    @php
        $kyc = getContent('kyc.content', true);
    @endphp

    @if (auth()->check() && $author->isAuthor())
        @if ($pendingProducts)
            <x-alert type="danger" route="{{ route('user.author.hidden_items', ['status' => Status::PRODUCT_PENDING]) }}">
                @lang('You have') {{ $pendingProducts }} @lang('pending')
                {{ __(str()->plural('product', $pendingProducts)) }}
            </x-alert>
        @endif
        @if ($downProducts)
            <x-alert type="danger" route="{{ route('user.author.hidden_items', ['status' => Status::PRODUCT_DOWN]) }}">
                @lang('You have') {{ $downProducts }} @lang('down') {{ __(str()->plural('product', $downProducts)) }}
            </x-alert>
        @endif

        @if ($softRejectedProducts)
            <x-alert type="danger" route="{{ route('user.author.hidden_items', ['status' => Status::PRODUCT_SOFT_REJECTED]) }}">
                @lang('You have') {{ $softRejectedProducts }} @lang('soft rejected')
                {{ __(str()->plural('product', $softRejectedProducts)) }}
            </x-alert>
        @endif
        @if ($unRepliedComments)
            <x-alert type="warning" route="{{ route('user.author.comments.index', ['not_replied' => 1]) }}">
                @lang('You have') {{ $unRepliedComments }} @lang('unreplied')
                {{ __(str()->plural('comment', $unRepliedComments)) }}
            </x-alert>
        @endif

        @if ($unRepliedReviews)
            <x-alert type="warning" route="{{ route('user.author.reviews.index', ['not_replied' => 1]) }}">
                @lang('You have') {{ $unRepliedReviews }} @lang('unreplied')
                {{ __(str()->plural('review', $unRepliedReviews)) }}
            </x-alert>
        @endif
    @endif

    <div class="row gy-4 dashboard-row-wrapper">
        <div class="notice"></div>

        @if ($author->kv == Status::KYC_UNVERIFIED && $author->kyc_rejection_reason)
            <div class="col-12">
                <div class="alert alert--danger" role="alert">
                    <h4 class="alert-heading mb-2 text--danger fs-18">@lang('KYC Documents Rejected') <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#kycRejectionReason">@lang('Show Reason')</button></h4>
                    <p class="fs-16">
                        {{ __(@$kyc->data_values->reject) }}

                        <a href="javascript::void(0)" class="link-color" data-bs-toggle="modal" data-bs-target="#kycRejectionReason">@lang('Click here')</a> @lang('to show the reason').

                        <a href="{{ route('user.kyc.form') }}">@lang('Click Here to Re-submit Documents')</a>.
                        <br>
                        <a href="{{ route('user.kyc.data') }}">@lang('See KYC Data')</a>
                    </p>
                </div>
            </div>
        @elseif($author->kv == Status::KYC_UNVERIFIED)
            <div class="col-12">
                <div class="alert alert--info" role="alert">
                    <h4 class="alert-heading mb-2 text--info fs-18">@lang('KYC Verification required')</h4>
                    <p class="fs-16">{{ __(@$kyc->data_values->required) }} <a href="{{ route('user.kyc.form') }}">@lang('Click Here to Submit Documents')</a>
                    </p>
                </div>
            </div>
        @elseif($author->kv == Status::KYC_PENDING)
            <div class="col-12">
                <div class="alert alert--warning" role="alert">
                    <h4 class="alert-heading mb-2 text--warning fs-18">@lang('KYC Verification pending')</h4>
                    <p class="fs-16">{{ __(@$kyc->data_values->pending) }} <a href="{{ route('user.kyc.data') }}">@lang('See KYC Data')</a></p>
                </div>
            </div>
        @endif

        <div class="col-12">
            @include($activeTemplate . 'user.dashboard.widgets')
        </div>
        @if ($author->is_author)
            @include($activeTemplate . 'user.dashboard.recent_sales')
        @else
            <!--<div class="col-12">-->
            <!--    <div class="card product-card">-->
            <!--        <div class="card-body p-4">-->
            <!--            <div class="row justify-content-center">-->
                            <!--<div class="col-lg-8">-->
                                @php
                                    $becomeAuthor = getContent('become_author.content', true);
                                    $becomeAuthor = @$becomeAuthor->data_values;
                                @endphp
                                <!--<div class="text-center">-->
                                <!--    <h3 class="text--base">{{ __($becomeAuthor->heading) }}</h3>-->
                                <!--    <p class="mb-3">{{ __($becomeAuthor->details) }}</p>-->
                                <!--    <a href="{{ route('user.author.form') }}" class="btn btn--base">{{ __($becomeAuthor->button_text) }}</a>-->
                                <!--</div>-->
                            <!--</div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
        @endif
    </div>

    @if (auth()->user()->kv == Status::KYC_UNVERIFIED && auth()->user()->kyc_rejection_reason)
        <div class="modal fade" id="kycRejectionReason">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('KYC Document Rejection Reason')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ auth()->user()->kyc_rejection_reason }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection
