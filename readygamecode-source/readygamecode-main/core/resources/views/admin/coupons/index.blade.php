@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Code')</th>
                                    <th>@lang('Discount Type')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Expire Date')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($coupons as $coupon)
                                    <tr>
                                        <td>
                                            {{ __($coupon->name) }}
                                        </td>

                                        <td>
                                            {{ $coupon->code }}
                                        </td>

                                        <td>
                                            @if ($coupon->discount_type == Status::YES)
                                                <span class="text--small badge font-weight-normal badge--primary">
                                                    {{ $coupon->couponType }}</span>
                                            @else
                                                <span class="text--small badge font-weight-normal badge--dark">
                                                    {{ $coupon->couponType }}</span>
                                            @endif
                                        </td>

                                        <td>
                                            {{ getAmount($coupon->amount) }}
                                            {{ $coupon->discount_type == Status::YES ? gs()->cur_text : '%' }}
                                        </td>

                                        <td>
                                            @php
                                                echo $coupon->statusBadge;
                                            @endphp
                                        </td>

                                        <td>
                                            {{ showDateTime($coupon->end_date, 'd M, Y') }}
                                        </td>

                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.coupon.edit', $coupon->id) }}"
                                                   class="btn btn-sm btn-outline--primary {{ $coupon->status == Status::EXPIRE ? 'disabled' : '' }}">
                                                    <i class="las la-pen"></i> @lang('Edit')
                                                </a>

                                                @if ($coupon->status == Status::DISABLE)
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline--success confirmationBtn {{ $coupon->status == Status::EXPIRE ? 'disabled' : '' }}"
                                                            data-action="{{ route('admin.coupon.status', $coupon->id) }}"
                                                            data-question="@lang('Are you sure to enable this coupon?')">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline--danger confirmationBtn {{ $coupon->status == Status::EXPIRE ? 'disabled' : '' }}"
                                                            data-action="{{ route('admin.coupon.status', $coupon->id) }}"
                                                            data-question="@lang('Are you sure to disable this coupon?')">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($coupons->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($coupons) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search by coupon" />
    <a href="{{ route('admin.coupon.create') }}" class="btn btn-sm btn-outline--primary"><i
           class="las la-plus"></i>@lang('Add New')</a>
@endpush
