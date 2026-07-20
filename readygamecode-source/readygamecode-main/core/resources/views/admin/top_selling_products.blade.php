<ul class="list-group list-group-flush">
    @forelse ($topSellingItems as $product)
        <li class="list-group-item d-flex flex-wrap align-items-center px-2">
            <a href="{{ route('admin.product.details', $product->slug) }}" class="top-sell-item__thumb"
               title="{{ __($product->title) }}">
                 <img class="top-sell-item__thumb-img"
                     src="{{ getImage(getFilePath('productInlinePreview') . productFilePath($product, 'inline_preview_image'), getFileSize('productInlinePreview')) }}"
                     alt="{{ __($product->title) }}" loading="lazy">
            </a>
            <div class="top-sell-item__content">
                <a href="{{ route('admin.product.details', $product->slug) }}" class="top-sell-item__title d-block">
                    {{ __(Str::limit($product->title, 50, '...')) }}
                </a>
                <span class="top-sell-item__desc d-block">@lang('Sales')
                    ({{ $product->total_sold }})
                    {{ $product->id }}
                </span>
            </div>
        </li>
    @empty
        <li class="list-group-item d-flex flex-wrap align-items-center px-2">
            <span class="top-sell-item__title d-block">@lang('No data found')</span>
        </li>
    @endforelse
</ul>
