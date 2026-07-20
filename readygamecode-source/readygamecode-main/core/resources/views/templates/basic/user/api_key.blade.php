@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center gy-3">
        <div class="col-xl-3 col-lg-4">
            <div class="common-sidebar__item api-sidebar-menu">
                <div class="common-sidebar__content">
                    <h6 class="title list-title">@lang('Get Started')</h6>
                    <ul class="api-sidebar-submenu" id="navbar-example3">
                        <li class="api-sidebar-submenu__item">
                            <a href="#overview" class="api-sidebar-submenu__link">@lang('Overview')</a>
                        </li>
                    </ul>
                </div>
                <div class="common-sidebar__content">
                    <h6 class="title list-title">@lang('Items')</h6>
                    <ul class="api-sidebar-submenu" id="navbar-example3">
                        <li class="api-sidebar-submenu__item">
                            <a href="#get-product" class="api-sidebar-submenu__link">@lang('Get All Products')</a>
                        </li>
                        <li class="api-sidebar-submenu__item">
                            <a href="#product-details" class="api-sidebar-submenu__link">@lang('Get A Product Details')</a>
                        </li>
                    </ul>
                </div>
                <div class="common-sidebar__content">
                    <h6 class="title list-title">@lang('Purchases')</h6>
                    <ul class="api-sidebar-submenu" id="navbar-example3">
                        <li class="api-sidebar-submenu__item">
                            <a href="#purchase-validation" class="api-sidebar-submenu__link">@lang('Purchase Validation')</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-9 col-lg-8">
            <div class="common-sidebar__item">
                <div class="common-sidebar__content" data-bs-spy="scroll" data-bs-target="#navbar-example3"
                     data-bs-smooth-scroll="true" tabindex="0">

                    <div class="api-docs-content mb-xl-5 mb-4" id="overview">
                        <h5 class="title mb-3">@lang('Overview')</h5>
                        <p class="desc">
                            @lang('Using the ') <span class="text--base fw-bold">{{ __(gs('site_name')) }}</span>
                            @lang('API is pretty simple. You can easily confirm product purchases by users with our straightforward integration. Our API is designed for seamless implementation into any web & mobile application, supporting both GET and POST requests while providing responses in JSON format. Remember, URLs are case-sensitive for accurate interaction.')
                        </p>
                        <h6 class="subtitle mb-2">@lang('API key')</h6>
                        <div class="api-content">
                            @if ($apiKey)
                                <p class="api-content__desc">
                                    @lang('Get your API key from the below. The API key is used to authenticate the request and determine whether the request is valid or not. If you want to regenerate the API key from the below sync icon.')
                                </p>
                                <div class="form-group mb-0">
                                    <div class="input-group">
                                        <button class="input-group-text  c--p confirmationBtn"
                                                data-question="@lang('Are you sure to regenerate your API key? Your old API key stops working here if you do it!')" data-bs-toggle="tooltip"
                                                title="@lang('Regenerate API Key')" data-action="{{ route('user.api.key.generate') }}">
                                            <i class="las la-sync-alt"></i>
                                        </button>
                                        <input type="text" class="form-control  copyText" readonly value="{{ @$apiKey->key }}">
                                        <button class="input-group-text c--p coptBtn">
                                            <i class="las la-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            @else
                                <p class="api-content__desc">
                                    @lang('The API key is used to authenticate the request and determine whether the request is valid or not. If you want to generate the API key from the below generate button.')
                                </p>
                                <div class="text-center py-4">
                                    <img src="{{ asset('assets/images/api_key.png') }}" class="mb-2">
                                    <p class="fs-14 mb-3">@lang("You don't have any API keys. Generate your API key by clicking below the Generate button")</p>
                                    <button class="btn btn--base confirmationBtn" type="button"
                                            data-question="@lang('Are you sure to generate your API key?')" data-action="{{ route('user.api.key.generate') }}">@lang('Generate API Key')
                                    </button>
                                </div>
                            @endif

                        </div>
                    </div>

                    <div class="api-docs-content mb-xl-5 mb-4" id="get-product">
                        <h5 class="title">@lang('Get All Products')</h5>
                        <p class="desc">@lang('Retrieves all products associated with the provided username')</p>
                        <div class="api-docs-endpoint-wrapper mb-3">
                            <h6 class="subtitle mb-2">@lang('Endpoint')</h6>
                            <div class="api-docs-endpoint rounded-2">
                                <span class="badge badge--primary">@lang('GET')</span>
                                <span class="text">{{ route('api.author.products.all') }}</span>
                                <button class="right-highlited__button clipboard-btn" data-clipboard-target="{{ route('api.author.products.all') }}">
                                    <i class="las la-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="api-docs-parameter mb-3">
                            <h6 class="subtitle mb-2">@lang('Parameters')</h6>
                            <ul>
                                <li><strong>@lang('username ') </strong>@lang('The username of the user to retrieve all products') <code>@lang('(Required)')</code>
                                </li>
                            </ul>
                        </div>
                        <div class="api-docs-response-wrapper">
                            <h6 class="subtitle mb-2">@lang('Responses')</h6>
                            <div class="api-docs-response-item mb-4">
                                <h6 class="subtitle two mb-2">@lang('Success Responses')</h6>
                                <pre class="rounded-3 py-0">
                                    <code class="language-php success-code">
{
    "remark": "author_products",
    "status": "success",
    "message": {
        "success": [
            "Author products fetched successfully"
        ]
    },
    "data": {
        "products": [
            {
                "id": 4,
                "user_id": 2,
                "assigned_to": 2,
                "category_id": 2,
                "sub_category_id": 3,
                "title": "CryptoCom - Flutter App",
                "slug": "cryptocom-crypto-based-ecommerce-shopping-platform-58311",
                "price": "100.00000000",
                "price_cl": "150.00000000",
                "thumbnail": "67b9b036383011740222518.png",
                "approved_by": 2,
                "demo_url": "https://script.viserlab.com/cryptocom/",
                "preview_video": "67c2c6439805e1740817987.mp4",
                "attribute_info": [],
                "preview_image": "67b9b0363e1241740222518.png",
                "inline_preview_image": "67b9b03658d991740222518.png",
                "total_sold": 3,
                "total_review": 0,
                "avg_rating": "0.00",
                "description": "<span style=\"color:rgb(84,84,84);font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;\">Looking for a crypto-based E-commerce system for your business, then you are in the right place. No need to pay any extra money, no hidden charges, no need to wait, no need to hire developers or designers. just purchase and install CryptoCom, your shopping e-commerce website will be ready within the next 5 minutes. Our system can handle unlimited users, orders, services, categories, digital items, traditional items, able to accept payment via over 5000 altcoins, bitcoin, doge, ripple, and ethereum. the complete ready-to-go solution, we also here to provide you best support, installation, and customization if you need it. hurry up, get your copy and start your E-commerce website. We Add GDPR popups, advertisements, and almost all services on it for better performance and acceptance.</span>",
                "changelog": null,
                "is_free": 0,
                "comment_disable": 0,
                "is_featured": 0,
                "tags": [
                    "crypto",
                    "flutter",
                    "app",
                    "codeplus"
                ],
                "status": 1,
                "file": "67b9b03636b891740222518.zip",
                "temp_file": null,
                "product_updated": 0,
                "published_at": "2025-02-22 11:09:27",
                "last_updated": null,
                "created_at": "2025-02-22T11:08:38.000000Z",
                "updated_at": "2025-02-26T10:17:30.000000Z"
            }
            {
                - - - - - - - - - - - - - - - - - - - -
            }
        ]
    }
}
                                    </code>
                                </pre>
                            </div>
                            <div class="api-docs-response-item">
                                <h6 class="subtitle two mb-2">@lang('Error Responses')</h6>
                                <pre class="rounded-3 py-0">
                                    <code class="language-php error-code">
{
    "remark": "validation_error",
    "status": "error",
    "message": {
        "error": [
            "The selected username is invalid."
        ]
    }
}
                                    </code>
                                </pre>
                            </div>
                        </div>
                    </div>

                    <div class="api-docs-content mb-xl-5 mb-4" id="product-details">
                        <h5 class="title">@lang('Get A Product Details')</h5>
                        <p class="desc">@lang('Retrieves a product details associated with the provided product id')</p>
                        <div class="api-docs-endpoint-wrapper mb-4">
                            <h6 class="subtitle mb-2">@lang('Endpoint')</h6>
                            <div class="api-docs-endpoint rounded-2">
                                <span class="badge badge--primary">@lang('GET')</span>
                                <span class="text">{{ route('api.author.products.detail') }}</span>
                                <button class="right-highlited__button  clipboard-btn" data-clipboard-target="{{ route('api.author.products.detail') }}">
                                    <i class="las la-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="api-docs-parameter mb-4">
                            <h6 class="subtitle mb-2">@lang('Parameters')</h6>
                            <ul>
                                <li><strong>@lang('product_id') </strong>@lang('The product id to get the specific product details') <code>(Required)</code></li>
                            </ul>
                        </div>
                        <div class="api-docs-response-wrapper mb-4">
                            <h6 class="subtitle mb-2">@lang('Responses')</h6>
                            <div class="api-docs-response-item mb-4">
                                <h6 class="subtitle two mb-2">@lang('Success Responses')</h6>
                                <pre class="rounded-3 py-0">
                                    <code class="language-php success-code">
{
    "remark": "product_detail",
    "status": "success",
    "message": {
        "success": [
            "Product fetched successfully"
        ]
    },
    "data": {
        "image_path": "assets/files/product",
        "thumbnail": "assets/files/product",
        "video_preview": "assets/files/product",
        "product": {
            "id": 1,
            "user_id": 1,
            "assigned_to": 2,
            "category_id": 1,
            "sub_category_id": 1,
            "title": "PromptLab",
            "slug": "promptlab-02741",
            "price": "30.00000000",
            "price_cl": "60.00000000",
            "thumbnail": "67b98e50782b61740213840.png",
            "approved_by": 2,
            "demo_url": "https://script.viserlab.com/promptlab/",
            "preview_video": "Hi-Tech Intro.mp4",
            "attribute_info": [],
            "preview_image": "67b98e507e0381740213840.png",
            "inline_preview_image": "67b98e50a303d1740213840.png",
            "total_sold": 1,
            "total_review": 0,
            "avg_rating": "0.00",
            "description": "<span style=\"background-color:rgb(250,250,250);color:rgb(84,84,84);font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;\">PromptLab Is an AI Prompt Marketplace application designed to enhance the management of your prompt business. It provides administrators with essential tools to oversee prompt submissions, user activities, and financial transactions. The application features flexible commission settings, a wide range of payment gateways, and support for multiple languages to reach a global audience.</span><br style=\"background-color:rgb(250,250,250);color:rgb(84,84,84);font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;\" /><br style=\"background-color:rgb(250,250,250);color:rgb(84,84,84);font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;\" /><span style=\"background-color:rgb(250,250,250);color:rgb(84,84,84);font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;\">Additionally, it offers detailed reporting and analytics to help you monitor performance, track sales, and understand user behavior. The intuitive admin panel allows for easy customization and efficient handling of marketplace operations. With our application, you can ensure smooth management, optimize revenue streams, and deliver a high-quality experience to both buyers and sellers. Choose our solution to effectively run and grow your prompt marketplace. It’s easily installable, and controllable through the admin panel, and comes with a responsive design, high security, interactive User interface. support plugins, LiveChat, Google ReCaptcha, analytics, automatic payment gateway, cards, currencies, and cryptos. we are also here to provide you with the best support, installation, and customization if you need it.</span>",
            "changelog": null,
            "is_free": 0,
            "comment_disable": 0,
            "is_featured": 1,
            "tags": [
                "promptlab",
                "codeplus",
                "viserlab",
                "php",
                "script"
            ],
            "status": 1,
            "file": "67b98e5076aed1740213840.zip",
            "temp_file": null,
            "product_updated": 0,
            "published_at": "2025-02-22 08:53:01",
            "last_updated": null,
            "created_at": "2025-02-22T08:44:02.000000Z",
            "updated_at": "2025-02-23T11:43:18.000000Z"
        }
    }
}
                                    </code>
                                </pre>
                            </div>
                            <div class="api-docs-response-item">
                                <h6 class="subtitle two mb-2">@lang('Error Responses')</h6>
                                <pre class="rounded-3 py-0">
                                    <code class="language-php error-code">
{
    "remark": "not_found",
    "status": "error",
    "message": {
        "error": [
            "Product not found"
        ]
    }
}
                                    </code>
                                </pre>
                            </div>
                        </div>
                    </div>

                    <div class="api-docs-content" id="purchase-validation">
                        <h5 class="title">@lang('Purchase Validation')</h5>
                        <p class="desc">@lang('Retrieves all items associated with the provided API key')</p>
                        <div class="api-docs-endpoint-wrapper mb-4">
                            <h6 class="subtitle mb-2">@lang('Endpoint')</h6>
                            <div class="api-docs-endpoint rounded-2">
                                <span class="badge badge--primary">@lang('POST')</span>
                                <span class="text">{{ route('api.purchase.code.verify') }}</span>
                                <button class="right-highlited__button  clipboard-btn" data-clipboard-target="{{ route('api.purchase.code.verify') }}">
                                    <i class="las la-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="api-docs-parameter mb-4">
                            <h6 class="subtitle mb-2">@lang('Header')</h6>
                            <ul>
                                <li class="mb-3"><strong>@lang('apikey') </strong>@lang('Your API key')
                                    <code>@lang('(Required)')</code>
                                </li>
                            </ul>
                            <h6 class="subtitle mb-2">@lang('Parameters')</h6>
                            <ul>
                                <li><strong>@lang('purchase_code') </strong>@lang('Provide a valid purchase code to verify') <code>@lang('(Required)')</code>
                                </li>
                            </ul>
                        </div>
                        <div class="api-docs-response-wrapper mb-4">
                            <h6 class="subtitle mb-2">@lang('Responses')</h6>
                            <div class="api-docs-response-item mb-4">
                                <h6 class="subtitle two mb-2">@lang('Success Responses')</h6>
                                <pre class="rounded-3 py-0">
                                    <code class="language-php success-code">
{
    "status": "success",
    "status_code": "200",
    "message": {
        "success": [
            "Purchase code matched"
        ]
    }
}
                                    </code>
                                </pre>
                            </div>
                            <div class="api-docs-response-item">
                                <h6 class="subtitle two mb-2">@lang('Error Responses')</h6>
                                <pre class="rounded-3 py-0">
                                    <code class="language-php error-code">
{
    "status": "error",
    "status_code": 422,
    "message": [
        "error" : [
            "The purchase code field is required.",
            "The selected purchase code is invalid.",
        ]
    ]
}
                                    </code>
                                </pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <x-confirmation-modal frontend="true" />
