@php
    $seo = $seo ?? null;
    $seoContents = $seoContents ?? null;
    $metaImage = $seoImage ?? ($seo ? getImage(getFilePath('seo') . '/' . $seo->image) : asset('assets/images/default.png'));
@endphp

    <meta name="title" content="@yield('meta_title', gs()->siteName(__($pageTitle)))">
    <link rel="shortcut icon" href="{{ siteFavicon() }}" type="image/png">

    {{-- <!-- Apple Stuff --> --}}
    <link rel="apple-touch-icon" href="{{ siteFavicon() }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="{{ gs()->siteName($pageTitle) }}">
    {{-- <!-- Google / Search Engine Tags --> --}}
    <meta itemprop="name" content="@yield('meta_title', gs()->siteName($pageTitle))">
    <meta itemprop="description" content="@yield('meta_description', @$seoContents->description ?? ($seo->description ?? ''))">
    <meta itemprop="image" content="@yield('meta_image', $metaImage)">
    {{-- <!-- Facebook Meta Tags --> --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ gs()->siteName($pageTitle) }}">
    <meta property="og:title" content="@yield('meta_title', @$seoContents->social_title ?? ($seo->social_title ?? gs()->siteName($pageTitle)))">
    <meta property="og:description" content="@yield('meta_description', @$seoContents->social_description ?? ($seo->social_description ?? ''))">
    <meta property="og:image" content="@yield('meta_image', $metaImage)">
    @php $socialImageSize = explode('x', getFileSize('seo')) @endphp
    <meta property="og:image:width" content="{{ $socialImageSize[0] }}">
    <meta property="og:image:height" content="{{ $socialImageSize[1] }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <link rel="alternate" href="{{ url()->current() }}" hreflang="{{ str_replace('_', '-', app()->getLocale()) }}">
    {{-- <!-- Twitter Meta Tags --> --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('meta_title', @$seoContents->social_title ?? ($seo->social_title ?? gs()->siteName($pageTitle)))">
    <meta name="twitter:description" content="@yield('meta_description', @$seoContents->social_description ?? ($seo->social_description ?? ''))">
    <meta name="twitter:image" content="@yield('meta_image', $metaImage)">
    <meta name="twitter:url" content="{{ url()->current() }}">
