@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="profile-content">
        <div class="row gy-4">
            <div class="col-lg-8">
                <div class="profile-content__thumb">
                    @php
                        $image = @$author->cover_img
                            ? getImage(getFilePath('authorAvatar') . '/' . @$author->cover_img)
                            : siteFavicon();
                    @endphp
                    @if (!@$author->cover_img)
                        <div class="default_cover_img">
                            <img src="{{ $image }}"
                                class="author-avatar {{ !@$author->cover_img ? 'avatar-from-fav' : '' }}"
                                alt="@lang('Cover Image')">
                        </div>
                    @else
                        <img src="{{ $image }}" alt="@lang('Cover Image')">
                    @endif
                </div>
                <div class="profile-content-list">
                    <div class="profile-content-list__item">
                        @php echo @$author->bio @endphp
                    </div>
                </div>

                @if ($collections->count() > 0)
                    <div id="collections">
                        <h4 class="mt-3">@lang('Collections')</h4>
                        @if (auth()->user() && auth()->user()->username === $author->username)
                            <div class="show-filter mb-3 text-end">
                                <button type="button" class="btn btn--base showFilterBtn btn--sm"><i
                                        class="las la-filter"></i>
                                    @lang('Filter')</button>
                            </div>
                            <div class="card responsive-filter-card mb-4 custom--card">
                                <div class="card-body">
                                    <form>
                                        <div class="d-flex flex-wrap gap-4">
                                            <div class="flex-grow-1">
                                                <label class="form--label">@lang('Name')</label>
                                                <input type="text" name="search" value="{{ request()->search }}"
                                                    class="form-control form--control form--control--sm">
                                            </div>
                                            <div class="flex-grow-1 select2-parent">
                                                <label class="form-label d-block">@lang('Type')</label>
                                                <select name="is_public" class="form-select form--control select2"
                                                    data-minimum-results-for-search="-1">
                                                    <option value="">@lang('All')</option>
                                                    <option value="1" @selected(request()->is_public == '1')>@lang('Public')
                                                    </option>
                                                    <option value="0" @selected(request()->is_public == '0')>@lang('Private')
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="flex-grow-1 align-self-end">
                                                <button class="btn btn--sm btn--base w-100"><i class="las la-filter"></i>
                                                    @lang('Filter')</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                        <div class="row">
                            @foreach ($collections as $collection)
                                <div class="col-md-4">
                                    <div>
                                        <a href="{{ route('user.collections.details', ['username' => $author->username, 'id' => $collection->id]) }}"
                                            class="link">
                                            @php
                                                $collection->products->take(4);
                                                $image = $collection->image
                                                    ? getImage(
                                                        getFilePath('productCollection') . '/' . $collection->image,
                                                    )
                                                : getImage(getFilePath('productCollection') . '/default.png'); @endphp
                                            <img src="{{ $image }}" alt="@lang('Collection Image')">
                                        </a>
                                        <h6 class="text-center mt-2">
                                            <a
                                                href="{{ route('user.collections.details', ['username' => $author->username, 'id' => $collection->id]) }}">
                                                {{ __($collection->name) }}
                                            </a>
                                            @if (auth()->user() && auth()->user()->username === $author->username)
                                                <a class="confirmationBtn text--danger" type="button"
                                                    data-question="@lang('Are you sure to delete this collection?')"
                                                    data-action="{{ route('user.author.collections.delete', $collection->id) }}"><i
                                                        class="las la-trash"></i>
                                                </a>
                                                <a class="text-secondary collectionSettingBtn"
                                                    data-name="{{ @$collection->name }}"
                                                    data-is_public="{{ @$collection->is_public }}"
                                                    data-action="{{ route('user.author.collections.update', @$collection->id) }}">
                                                    <span class="icon"><i class="la la-gear"></i></span></a>
                                            @endif
                                        </h6>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ paginateLinks($collections) }}
                        </div>
                    </div>
                @endif
            </div>
            <div class="col-lg-4 ps-xl-5">
                <div class="common-sidebar">
                    @include($activeTemplate . 'partials.quick_upload')
                    @include($activeTemplate . 'partials.email_support')
                    @include($activeTemplate . 'partials.social_profile')
                </div>
            </div>
        </div>
    </div>

    <div id="collectionModal" class="modal fade custom--modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Create New Collection')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('user.author.collections.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name" class="form--label">@lang('Collection Name')</label>
                            <input type="text" name="name" class="form--control form--control--sm" id="name"
                                placeholder="@lang('Please write a meaningful name')" required />
                        </div>
                        <div class="form-group">
                            <label for="image" class="form-label">@lang('Image')</label>
                            <input type="file" name="image" id="image" class="form--control form--control--sm">
                            <small id="emailHelp"
                                class="form-text text-muted fs-12">@lang('The uploaded file must be '){{ getFileSize('productCollection') }}</small>
                        </div>
                        <div class="form-group d-flex collection-input-radio">
                            <div class="form--radio">
                                <input type="radio" class="form-check-input" name="is_public" id="private"
                                    value="0">
                                <label for="private"
                                    class="form-check-label custom-cursor-on-hover"><small>@lang('Keep it private')</small></label>
                            </div>

                            <div class="form--radio">
                                <input type="radio" class="form-check-input" name="is_public" id="public"
                                    value="1">
                                <label for="public"
                                    class="form-check-label custom-cursor-on-hover"><small>@lang('Make it public')</small></label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark btn--sm"
                            data-bs-dismiss="modal">@lang('Cancel')</button>
                        <button type="submit" class="btn btn--base btn--sm">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-confirmation-modal frontend="true" />
@endsection

@push('style')
    <style>
        .profile-content__thumb {
            border-radius: 0;
        }

        .default_cover_img {
            width: 100%;
            background: #ededed;
            min-height: 400px;
            display: grid;
            place-content: center;
        }

        .default_cover_img img {
            min-width: 80%;
            min-height: 80%;
        }
    </style>
@endpush


@push('script')
    <script>
        (function($) {
            "use strict";

            $(document).ready(function() {
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('search')) {
                    $('html, body').animate({
                        scrollTop: $('#collections').offset().top
                    }, 500);
                }
            });
            var modal = $('#collectionModal');
            $('#opencollectionModal').on('click', function() {
                let data = $(this).data();
                modal.modal('show');
            });

            $('.collectionSettingBtn').on('click', function() {

                let data = $(this).data();
                modal.find('[name=name]').val(data.name);
                modal.find('form').attr('action', data.action);
                modal.find(`[value="${data.is_public}"]`).attr('checked', true);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
