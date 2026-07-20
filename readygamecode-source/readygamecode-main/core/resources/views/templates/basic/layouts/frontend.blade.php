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
    <link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="{{ asset('assets/global/css/bootstrap.min.css') }}" as="style">
    <link rel="preload" href="{{ asset('assets/global/css/all.min.css') }}" as="style">
    <link rel="preload" href="{{ asset($activeTemplateTrue . 'css/main.css') }}" as="style">
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

    <link rel="stylesheet"
        href="{{ asset($activeTemplateTrue . 'css/color.php') }}?color={{ gs('base_color') }}&secondColor={{ gs('secondary_color') }}" />
        
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

    @stack('fbComment')

    <!-- <div class="preloader">
       <div class="loader-p"></div>
    </div> -->
    <div class="body-overlay"></div>
    <div class="sidebar-overlay"></div>
    <a class="scroll-top"><i class="fas fa-angle-double-up"></i></a>

    @php
        $isHeaderFooterHide =
            request()->routeIs('maintenance') || (request()->routeIs('user.register') && !gs('registration'));
    @endphp

    @if (!$isHeaderFooterHide)
        @include($activeTemplate . 'partials.header')
    @endif

    @yield('content')

    @if (!$isHeaderFooterHide)
        @include($activeTemplate . 'partials.footer')

        @php
            $cookie = App\Models\Frontend::where('data_keys', 'cookie.data')->first();
        @endphp
        @if ($cookie->data_values->status == Status::ENABLE && !\Cookie::get('gdpr_cookie'))
            <div class="cookies-card text-center hide">
                <div class="cookies-card__icon bg--base">
                    <i class="las la-cookie-bite"></i>
                </div>
                <p class="mt-4 cookies-card__content">{{ __($cookie->data_values->short_desc) }} <a
                        href="{{ route('cookie.policy') }}" target="_blank">@lang('learn more')</a></p>
                <div class="cookies-card__btn mt-4">
                    <a href="javascript:void(0)" class="btn btn--base w-100 policy">@lang('Allow')</a>
                </div>
            </div>
        @endif
    @endif

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

            $('.policy').on('click', function() {
                $.get('{{ route('cookie.accept') }}', function(response) {
                    $('.cookies-card').addClass('d-none');
                });
            });

            setTimeout(function() {
                $('.cookies-card').removeClass('hide')
            }, 2000);

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

            $('.select2').each(function(index, element) {
                $(element).select2();
            });

            $(document).on('click', '.advertisement-click', function(e) {
                let advertisementId = $(this).data('id');

                $.ajax({
                    url: "{{ route('advertisement.click', '') }}/" + advertisementId,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        console.log(response.success);
                    },
                    error: function(error) {
                        console.error('Error:', error.responseJSON.error);
                    }
                });
            });


        })(jQuery);
    </script>

    @stack('script')

    <!-- paras live chat -->
    
    <!--Start of Tawk.to Script-->
        <script type="text/javascript">
            var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
            (function(){
            var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
            s1.async=true;
            s1.src='https://embed.tawk.to/6846a184501b8d1909142ba1/1it9uds3l';
            s1.charset='UTF-8';
            s1.setAttribute('crossorigin','*');
            s0.parentNode.insertBefore(s1,s0);
            })();
        </script>
    <!--End of Tawk.to Script-->
    
    <!-- Start Social Media Code -->
    
    <style>
        a{
          text-decoration:none;
        }
        .floating_btn {
          position: fixed;
          bottom: 30px;
          left: 30px;
          width: 100px;
          height: 100px;
          display: flex;
          flex-direction: column;
          align-items:center;
          justify-content:center;
          z-index: 1000;
        }
        
        @keyframes pulsing {
          to {
            box-shadow: 0 0 0 30px rgba(232, 76, 61, 0);
          }
        }
        
        .contact_icon {
          background-color: #00d34f;
          color: #fff;
          width: 60px;
          height: 60px;
          font-size:35px;
          border-radius: 50px;
          text-align: center;
          box-shadow: 2px 2px 3px #999;
          display: flex;
          align-items: center;
          justify-content: center;
          transform: translatey(0px);
          animation: pulse 1.5s infinite;
          box-shadow: 0 0 0 0 #42db87;
          -webkit-animation: pulsing 1.25s infinite cubic-bezier(0.66, 0, 0, 1);
          -moz-animation: pulsing 1.25s infinite cubic-bezier(0.66, 0, 0, 1);
          -ms-animation: pulsing 1.25s infinite cubic-bezier(0.66, 0, 0, 1);
          animation: pulsing 1.25s infinite cubic-bezier(0.66, 0, 0, 1);
          font-weight: normal;
          font-family: sans-serif;
          text-decoration: none !important;
          transition: all 300ms ease-in-out;
        }
        
        
        .text_icon {
          margin-top: 8px;
          color: #707070;
          font-size: 13px;
        }
    </style>
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <div class="floating_btn">
        <a target="_blank" href="https://wa.me/919408212310">
          <div class="contact_icon">
            <i class="fa fa-whatsapp my-float"></i>
          </div>
        </a>
        <p class="text_icon">Talk to us?</p>
    </div>
    
    <!-- End Social Media Code -->

</body>

</html>
