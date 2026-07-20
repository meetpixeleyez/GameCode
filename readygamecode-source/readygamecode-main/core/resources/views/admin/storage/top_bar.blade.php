<ul class="nav nav-tabs mb-4 topTap breadcrumb-nav" role="tablist">
    <button class="breadcrumb-nav-close"><i class="las la-times"></i></button>
    <li class="nav-item {{ menuActive('admin.storage.ftp') }}" role="presentation">
        <a href="{{ route('admin.storage.ftp') }}" class="nav-link text-dark" type="button">
            <i class="las la-server"></i> @lang('FTP')
        </a>
    </li>
    <li class="nav-item {{ menuActive('admin.storage.wasabi') }}" role="presentation">
        <a href="{{ route('admin.storage.wasabi') }}" class="nav-link text-dark" type="button">
            <i class="las la-water"></i> @lang('Wasabi')
        </a>
    </li>
    <li class="nav-item {{ menuActive('admin.storage.digital.ocean') }}" role="presentation">
        <a href="{{ route('admin.storage.digital.ocean') }}" class="nav-link text-dark" type="button">
            <i class="las la-cloud"></i> @lang('Digital Ocean')
        </a>
    </li>
    <li class="nav-item {{ menuActive('admin.storage.backblaze') }}" role="presentation">
        <a href="{{ route('admin.storage.backblaze') }}" class="nav-link text-dark" type="button">
            <i class="las la-database"></i> @lang('Backblaze')
        </a>
    </li>
</ul>
