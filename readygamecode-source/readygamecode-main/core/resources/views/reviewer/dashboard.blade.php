@extends('reviewer.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-xxl-4 col-sm-6">
            <x-widget style="6" link="{{ route('reviewer.product.pending') }}" icon="las la-spinner" title="Pending Products" value="{{ $widget['pending_products'] }}" bg="warning" outline="true"/>
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget style="6" link="{{ route('reviewer.product.assigned') }}" icon="las la-check-circle" title="Assigned To Me" value="{{ $widget['assigned_products'] }}" bg="10" outline="true"/>
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget style="6" link="{{ route('reviewer.product.rejected.soft') }}" icon="las la-times-circle" title="Soft Rejected Products" value="{{ $widget['soft_rejected_products'] }}" bg="warning" outline="true" />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget style="6" link="{{ route('reviewer.product.rejected.hard') }}" icon="las la-ban" title="Hard Rejected Products" value="{{ $widget['hard_rejected_products'] }}" bg="red" outline="true" />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget style="6" link="{{ route('reviewer.product.updated') }}" icon="la la-pencil" title="Updated Products" value="{{ $widget['updated_products'] }}" bg="18" outline="true" />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget style="6" link="{{ route('reviewer.product.approved') }}" icon="las la-check-circle" title="Approved Product" value="{{ $widget['approved_products'] }}" bg="success" outline="true" />
        </div>
    </div>
@endsection
