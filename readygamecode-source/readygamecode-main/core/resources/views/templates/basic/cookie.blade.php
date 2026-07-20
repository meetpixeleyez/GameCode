@extends($activeTemplate . 'layouts.frontend')

@section('meta_title', gs()->siteName(__($pageTitle)))
@section('meta_description', str()->limit(strip_tags($cookie->data_values->description ?? 'Read our cookie policy to learn how we protect your privacy and improve your browsing experience.'), 155))
@section('meta_keywords', 'cookie policy, privacy, data protection, user consent')
@section('meta_image', asset('assets/images/default.png'))

@section('content')
    <section class="py-60">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h4>{{ __($pageTitle) }}</h4>
                    <hr>
                    @php
                        echo $cookie->data_values->description;
                    @endphp
                </div>
            </div>
        </div>
    </section>
@endsection
