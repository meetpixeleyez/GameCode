@extends($activeTemplate . 'layouts.frontend')

@section('meta_title', gs()->siteName(@$seoContents->social_title ?? __('Products')))
@section('meta_description', @$seoContents->social_description ?? 'Browse the best premium Unity game source code templates, app source codes, and game kits. Filter by category, price, rating, and discover your next development project.')
@section('meta_keywords', 'Unity game source code, premium game templates, app source code, Unity templates, buy game source code')
@section('meta_image', asset('assets/images/default.png'))

@section('content')
    <section class="product pt-60 pb-60">
        <div class="container">
            @include($activeTemplate . 'user.product.products_top')
            <div class="product__inner">
                @include($activeTemplate . 'user.product.products_sidebar')
                <div class="product-body">
                    <div class="row gy-4 justify-content-center">
                        @forelse ($products as $index => $product)
                            <div class="col-xl-4 col-sm-6 col-xsm-6">
                                <x-product :product="$product" />
                            </div>
                            @if (($index + 1) % 3 == 0)
                                @php
                                    echo getAds('728x90');
                                @endphp
                            @endif
                        @empty
                            <div class="card custom--card">
                                <div class="card-body">
                                    <x-empty-list title="No Products found" />
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="pt-30">
                {{ paginateLinks($products) }}
            </div>
        </div>
    </section>
    @include($activeTemplate . 'user.product.add_to_collection')
@endsection



@push('style')
    <style>
        .select2-container:has(.select2-selection--single) {
            width: auto !important;
        }
    </style>
@endpush



@push('script')
    <script>
        (function($) {
            "use strict";

            function setLocalItem(key, value) {
                localStorage.setItem(key, value);
            }

            function toggleSidebar() {
                const productFilterBtn = localStorage.getItem('product_filter_btn');
                if (productFilterBtn == 'hidden') {
                    $('body').addClass('toggle-sidebar');
                } else {
                    $('body').removeClass('toggle-sidebar');
                }
                iconChange();
            }
            toggleSidebar();

            $('.filter-btn').on('click', function() {
                $(this).toggleClass('filter_visible');
                const productFilterBtn = localStorage.getItem('product_filter_btn');
                if (productFilterBtn == 'hidden') {
                    setLocalItem('product_filter_btn', 'visible');
                } else {
                    setLocalItem('product_filter_btn', 'hidden');
                }
                iconChange();
            });

            function iconChange() {
                if (window.innerWidth <= 991) {
                    $(".filter-btn").find(`i`).addClass(`icon-Filter`).removeClass(`fas fa-times`);
                } else {
                    const productFilterBtn = localStorage.getItem('product_filter_btn');
                    if (productFilterBtn == 'hidden') {
                        $(".filter-btn").find(`i`).addClass(`icon-Filter`).removeClass(`fas fa-times`);
                    } else {
                        $(".filter-btn").find(`i`).removeClass(`icon-Filter`).addClass(`fas fa-times`);
                    }
                }
            }

            const productViewType = localStorage.getItem('product_view_type') || 'grid-view';
            $('.view-buttons__btn.grid-view-btn').removeClass('text--base');
            if (productViewType == 'grid-view') {
                $('.view-buttons__btn.grid-view-btn').addClass('text--base');
            } else {
                $('.view-buttons__btn.list-view-btn').addClass('text--base');
            }

            $('.product-body').addClass(productViewType);
            $('.list-view-btn').on('click', function() {
                setLocalItem('product_view_type', 'list-view');
            });
            $('.grid-view-btn').on('click', function() {
                setLocalItem('product_view_type', 'grid-view');
            });

        })(jQuery);
    </script>

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "ItemList",
            "name": "Product Listings",
            "description": "Featured Unity game source code, templates, and mobile app products.",
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
                    "name": "Products",
                    "item": "{{ route('products') }}"
                }
            ]
        }
    </script>
@endpush


