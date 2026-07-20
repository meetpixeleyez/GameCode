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
                                    <th>@lang('Product')</th>
                                    <th>@lang('Author')</th>
                                    <th>@lang('Discount Percentage')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($submittedProducts as $submitProduct)
                                    <tr>
                                        <td>
                                            <div class="user d-flex">
                                                <div class="thumb me-2">
                                                    <img src="{{ getImage(getFilePath('productThumbnail') . '/' . productFilePath($submitProduct->product, 'thumbnail')) }}"
                                                         alt="@lang('Product Image')">
                                                </div>
                                                <div>
                                                    <a
                                                       href="{{ route('admin.product.details', $submitProduct->product->slug) }}">{{ __(strLimit(@$submitProduct->product->title, 20)) }}</a>
                                                    <br>
                                                    <span
                                                          class="text--small">{{ showDateTime($submitProduct->product->created_at) }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ __(@$submitProduct->user->fullname) }}</span>
                                            <br>
                                            <span>
                                                <a
                                                   href="{{ route('admin.users.detail', $submitProduct->user->id) }}"><span>@</span>{{ $submitProduct->user->username }}</a>
                                            </span>
                                        </td>
                                        <td>{{ getAmount($submitProduct->discount_percentage) }}%</td>
                                        <td>
                                            @php echo $submitProduct->statusBadge @endphp
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <button type="button"
                                                        class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-action="{{ route('admin.campaign.update.product.approve', $submitProduct->id) }}"
                                                        data-question="@lang('Are you sure to approve this product for the campaign?')" @if ($submitProduct->status != Status::CAMPAIGN_PRODUCT_PENDING) disabled @endif>
                                                    <i class="la la-eye"></i> @lang('Approve')
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-action="{{ route('admin.campaign.update.product.reject', $submitProduct->id) }}"
                                                        data-question="@lang('Are you sure to reject this product for the campaign?')" @if ($submitProduct->status != Status::CAMPAIGN_PRODUCT_PENDING) disabled @endif>
                                                    <i class="la la-eye"></i> @lang('Reject')
                                                </button>
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
                @if ($submittedProducts->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($submittedProducts) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection
