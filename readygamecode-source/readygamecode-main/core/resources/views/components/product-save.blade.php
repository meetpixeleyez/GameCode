@if (@auth()->user()->is_author && $product->my_product)
    <a class="collection-list__button collection-btn product-edit-btn"
       href="{{ route('user.product.edit', $product->slug) }}">
        <i class="la la-edit"></i>
    </a>
@endif
@if (gs('comment_disable'))
    @if (@auth()->user()->is_author && $product->my_product)
        <a href="{{ route('user.product.commenting', $product->slug) }}"
           class="collection-list__button comment-toggle-btn {{ $product->comment_disable ? 'enable-comment' : 'disable-comment' }}"
           data-bs-toggle="tooltip" data-bs-placement="top"
           data-bs-title="@lang($product->comment_disable ? 'Enable Comment' : 'Disable Comment')">
            @if ($product->comment_disable)
                <i class="las la-comment-slash"></i>
            @else
                <i class="las la-comment"></i>
            @endif
        </a>
    @endif
@endif
<a data-product-id="{{ $product->id }}" data-product_title="{{ __($product->title) }}"
   href="{{ auth()->user() ? '' : route('user.login') }}"
   class="collection-list__button collection-btn @auth add-collection-btn @endauth" data-bs-toggle="tooltip"
   data-bs-placement="top" data-bs-title="@lang('Add to Collection')">
    <i class="icon-Add-to-collection"></i>
</a>
<a href="{{ auth()->user() ? '#' : route('user.login') }}"
   class="collection-list__button wishlist-btn @auth toggle-fav-button @endauth {{ isFavorite($product->id) ? 'wishlisted' : '' }}"
   data-product-id="{{ $product->id }}" data-bs-toggle="tooltip" data-bs-placement="top"
   data-bs-title="@lang('Toggle Favorite')" data-route="{{ route('user.author.favorites.toggle') }}">
    <i class="icon-Favaret"></i>
</a>
