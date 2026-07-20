@extends($activeTemplate . 'layouts.frontend')

@section('meta_title', gs()->siteName(__('Free Products')))
@section('meta_description', 'Find free Unity and mobile game templates, source code downloads, and starter kits for game developers. Browse no-cost projects and free downloadable app source code.')
@section('meta_keywords', 'free game source code, free Unity templates, free mobile game code, free app source code')
@section('meta_image', asset('assets/images/default.png'))

@section('content')
    @php
        $freeProduct = getContent('free_product.content', true);
    @endphp
    <section class="latest-template pt-60 pb-120">
        <div class="container">
            <div class="section-heading d-flex">
                <div class="col-md-9 text-start">
                    <h5 class="section-heading__title">{{ __(@$freeProduct->data_values->title) }}</h5>
                </div>
                <div class="col-md-3">
                    <x-search-form inputClass="form--control form--control--sm search" btn="btn--base btn--sm" />
                </div>
            </div>
            <ul class="nav custom--tab nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-all-items-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-all-items" type="button" role="tab" aria-controls="pills-all-items"
                        aria-selected="true">@lang('All Items')</button>
                </li>
                @foreach ($categories as $category)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-{{ $category->id }}-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-{{ $category->id }}" type="button" role="tab"
                            aria-controls="pills-{{ $category->id }}"
                            aria-selected="false">{{ __($category->name) }}</button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-all-items" role="tabpanel"
                    aria-labelledby="pills-all-items-tab" tabindex="0">
                    <div class="row gy-4">
                        @foreach ($products as $product)
                            <div class="col-lg-3 col-sm-6 col-xsm-6">
                                <x-product :product="$product" />
                            </div>
                        @endforeach
                    </div>
                    @if ($products->hasPages())
                        <div class="pt-30">
                            {{ paginateLinks($products) }}
                        </div>
                    @endif
                </div>

                @foreach ($categories as $category)
                    <div class="tab-pane fade" id="pills-{{ $category->id }}" role="tabpanel"
                        aria-labelledby="pills-{{ $category->id }}-tab" tabindex="0">
                        <div class="row gy-4">
                            @forelse ($category->products->take(8) as $product)
                                <div class="col-lg-3 col-sm-6 col-xsm-6">
                                    <x-product :product="$product" />
                                </div>
                            @empty
                                <x-empty-list title="No product found" />
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {
                    "@type": "ListItem",
                    "position": 1,
                    "name": "Home",
                    "item": "{{ route('home') }}"
                },
                {
                    "@type": "ListItem",
                    "position": 2,
                    "name": "Free Products",
                    "item": "{{ route('free.products') }}"
                }
            ]
        }
    </script>
@endpush

@push('script')
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "ItemList",
            "name": "Free Products",
            "description": "Free Unity game source codes and mobile app templates available for download.",
            "itemListElement": [
                @foreach($products as $index => $product)
                    {
                        "@type": "ListItem",
                        "position": {{ $index + 1 }},
                        "url": "{{ route('product.details', $product->slug) }}",
                        "name": "{{ e($product->title) }}"
                    }@if(!$loop->last),@endif
                @endforeach
            ]
        }
    </script>
@endpush
