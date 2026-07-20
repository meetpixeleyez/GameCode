@php
    $featureAuthorSection = getContent('featured_product.content', true);

    $featuredAuthor = \App\Models\User::where('is_author_featured', Status::YES)
        ->where('status', Status::USER_ACTIVE)
        ->with([
            'products' => function ($query) {
                $query->where('status', Status::YES)->orderByDesc('total_sold')->limit(4);
            },
        ])
        ->first();
@endphp

@if ($featuredAuthor)
    <section class="featured-theme py-60">
        <div class="container">
            <div class="row gy-4">
                <div class="col-xxl-6 col-lg-5 pe-xl-5">
                    <div class="feature-box flex-center">
                        <div class="feature-box__content">
                            <h4 class="feature-box__title">@lang('Featured Author')</h4>
                            <h6 class="feature-box__desc">{{ __(@$featuredAuthor->fullname) }}</h6>
                            <p class="feature-box__title">@lang('Member Since ' . showDateTime($featuredAuthor->created_at, 'F Y'))</p>
                            <a href="{{ route('user.portfolio', $featuredAuthor->username) }}"
                               class="btn btn-outline--base">@lang('View Portfolio')</a>
                            <img src="{{ frontendImage('featured_product', @$featureAuthorSection->data_values->top_image, '220x170') }}"
                                 alt="@lang('Featured Image')" class="feature-box__water-img one">
                            <img src="{{ frontendImage('featured_product', @$featureAuthorSection->data_values->bottom_image, '250x280') }}"
                                 alt="@lang('Featured Image')" class="feature-box__water-img two">
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6 col-lg-7">
                    <div class="row gy-4">
                        @foreach ($featuredAuthor->products as $product)
                            <div class="col-sm-6 col-xsm-6">
                                <x-product :product="$product" />
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @php
            echo getAds('728x90');
        @endphp
    </section>
@endif