@endsection

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/vendor/highlight.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            $('.coptBtn').on('click', function(e) {
                var copyText = $(this).siblings('.copyText')[0];
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                document.execCommand("copy");
                copyText.blur();

                notify('success', "API Key Copied")
            });

            $('.clipboard-btn').on('click', function() {
                var textToCopy = $(this).attr('data-clipboard-target');
                var tempInput = $("<input>");
                $("body").append(tempInput);
                tempInput.val(textToCopy).select();
                document.execCommand("copy");
                tempInput.remove();

                notify('success', "Copied to clipboard: " + textToCopy);
            });
        })(jQuery)
    </script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/vendor/highlight.css') }}">
@endpush

@push('style')
    <style>
        .api-sidebar-menu {
            position: sticky;
            top: 60px;
        }


        .api-sidebar-submenu__link {
            color: #64748b;
            font-size: 14px;
            font-weight: 400;
        }

        .api-sidebar-submenu__link:hover,
        .api-sidebar-submenu__link:focus {
            color: #334155;
        }

        .api-sidebar-submenu__link.active {
            color: #64748b;
        }


        .code {
            position: relative;
            border-radius: 10px !important;
            overflow: hidden;
        }

        .table {
            border: 1px solid hsl(var(--black) / .1);
        }

        .api-sidebar-submenu__item:not(:last-child) {
            margin-bottom: 5px;
        }

        .common-sidebar__content:not(:last-child) {
            margin-bottom: 20px;
        }

        .common-sidebar__content .title {
            margin-bottom: 5px;
        }

        @media (max-width:991px) {
            .api-sidebar-menu {
                padding: 20px 30px;
            }
        }

        .api-content__desc {
            margin-bottom: 20px;
            margin-top: 8px;
            font-size: 14px;
        }

        .copied::after {
            position: absolute;
            top: 8px;
            right: 0;
            width: 100px;
            display: block;
            content: "Copied";
            font-size: 12px;
            padding: 5px 5px;
            color: #fff;
            border-radius: 3px;
            background-color: transparent;
        }

        .form-group-label {
            color: #34495e;
            font-size: 16px;
            font-weight: 500;
        }

        pre[class*=language-] {
            margin: 0px !important
        }

        /* new */

        .form-control:focus {
            box-shadow: none;
        }

        .success-code .token {
            color: #9cd529 !important;
        }

        .error-code .token {
            color: #dc3545 !important;
        }

        .api-docs-endpoint {
            position: relative;
            background: #22272e;
            padding: 12px 15px;
            padding-right: 40px;
        }

        .api-docs-endpoint .badge {
            background: #2775e2 !important;
            color: #fff !important;
            margin-right: 5px !important;
            line-height: 1;
        }

        .api-docs-endpoint .text {
            color: #dde1e4;
        }

        .api-docs-endpoint .right-highlited__button {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #fff;
            font-size: 20px;
        }

        .api-docs-parameter ul li {
            position: relative;
            padding-left: 18px;
            margin-bottom: 8px;
        }

        .api-docs-parameter ul li:last-child {
            margin-bottom: 0;
        }

        .api-docs-parameter ul li::before,
        .api-docs-parameter ul li::after {
            position: absolute;
            content: '';
            background: transparent;
            left: 0;
            top: 8px;
            width: 10px;
            height: 10px;
            border: 1px solid hsla(var(--heading-color));
            border-radius: 50%;
        }

        .api-docs-parameter ul li::after {
            width: 6px;
            height: 6px;
            background: hsl(var(--heading-color));
            border: 0;
            left: 2px;
            top: 10px;
        }

        .api-docs-parameter ul li strong {
            color: hsl(var(--heading-color));
        }

        .api-docs-content {
            scroll-margin-top: 75px;
            scroll-margin-bottom: 75px;
        }

        .api-docs-content .title {
            margin-bottom: 10px;
        }


        .api-docs-content .desc {
            margin-bottom: 20px;
        }

        .api-docs-content .subtitle.two {
            font-size: 16px;
        }

        .api-docs-card {
            border: 1px solid hsl(var(--border-color-light)/0.45);
            background-color: hsl(var(--white));
            border-radius: 8px;
            box-shadow: var(--box-shadow);
        }

        .api-docs-card .card-header {
            background: transparent;
            border-color: hsl(var(--border-color-light)/0.45);
        }

        .api-docs-card .card-header .title {
            margin-bottom: 0;
        }

        .api-docs-card ul li {
            margin-bottom: 8px;
        }

        .api-docs-card ul li strong {
            color: hsl(var(--heading-color))
        }

        @media (max-width: 1199px) {
            .api-docs-content .subtitle.two {
                font-size: 15px;
            }

            .api-docs-card ul li,
            .api-docs-parameter ul li {
                margin-bottom: 6px;
                font-size: 15px;
            }

            .api-sidebar-submenu__item:not(:last-child) {
                margin-bottom: 3px;
            }

            .common-sidebar__content:not(:last-child) {
                margin-bottom: 15px;
            }

            .common-sidebar__item {
                padding: 20px 15px;
            }


            .api-docs-content .desc {
                margin-bottom: 18px;
            }

            .api-content input.form-control,
            .api-docs-endpoint .text {
                font-size: 15px;
            }

            .api-docs-parameter ul li::before {
                top: 7px;
            }

            .api-docs-parameter ul li::after {
                top: 9px;
            }
        }

        @media (max-width: 767px) {
            .api-docs-parameter ul li {
                padding-left: 15px;
            }
        }

        @media (max-width: 575px) {
            .common-sidebar__item {
                padding: 15px 12px;
            }

            .common-sidebar__content:not(:last-child) {
                margin-bottom: 10px;
            }

            .common-sidebar__content .title {
                margin-bottom: 3px;
            }

            .api-sidebar-submenu__item:not(:last-child) {
                margin-bottom: 0;
            }

            .api-docs-content .title {
                margin-bottom: 3px;
            }

            .api-docs-content .desc {
                margin-bottom: 15px;
            }

            .api-docs-card ul li,
            .api-docs-parameter ul li {
                margin-bottom: 5px;
                font-size: 14px;
            }

            .api-docs-content .subtitle.two {
                font-size: 14px;
            }

            code .token {
                font-size: 14px;
            }

            .api-content input.form-control,
            .api-docs-endpoint .text {
                font-size: 14px;
            }

            .api-docs-parameter ul li {
                padding-left: 12px;
            }

            .api-docs-parameter ul li::before {
                width: 8px;
                height: 8px;
            }

            .api-docs-parameter ul li::after {
                width: 4px;
                height: 4px;
                left: 2px;
                top: 9px;
            }
        }

        @media (max-width: 424px) {
            code .token {
                font-size: 13px;
            }

            .api-docs-endpoint {
                padding: 10px 12px;
                padding-right: 40px;
            }
        }
    </style>
@endpush
