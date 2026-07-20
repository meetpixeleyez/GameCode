@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light">
                            <thead>
                                <tr>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Value')</th>
                                    <th>@lang('Size')</th>
                                    <th>@lang('Redirect')</th>
                                    <th>@lang('Impression')</th>
                                    <th>@lang('Click')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($advertisements as $advertisement)
                                    <tr>
                                        <td><span class="fw-bold text--primary">{{ __($advertisement->type) }}</span></td>
                                        <td>
                                            @if (@$advertisement->type == 'image')
                                                <img src="{{ getImage(getFilePath('advertisement') . '/' . $advertisement->value) }}"
                                                    alt="" class="max-w-50">
                                            @else
                                                <span class="badge badge--primary">@lang('Script')</span>
                                            @endif
                                            {{ __(@$advertisement->symbol) }}

                                        </td>
                                        <td>{{ $advertisement->size }}</td>
                                        <td>
                                            @if ($advertisement->redirect_url != 'N/A')
                                                <a target="_blank" class="text--info"
                                                    href="{{ $advertisement->redirect_url }}">
                                                    <i class="las la-external-link-alt"></i>
                                                </a>
                                            @else
                                                {{ __($advertisement->redirect_url) }}
                                            @endif
                                        </td>

                                        <td>
                                            <span class="badge badge--success"> {{ @$advertisement->impression }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge--primary">
                                                {{ @$advertisement->click }}
                                            </span>
                                        </td>

                                        <td> @php echo $advertisement->statusBadge @endphp </td>
                                        <td>
                                            <div class="button--group">
                                                <button type="button" class="btn btn-sm btn-outline--primary editBtn"
                                                    data-action="{{ route('admin.advertisement.store', $advertisement->id) }}"
                                                    data-image="{{ getImage(getFilePath('advertisement') . '/' . @$advertisement->value) }}"
                                                    data-advertisement="{{ $advertisement }}">
                                                    <i class="la la-pen"></i>
                                                    @lang('Edit')
                                                </button>

                                                @if ($advertisement->status == Status::DISABLE)
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-action="{{ route('admin.advertisement.status', $advertisement->id) }}"
                                                        data-question="@lang('Are you sure to enable this advertisement?')">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-action="{{ route('admin.advertisement.status', $advertisement->id) }}"
                                                        data-question="@lang('Are you sure to disable this advertisement?')">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($advertisements->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($advertisements) @endphp
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />

    <div class="modal fade " id="advertizementModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form class="form-horizontal" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>@lang('Advertisement Type')</label>
                                    <select class="form-control" id="advertisementType" name="type" required>
                                        <option value="" selected disabled>@lang('Select One')</option>
                                        <option value="image">@lang('Image')</option>
                                        <option value="script">@lang('Script')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12" id="imageSize">
                                <div class="form-group">
                                    <div class="image-size">
                                        <label>@lang('Size')</label>
                                        <select class="form-control" name="size">
                                            <option value="" selected>@lang('Select One')</option>
                                            <option value="728x90">@lang('728x90')</option>
                                            <option value="300x600">@lang('300X600')</option>
                                            <option value="300x250">@lang('300x250')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 d-none" id="advertizementImage">
                                <div class="form-group">
                                    <label> @lang('Image')</label>
                                    <x-image-uploader name="image" type="advertisement" :size="false" class="w-100"
                                        id="imageUpload" :required="false" />
                                </div>
                                <div class="form-group">
                                    <label class="required">@lang('Redirect Url') </label>
                                    <input type="text" class="form-control" name="redirect_url"
                                        placeholder="@lang('Redirect Url')">
                                </div>
                            </div>
                            <div class="col-lg-12 d-none" id="advertisementScript">
                                <div class="form-group">
                                    <label class="font-weight-bold required">@lang('Script')</label>
                                    <textarea name="script" class="form-control" rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form />
    <button type="button" class="btn btn-sm h-45 btn-outline--primary addAdvertisement"
        data-action="{{ route('admin.advertisement.store') }}">
        <i class="la la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            let backgroundImage = '';
            $('.addAdvertisement').on('click', function() {
                backgroundImage = '';
                let modal = $('#advertizementModal');
                let data = $(this).data();
                modal.find(".modal-title").text("@lang('Add Advertisement')");
                modal.find('form')[0].reset();
                modal.find('form').attr('action', data.action);
                $('#advertisementType').val('image');
                $('#imageSize').find('select').val('728x90');
                $('#advertizementImage').removeClass('d-none').addClass('d-block');

                placeholderImage('728x90');
                changeImagePreview();

                $('#advertisementScript').removeClass('d-block').addClass('d-none');
                modal.modal('show');
            });


            $('#advertisementType').on('change', function() {
                let advertisementType = $('#advertisementType').val();

                if (advertisementType === 'image') {
                    $('#imageSize').find('select').val('728x90');
                    $('#advertizementImage').removeClass('d-none').addClass('d-block');

                    placeholderImage('728x90');
                    changeImagePreview();
                    $('#advertisementScript').removeClass('d-block').addClass('d-none');
                } else {
                    $('#advertizementImage').removeClass('d-block').addClass('d-none');
                    $('#advertisementScript').removeClass('d-none').addClass('d-block');
                    $('[name="script"]').val('');
                }
            });

            $('#imageSize').on('change', function() {
                let imageSize = $(this).find('select');
                let type = $("#advertisementType").val();

                if (type == null || type.length <= 0) {
                    alert("@lang('Please first select type')")
                    $("#advertisementType").focus();
                    imageSize.val("");
                    return;
                }
                if (type == "image") {
                    placeholderImage(imageSize.val());
                    changeImagePreview();
                    $('#advertizementImage').removeClass('d-none');
                    $('#advertizementImage').addClass('d-block');
                }

            });

            $('.editBtn').on('click', function() {
                let modal = $('#advertizementModal');
                let data = $(this).data();
                modal.find(".modal-title").text("@lang('Edit Advertisement')");
                modal.find('form')[0].reset();
                modal.find('form').attr('action', data.action);

                let advertisementType = modal.find("#advertisementType");
                advertisementType.val(data.advertisement.type);
                advertisementType.find('option').not(':selected');

                if (data.advertisement.type == 'image') {
                    let imageSize = modal.find("#imageSize").find("select");
                    imageSize.val(data.advertisement.size);
                    imageSize.find('option').not(':selected');
                    $('#imageSize').addClass('d-block');
                    $('#imageSize').removeClass('d-none');
                    backgroundImage = $(this).data('image');

                    $(modal).find('.image-upload-preview').css('background-image', `url(${backgroundImage})`);
                    $(modal).find('.image-upload').css('display', 'block')
                    modal.find('input[name="redirect_url"]').val(data.advertisement.redirect_url)
                    modal.find('textarea[name=script]').text("");
                    changeImagePreview();
                } else {
                    $('#advertizementImage').removeClass('d-block').addClass('d-none');
                    $('#advertisementScript').removeClass('d-none').addClass('d-block');
                    $('textarea[name=script]').text(data.advertisement.content);
                    $(modal).find('.profilePicPreview').css('background-image', `url("")`);
                }
                modal.modal('show');
            });

            function placeholderImage(imageSize) {
                let placeholderImageUrl = `{{ route('placeholder.image', ':size') }}`;
                $('.image-upload').css('display', 'block')
                $('.image-upload-preview').css('background-image',
                    `url(${backgroundImage != '' ? backgroundImage : placeholderImageUrl.replace(':size',imageSize)})`
                );
                $('#advertisement__image_size').text(`, Upload Image Size Must Be ${imageSize} px`);
                $("#imageUpload").attr('size-validation', imageSize)
            }

            function changeImagePreview() {
                let selectSize = $(document).find("#imageSize").find('select').val();
                let size = selectSize.split('x');
                $('#advertizementImage').removeClass('d-none').addClass('d-block');
                $('#advertisementScript').removeClass('d-block').addClass('d-none');
            }
        })(jQuery);
    </script>
@endpush

@push('script')
    <style>
        .max-w-50 {
            max-width: 50px !important;
        }
    </style>
@endpush
