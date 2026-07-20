<!doctype html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="@yield('meta_description', 'Buy Unity Game Source Codes - Android & iOS')">
    <meta name="keywords" content="@yield('meta_keywords', 'Buy Unity Game Source Code, Unity Game Source Code, Buy Unity Source Code, Unity Source Code, Unity 3d Game Source Code, Buy Unity 3d Game Source Code, Android Game Source Code, Unity Template')">
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">
    <meta name="p:domain_verify" content="8aee5cc16d3ec2cebfa47f292e52b24f"/>
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <meta property="og:image" content="@yield('meta_image', asset('images/default.jpg'))">
    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">
    <title>@yield('meta_title', gs()->siteName(__($pageTitle)))</title>
    @include('partials.seo')
    @include('partials.structured_data')
    @stack('structured_data')
    <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">

    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/icom-moon.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/main.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/template.css') }}">

    @stack('style-lib')

    @stack('style')

    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/color.php') }}?color={{ gs('base_color') }}&secondColor={{ gs('secondary_color') }}" />
    
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-LBSTHLG2QP"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', 'G-LBSTHLG2QP');
    </script>
    
</head>

@php echo loadExtension('google-analytics') @endphp

<body>

    <!-- <div class="preloader">
       <div class="loader-p"></div>
    </div> -->
    <div class="body-overlay"></div>
    <div class="sidebar-overlay"></div>
    <a class="scroll-top"><i class="fas fa-angle-double-up"></i></a>

    @include($activeTemplate . 'partials.header')

    @include($activeTemplate . 'user.profile.profile_banner')
    <div class="profile-page pt-60 pb-120">
        <div class="container">
            @yield('content')
        </div>
    </div>

    @include($activeTemplate . 'partials.footer')

    <script src="{{ asset('assets/global/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/slick.min.js') }}"></script>

    @stack('script-lib')

    <script src="{{ asset($activeTemplateTrue . 'js/main.js') }}"></script>


    @include('partials.notify')


    @if (gs('pn'))
        @include('partials.push_script')
    @endif

    @include('Template::partials.waveserfer')

    @stack('script')

    <script>
        (function($) {
            "use strict";
            $('.select2').select2();

            $('.language_switcher > .language_switcher__caption').on('click', function() {
                $(this).parent().toggleClass('open');
            });
            $(document).on('keyup', function(evt) {
                if ((evt.keyCode || evt.which) === 27) {
                    $('.language_switcher').removeClass('open');
                }
            });
            $(document).on('click', function(evt) {
                if ($(evt.target).closest(".language_switcher > .language_switcher__caption").length === 0) {
                    $('.language_switcher').removeClass('open');
                }
            });


            var inputElements = $('[type=text],[type=password],select,textarea');
            $.each(inputElements, function(index, element) {
                element = $(element);
                element.closest('.form-group').find('label').attr('for', element.attr('name'));
                element.attr('id', element.attr('name'))
            });

            $.each($('input, select, textarea'), function(i, element) {
                var elementType = $(element);
                if (elementType.attr('type') != 'checkbox') {
                    if (element.hasAttribute('required')) {
                        $(element).closest('.form-group').find('label').addClass('required');
                    }
                }
            });

            Array.from(document.querySelectorAll('table')).forEach(table => {
                let heading = table.querySelectorAll('thead tr th');
                Array.from(table.querySelectorAll('tbody tr')).forEach((row) => {
                    Array.from(row.querySelectorAll('td')).forEach((colum, i) => {
                        colum.setAttribute('data-label', heading[i].innerText)
                    });
                });
            });

            $(".toggle-fav-button").on("click", function(e) {
                e.preventDefault();
                const productId = $(this).data("product-id");
                const url = $(this).data("route");
                $(this).toggleClass("wishlisted");

                $.ajax({
                    url,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    method: "POST",
                    data: {
                        product_id: productId
                    },
                });
            });

            let disableSubmission = false;
            $('.disableSubmission').on('submit', function(e) {
                if (disableSubmission) {
                    e.preventDefault()
                } else {
                    disableSubmission = true;
                }
            });

            $('.select2').each(function(index,element){
                $(element).select2();
            });

        })(jQuery);
    </script>

</body>

</html>
