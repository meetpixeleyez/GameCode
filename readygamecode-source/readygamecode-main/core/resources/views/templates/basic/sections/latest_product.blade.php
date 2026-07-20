@php
    $latestProductContent = getContent('latest_product.content', true);
    $categories = App\Models\Category::active()
        ->with([
            'products' => function ($query) {
                $query->approved()->allActive()->where('is_free', Status::DISABLE)->orderBy('id','desc');
            },
            'products.author',
            'products.users',
        ])
        ->get();

    $latestProductsQuery = App\Models\Product::with('author')->approved()->allActive()->where('is_free', Status::DISABLE);
    $latestProductCount  = (clone $latestProductsQuery)->count();
    $latestProducts      = $latestProductsQuery->latest()->limit(16)->get();
    
@endphp

<section class="latest-template py-60 overflow-hidden">
    <div class="container">
        <div class="section-heading style-left flex-between gap-3 mb-4">
            <div class="section-heading__inner">
                <h4 class="section-heading__title">{{ __(@$latestProductContent->data_values->title) }}</h4>
            </div>
            <a href="{{ route('products') }}?sort_by=new_item" class="btn btn-outline--base btn--sm">
                @lang('View All Items')
            </a>
        </div>
        
        <div class="row g-4">
            @foreach ($latestProducts as $product)
                <div class="col-lg-3 col-md-4 col-sm-6 col-12"> {{-- 4 items per row (lg), 3 (md), 2 (sm) --}}
                    <x-product :product="$product" />
                </div>
            @endforeach
        </div>

    </div>
</section>

<!--<section class="latest-template pt-60">-->
<!--    <div class="container">-->
<!--        <div class="section-heading">-->
<!--            <h4 class="section-heading__title">{{ __(@$latestProductContent->data_values->title) }}</h4>-->
            <!--<p class="section-heading__desc">{{ __(@$latestProductContent->data_values->subtitle) }}</p>-->
<!--        </div>-->

<!--        <ul class="nav custom--tab nav-pills mb-3" id="pills-tab" role="tablist">-->
<!--            <li class="nav-item" role="presentation">-->
<!--                <button class="nav-link active" id="pills-all-items-tab" data-bs-toggle="pill" data-bs-target="#pills-all-items" type="button"-->
<!--                    role="tab" aria-controls="pills-all-items" aria-selected="true">@lang('All Items')</button>-->
<!--            </li>-->
<!--            @foreach ($categories as $category)-->
<!--                <li class="nav-item" role="presentation">-->
<!--                    <button class="nav-link" id="pills-{{ $category->id }}-tab" data-bs-toggle="pill" data-bs-target="#pills-{{ $category->id }}"-->
<!--                        type="button" role="tab" aria-controls="pills-{{ $category->id }}"-->
<!--                        aria-selected="false">{{ __($category->name) }}</button>-->
<!--                </li>-->
<!--            @endforeach-->
<!--        </ul>-->

<!--        <div class="tab-content" id="pills-tabContent">-->
<!--            <div class="tab-pane fade show active" id="pills-all-items" role="tabpanel" aria-labelledby="pills-all-items-tab" tabindex="0">-->
<!--                <div class="row gy-4">-->
<!--                    @foreach ($latestProducts as $product)-->
<!--                        <div class="col-lg-3 col-sm-6 col-xsm-6">-->
<!--                            <x-product :product="$product" />-->
<!--                        </div>-->
<!--                    @endforeach-->
<!--                </div>-->
<!--            </div>-->

<!--            @foreach ($categories as $category)-->
<!--                <div class="tab-pane fade" id="pills-{{ $category->id }}" role="tabpanel" aria-labelledby="pills-{{ $category->id }}-tab"-->
<!--                    tabindex="0">-->
<!--                    <div class="row gy-4">-->
<!--                        @forelse ($category->products->take(8) as $product)-->
<!--                            <div class="col-lg-3 col-sm-6 col-xsm-6">-->
<!--                                <x-product :product="$product" />-->
<!--                            </div>-->
<!--                        @empty-->
<!--                        <x-empty-list title="No product found" />-->
<!--                        @endforelse-->
<!--                    </div>-->
<!--                </div>-->
<!--            @endforeach-->
<!--        </div>-->
<!--        @if ($latestProductCount > 8)-->
<!--        <div class="text-center view-all-btn">-->
<!--            <a href="{{ route('products') }}?sort_by=new_item" class="btn btn--sm btn-outline--base">@lang('View All Items')</a>-->
<!--        </div>-->
<!--        @endif-->
<!--    </div>-->
<!--    @php-->
<!--        echo getAds('728x90');-->
<!--    @endphp-->
<!--</section>-->
