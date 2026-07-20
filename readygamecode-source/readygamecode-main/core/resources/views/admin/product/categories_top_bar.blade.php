<ul class="nav nav-tabs mb-4 topTap breadcrumb-nav" role="tablist">
    <button class="breadcrumb-nav-close"><i class="las la-times"></i></button>
    <li class="nav-item {{ menuActive('admin.category.index') }}" role="presentation">
        <a href="{{ route('admin.category.index') }}" class="nav-link text-dark" type="button">
            <i class="las la-list-alt"></i> @lang('Categories')
        </a>
    </li>
    <li class="nav-item {{ menuActive('admin.subcategory.index') }}" role="presentation">
        <a href="{{ route('admin.subcategory.index') }}" class="nav-link text-dark" type="button">
            <i class="las la-list-ul"></i> @lang('Subcategories')
        </a>
    </li>
    <li class="nav-item {{ menuActive('admin.reviewcategory.index') }}" role="presentation">
        <a href="{{ route('admin.reviewcategory.index') }}" class="nav-link text-dark" type="button">
            <i class="las la-star-half-alt"></i> @lang('Review Categories')
        </a>
    </li>
</ul>
