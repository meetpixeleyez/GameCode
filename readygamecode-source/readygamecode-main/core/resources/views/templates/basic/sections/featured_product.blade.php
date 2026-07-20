@php
    $featureProductSection = getContent('featured_product.content', true);
    $featuredProducts = \App\Models\Product::approved()
        ->allActive()
        ->featured()
        ->with(['author', 'users'])
        ->limit(16)
        ->get();
@endphp

<section class="featured-theme py-60 overflow-hidden">
    <div class="container">
        <div class="section-heading style-left flex-between gap-3 mb-4">
            <div class="section-heading__inner">
                <h4 class="section-heading__title">{{ __($featureProductSection->data_values->heading ?? 'Featured Products') }}</h4>
            </div>
            <a href="{{ route('products') }}?sort_by=featured_product" class="btn btn-outline--base btn--sm">
                @lang('View All Items')
            </a>
        </div>

        <div class="row g-4">
            @foreach ($featuredProducts as $product)
                <div class="col-lg-3 col-md-4 col-sm-6 col-12"> {{-- 4 items per row (lg), 3 (md), 2 (sm) --}}
                    <x-product :product="$product" />
                </div>
            @endforeach
        </div>
    </div>
</section>

<!--<section class="featured-theme py-60">-->
<!--    <div class="container">-->
<!--        <div class="row gy-4">-->
<!--            <div class="col-xxl-6 col-lg-5 pe-xl-5">-->
<!--                <div class="feature-box flex-center">-->
<!--                    <div class="feature-box__content">-->
<!--                        <h4 class="feature-box__title">{{ __(@$featureProductSection->data_values->title) }}</h4>-->
                        <!--<p class="feature-box__desc">{{ __(@$featureProductSection->data_values->subtitle) }}</p>-->
<!--                        <div class="feature-box__button">-->
<!--                            <a href="{{ route('products') }}" class="btn btn-outline--base">@lang('View All Items')</a>-->
<!--                        </div>-->
<!--                        <img src="{{ frontendImage('featured_product', @$featureProductSection->data_values->top_image, '220x170') }}"-->
<!--                            alt="@lang('Featured Image')" class="feature-box__water-img one">-->
<!--                        <img src="{{ frontendImage('featured_product', @$featureProductSection->data_values->bottom_image, '250x280') }}"-->
<!--                            alt="@lang('Featured Image')" class="feature-box__water-img two">-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="col-xxl-6 col-lg-7">-->
<!--                <div class="row gy-4">-->
<!--                    @foreach ($featuredProducts as $product)-->
<!--                        <div class="col-sm-6 col-xsm-6">-->
<!--                            <x-product :product="$product" />-->
<!--                        </div>-->
<!--                    @endforeach-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--    @php-->
<!--        echo getAds('728x90');-->
<!--    @endphp-->
<!--</section>-->
