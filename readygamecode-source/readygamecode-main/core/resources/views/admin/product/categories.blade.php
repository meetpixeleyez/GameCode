@extends('admin.layouts.app')

@section('panel')
    @push('topBar')
        @include('admin.product.categories_top_bar')
    @endpush

    <div class="row">
        <div class="col-md-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Personal Buyer Fee')</th>
                                    <th>@lang('Commercial Buyer Fee')</th>
                                    <th>@lang('Twelve Months Extended Fee')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Featured')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($categories as $category)
                                    <tr>
                                        <td>
                                            <div class="user">
                                                <div class="thumb">
                                                    <img src="{{ getImage(getFilePath('category') . '/' . @$category->image) }}"
                                                         alt="@lang('Category Image')" />
                                                    <span class="ms-2">{{ __($category->name) }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ showAmount($category->personal_buyer_fee) }}</td>
                                        <td>{{ showAmount($category->commercial_buyer_fee) }}</td>
                                        <td>{{ showAmount($category->twelve_month_extended_fee) }}</td>
                                        <td>@php echo $category->statusBadge @endphp</td>
                                        <td>@php echo $category->featuredBadge @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.category.seo', $category->id) }}"
                                                   class="btn btn-sm btn-outline--info"><i class="la la-cog"></i>
                                                    @lang('SEO Setting')</a>
                                                <button class="btn btn-outline--dark btn-sm" data-bs-toggle="dropdown"
                                                        type="button" aria-expanded="false">
                                                    <i
                                                       class="la la-ellipsis-v d-none d-sm-inline-block"></i>@lang('More')
                                                </button>
                                                <div class="dropdown-menu">
                                                    <button class="btn btn-sm btn-outline--primary editButton dropdown-item"
                                                            data-id="{{ $category->id }}" data-name="{{ __($category->name) }}"
                                                            data-status="{{ $category->status }}"
                                                            data-personal_buyer_fee="{{ getAmount($category->personal_buyer_fee) }}"
                                                            data-commercial_buyer_fee="{{ getAmount($category->commercial_buyer_fee) }}"
                                                            data-twelve_month_extended_fee="{{ getAmount($category->twelve_month_extended_fee) }}"
                                                            data-description="{{ $category->description }}"
                                                            data-file_type="{{ $category->file_type }}"
                                                            data-file_size="{{ $category->file_size }}"
                                                            data-preview_file_types="{{ json_encode($category->preview_file_types) }}"
                                                            data-action="{{ route('admin.category.update', $category->id) }}"
                                                            data-image="{{ getImage(getFilePath('category') . '/' . @$category->image) }}">
                                                        <i class="la la-pencil"></i>@lang('Edit')
                                                    </button>
                                                    @if ($category->featured)
                                                        <button
                                                                class="btn btn-outline--danger btn-sm confirmationBtn dropdown-item"
                                                                data-question="@lang('Are you sure to unfeautre this category?')"
                                                                data-action="{{ route('admin.category.feature.toggle', $category->id) }}"
                                                                type="button">
                                                            <i class="las la-eye-slash"></i>@lang('Unfeature')
                                                        </button>
                                                    @else
                                                        <button
                                                                class="btn btn-outline--info btn-sm confirmationBtn dropdown-item"
                                                                data-question="@lang('Are you sure to feature this category?')"
                                                                data-action="{{ route('admin.category.feature.toggle', $category->id) }}"
                                                                type="button">
                                                            <i class="las la-eye"></i>@lang('Feature')
                                                        </button>
                                                    @endif
                                                    @if ($category->status)
                                                        <button
                                                                class="btn btn-outline--danger btn-sm confirmationBtn dropdown-item"
                                                                data-question="@lang('Are you sure to inactive this category?')"
                                                                data-action="{{ route('admin.category.active.toggle', $category->id) }}"
                                                                type="button">
                                                            <i class="las la-eye-slash"></i>@lang('Inactive')
                                                        </button>
                                                    @else
                                                        <button
                                                                class="btn btn-outline--info btn-sm confirmationBtn dropdown-item"
                                                                data-question="@lang('Are you sure to active this category?')"
                                                                data-action="{{ route('admin.category.active.toggle', $category->id) }}"
                                                                type="button">
                                                            <i class="las la-eye"></i>@lang('Active')
                                                        </button>
                                                    @endif
                                                </div>
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
                @if ($categories->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($categories) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="categoryModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row jus">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="imageLabel">@lang('Image')</label>
                                    <x-image-uploader class="w-100" type="category" />
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label>@lang('Name')</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Buyer Fee')(@lang('Personal License'))</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                        <input class="form-control" type="number" step="any"
                                               name="personal_buyer_fee" required>
                                        <span class="input-group-text">{{ gs('cur_text') }}</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Buyer Fee')(@lang('Commercial License'))</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                        <input class="form-control" type="number" step="any"
                                               name="commercial_buyer_fee" required>
                                        <span class="input-group-text">{{ gs('cur_text') }}</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>@lang('12 Month Extended Fee')</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                        <input class="form-control" type="number" step="any"
                                               name="twelve_month_extended_fee" required>
                                        <span class="input-group-text">{{ gs('cur_text') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('File Type')</label>
                                    <select class="form-control" name="file_type" id="fileType">
                                        <option value="">@lang('Choose')</option>
                                        <option value="script">@lang('Script')</option>
                                        <option value="audio">@lang('Audio')</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6" id="fileSizeGroup" class="hidden-group">
                                <div class="form-group">
                                    <label>@lang('File Size (MB)')</label>
                                    <input type="number" class="form-control" name="file_size" step="any">
                                </div>
                            </div>

                            <div class="col-md-6" id="previewFileTypesGroup" class="hidden-group">
                                <div class="form-group">
                                    <label>@lang('Preview File Types')</label>
                                    <select name="preview_file_types[]" class="form-control select2-auto-tokenize"
                                            multiple="multiple">
                                    </select>
                                    <em class="form-text text-muted">
                                        @lang('The allowed files to be uploaded as main file, like (MP3,PNG,JPG,JPEG)')
                                    </em>
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
    <x-confirmation-modal />
@endsection


@push('breadcrumb-plugins')
    <x-search-form placeholder="Category Name" />
    <button class="btn btn-outline--primary btn-sm addBtn"><i class="las la-plus"></i>@lang('Add New')</button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            let modal = $('#categoryModal');
            let defaultImage = `{{ getImage(getFilePath('category'), getFileSize('category')) }}`;
            $('.addBtn').on('click', function() {
                $('select[name="preview_file_types[]"]').empty();
                modal.find('.modal-title').text(`@lang('Add Category')`);
                modal.find('.image-upload-preview').css('background-image', `url(${defaultImage})`);
                modal.find('form').attr('action', `{{ route('admin.category.store') }}`);
                modal.find('[name=image]').attr('required', true);
                modal.find('.imageLabel').addClass('required');
                modal.modal('show');
            });
            $('.editButton').on('click', function() {
                let data = $(this).data();
                $('select[name="preview_file_types[]"]').empty();

                modal.find('.modal-title').text(`@lang('Edit Category')`);
                modal.find('[name=name]').val(data.name);
                modal.find('[name=file_type]').val(data.file_type).trigger('change');
                modal.find('[name=file_size]').val(data.file_size);
                if (data.preview_file_types) {
                    data.preview_file_types.forEach(function(option) {
                        var optionElement = `<option value="${option}" selected>${option}</option>`;
                        $('select[name="preview_file_types[]"]').append(optionElement);
                    });
                }
                modal.find('[name=personal_buyer_fee]').val(data.personal_buyer_fee);
                modal.find('[name=commercial_buyer_fee]').val(data.commercial_buyer_fee);
                modal.find('[name=twelve_month_extended_fee]').val(data.twelve_month_extended_fee);
                modal.find('.image-upload-preview').css('background-image', `url(${data.image})`);
                modal.find('[name=description]').html(data.description);
                modal.find('[name=status]').bootstrapToggle(data.status ? 'on' : 'off');
                modal.find('form').attr('action', data.action);
                modal.find('[name=image]').removeAttr('required', true);
                modal.modal('show');
            });
            modal.on('hidden.bs.modal', function() {
                modal.find('form')[0].reset();
                modal.find('[name=status]').bootstrapToggle('on');
            });


            function toggleFileFields() {
                if ($('#fileType').val() !== 'audio') {
                    $("[name='preview_file_types[]']").val(null)
                    $('[name="file_size"]').val(null)
                }

                if ($('#fileType').val() === 'audio') {
                    $('#fileSizeGroup').removeClass('hidden-group');
                    $('#previewFileTypesGroup').removeClass('hidden-group');
                } else {
                    $('#fileSizeGroup').addClass('hidden-group');
                    $('#previewFileTypesGroup').addClass('hidden-group');
                }
            }

            toggleFileFields();

            $('#fileType').on('change', function() {
                toggleFileFields();
            });


        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .hidden-group {
            display: none;
        }
    </style>
@endpush
