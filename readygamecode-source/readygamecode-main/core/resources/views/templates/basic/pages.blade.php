@extends($activeTemplate.'layouts.frontend')

@section('meta_title', gs()->siteName(__($pageTitle)))
@section('meta_description', str()->limit(strip_tags(@$seoContents->description ?? 'Explore our game source code pages and developer resources.'), 155))
@section('meta_image', $seoImage ?? asset('assets/images/default.png'))

@section('content')


    @if($sections != null)
        @foreach(json_decode($sections) as $sec)
            @include($activeTemplate.'sections.'.$sec)
        @endforeach
    @endif
@endsection
