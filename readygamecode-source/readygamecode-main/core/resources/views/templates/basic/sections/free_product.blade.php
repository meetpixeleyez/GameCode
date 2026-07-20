@php
    $freeProduct = getContent('free_product.content', true);
    $freeProducts = \App\Models\Product::approved()
        ->allActive()
        ->with('author')
        ->withCount(['orderItems as total_sold'])
        ->groupBy('products.id')
        ->orderBy('total_sold', 'desc')
        ->where('is_free', Status::ENABLE)
        ->limit(10)
        ->get();
@endphp
@if (!$freeProducts->isEmpty())
    <section class="browse-best-selling py-120 overflow-hidden">
        <div class="container">
            <div class="section-heading style-left flex-between gap-3">
                <div class="section-heading__inner">
                    <h4 class="section-heading__title">{{ __(@$freeProduct->data_values->title) }}</h4>
                </div>
                @if ($freeProducts->count() > 4)
                    <a href="{{ route('free.products') }}"
                        class="btn btn-outline--base btn--sm">@lang('View All Items')</a>
                @endif
            </div>
            <div class="browse-best-selling-slider">
                @foreach ($freeProducts as $product)
                    <x-product :product="$product" />
                @endforeach
            </div>
        </div>
    </section>
@endif
