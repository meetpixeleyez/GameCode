@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="upload-product pt-60 pb-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-8 col-xl-10">
                    <form action="{{ route('user.product.upload') }}" class="upload-product-item-wrapper">
                        <div class="upload-product-item">
                            <h6 class="upload-product-item__title">@lang('Select Category')</h6>
                            <div class="form-group">
                                <label class="form--label">@lang('Category')</label>
                                <select class="select form--control form--control--sm category select2" name="category"
                                    required>
                                    <option value="">@lang('Select One')</option>
                                    @foreach ($categories as $category)
                                        <option data-subcategories="{{ $category->subCategories }}"
                                            value="{{ $category->id }}">{{ __($category->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form--label">@lang('Subcategory')</label>
                                <select name="sub_category" class="select form--control form--control--sm select2" required>
                                    <option value="">@lang('Select One')</option>
                                </select>
                            </div>
                            @if (gs('free_item'))
                                <div class="form-group d-flex">
                                    <div>
                                        <label class="form--label mb-0 me-2">@lang('Would you like to offer this product for free?')</label>
                                    </div>
                                    <div class="custom-switch-div">
                                        <div class="custom-switch">
                                            <input type="checkbox" name="is_free" id="is_free" value="1"
                                                class="custom-switch-input">
                                            <label class="custom-switch-label" for="is_free">
                                                <span class="custom-switch-inner"></span>
                                                <span class="custom-switch-switch"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <button type="submit" class="btn btn--sm  btn--base">@lang('Next')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <style>
        .custom-switch {
            position: relative;
            display: inline-block;
            width: 30px;
            height: 15px;
        }

        .custom-switch-input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .custom-switch-label {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 15px;
        }

        .custom-switch-switch {
            position: absolute;
            content: "";
            height: 11px;
            width: 11px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        .custom-switch-input:checked+.custom-switch-label {
            background-color: #28a745;
        }

        .custom-switch-input:checked+.custom-switch-label .custom-switch-switch {
            transform: translateX(15px);
        }

        .custom-switch-div {
            padding-top: 6px;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.category').on('change', function() {
                let subcategories = $(this).find(':selected').data('subcategories');
                let html = '';

                $.each(subcategories, function(index, subcategory) {
                    html += `<option value="${subcategory.id}">${subcategory.name}</option>`;
                });

                $('[name=sub_category]').html(html);
            });
        })(jQuery);
    </script>
@endpush
