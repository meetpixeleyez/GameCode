@extends($activeTemplate . 'layouts.frontend')

@section('meta_title', gs()->siteName(@$seoContents->social_title ?? __('Home')))
@section('meta_description', @$seoContents->description ?? 'Discover premium Unity game source codes and game templates for Android & iOS. Browse top-rated projects, templates, and mobile game source code downloads.')
@section('meta_keywords', 'Unity game source code, game template, mobile game source code, game assets, Unity template')
@section('meta_image', $seoImage ?? asset('assets/images/default.png'))

@section('content')

    @include($activeTemplate . 'sections.banner')

    @if (@$sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif

    @include($activeTemplate . 'user.product.add_to_collection')
@endsection
