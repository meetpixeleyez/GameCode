@php
    $bestSelling = getContent('marketplace.content', true);
    $bestSellingProducts = \App\Models\Product::approved()
        ->with('author')
        ->orderByDesc('total_sold')
        ->limit(16)
        ->get();
@endphp

<section class="browse-best-selling pb-20 overflow-hidden">
    <div class="container">
        <div class="section-heading style-left flex-between gap-3 mb-4">
            <div class="section-heading__inner">
                <h4 class="section-heading__title">Popular Items</h4>
            </div>
            <a href="{{ route('products') }}?sort_by=best_selling" class="btn btn-outline--base btn--sm">
                @lang('View All Items')
            </a>
        </div>

        <div class="row g-4">
            @foreach ($bestSellingProducts as $product)
                <div class="col-lg-3 col-md-4 col-sm-6 col-12"> {{-- 4 items per row (lg), 3 (md), 2 (sm) --}}
                    <x-product :product="$product" />
                </div>
            @endforeach
        </div>
    </div>
</section>




<!--<section class="browse-best-selling py-120 overflow-hidden">-->
<!--    <div class="container">-->
<!--        <div class="section-heading style-left flex-between gap-3">-->
<!--            <div class="section-heading__inner">-->
<!--                <h4 class="section-heading__title">{{ __(@$bestSelling->data_values->title) }}</h4>-->
<!--            </div>-->
<!--            <a href="{{ route('products') }}?sort_by=best_selling" class="btn btn-outline--base btn--sm">@lang('View All Items')</a>-->
<!--        </div>-->
<!--        <div class="browse-best-selling-slider">-->
<!--            @foreach ($bestSellingProducts as $product)-->
<!--                <x-product :product="$product" />-->
<!--            @endforeach-->
<!--        </div>-->
<!--    </div>-->
<!--</section>-->
