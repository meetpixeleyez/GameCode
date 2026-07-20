@extends($activeTemplate . 'layouts.frontend')

@section('meta_title', gs()->siteName(__($pageTitle)))
@section('meta_description', str()->limit(strip_tags(@$seoContents->description ?? 'View our policy information for secure, reliable digital product purchases and downloads.'), 155))
@section('meta_image', $seoImage ?? asset('assets/images/default.png'))

@section('content')
    <section class="py-60">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h4>{{ __($pageTitle) }}</h4>
                    <hr>
                    @php
                        echo $policy->data_values->details;
                    @endphp
                </div>
            </div>
        </div>
    </section>
@endsection
