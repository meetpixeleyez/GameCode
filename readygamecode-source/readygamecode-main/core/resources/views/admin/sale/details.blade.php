@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-5 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{ route('product.details', $product->product->slug) }}">
                                <h5 class="m-0">{{ __($product->product->title) }}</h5>
                                <div class="image-upload mt-3">
                                    <img src="{{ getImage(getFilePath('productPreview') . '/' . productFilePath(@$product->product, 'preview_image')) }}"
                                        class="rounded">
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('License Type')</span>
                                    @if ($product->license == Status::PERSONAL_LICENSE)
                                        <span class="text-secondary">@lang('Personal License')</span>
                                    @else
                                        <span class="text-secondary">@lang('Commercial License')</span>
                                    @endif
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Purchase Code')</span>
                                    <span class="text-secondary">{{ $product->purchase_code }}</span>
                                </li>
                                @if (!$product->is_free)
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                        <span class="fw-bold">@lang('Price')</span>
                                        <span>{{ showAmount($product->product_price) }}</span>
                                    </li>
                                @else
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                        <span class="fw-bold">@lang('Price')</span>
                                        <span class="badge badge--success">@lang('Free')</span>
                                    </li>
                                @endif
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Author')</span>
                                    <span>
                                        <div class="user d-flex">
                                            <div class="ms-2 text-end">
                                                <span>{{ __(@$product->product->author->fullname) }}</span>
                                                <br>
                                                <span class="small d-block">
                                                    <a href="{{ route('admin.users.detail', $product->product->author->id) }}">{{ @$product->product->author->username }}</a>
                                                </span>
                                            </div>
                                        </div>
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Buyer')</span>
                                    <span>
                                        <div class="user d-flex">
                                            <div class="ms-2 text-end">
                                                <span>{{ __(@$product->buyer->fullname) }}</span>
                                                <br>
                                                <span class="small d-block">
                                                    <a
                                                        href="{{ route('admin.users.detail', $product->buyer->id) }}">{{ @$product->buyer->username }}</a>
                                                </span>
                                            </div>
                                        </div>
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Author Fee')</span>
                                    <span>{{ showAmount($product->seller_fee) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Buyer Fee')</span>
                                    <span>{{ showAmount($product->buyer_fee) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Refunded')</span>
                                    @php echo $product->refundedBadge; @endphp
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Demo Link')</span>
                                    <a href="{{ $product->product->demo_url }}" target="_blank">
                                        {{ @$product->product->demo_url }}
                                    </a>
                                </li>
                                @foreach ($product->product->attribute_info as $info)
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                        <span class="fw-bold">{{ __($info->name) }}</span>
                                        @if (is_array($info->value))
                                            <div>
                                                @foreach ($info->value as $val)
                                                    <span>{{ __($val) }}</span>
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            </div>
                                        @else
                                            <span>{{ __(@$info->value) }}</span>
                                        @endif
                                    </li>
                                @endforeach
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Tags')</span>
                                    <div>
                                        @forelse ($product->product->tags ?? [] as $tag)
                                            <span class="badge badge--primary mb-2">{{ __($tag) }}</span>
                                        @empty
                                            <span class="text-secondary">@lang('No Tags')</span>
                                        @endforelse
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Sale Date')</span>
                                    <div>
                                        <span>{{ showDateTime($product->created_at) }}</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
