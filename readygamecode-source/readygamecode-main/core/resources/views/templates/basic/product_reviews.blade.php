@extends($activeTemplate . 'layouts.frontend')
@section('meta_title', gs()->siteName(__($pageTitle)))
@section('meta_description', __('Read customer reviews and ratings for this Unity game source code product.'))
@section('meta_robots', 'noindex, follow')

@section('content')
    @php
        $author = $product->author;
    @endphp

    <section class="product-details pt-60 pb-120">
        <div class="container">
            @include($activeTemplate . 'user.product.top')

            <div class="row gy-4">
                <div class="col-lg-8">
                    @forelse ($reviews as $review)
                        <div class="product-review">
                            <div class="product-review__top flex-between">
                                <div class="product-review__rating flex-align">
                                    <x-rating style="3" :value="$review->rating" />
                                    <span class="product-review__reason">@lang('For')
                                        <span class="product-review__subject"> {{ @$review->category->name }}</span>
                                    </span>
                                </div>
                                <div class="product-review__date">
                                    @lang('by')
                                    <a href="mailto:{{ $review->user->email }}" class="product-review__user text--base">
                                        {{ $review->user->fullname }}
                                    </a>
                                    {{ diffForHumans($review->updated_at) }}
                                </div>
                            </div>

                            <div class="product-review__body">
                                @if (@$review->is_reported)
                                    <div class="alert alert-light" role="alert">
                                        @lang('This review is currently under review')
                                    </div>
                                @else
                                    <p class="product-review__desc">
                                        {{ $review->review }}
                                    </p>
                                @endif
                            </div>

                            @if (auth()->user() && $review->product->user_id === auth()->id())
                                <div class="user-review__report text-end">
                                    @if (!$review->is_reported)
                                        <button type="button" class="btn btn--sm btn-outline--warning report-review-btn"
                                            data-bs-placement="top" data-bs-toggle="modal" data-bs-target="#reportModal"
                                            data-bs-title="@lang('Report This Review')" data-review-id="{{ $review->id }}">
                                            <i class="las la-flag me-0"></i> @lang('Report')
                                        </button>
                                    @endif
                                </div>
                            @endif

                            @if (!@$review->is_reported)
                                @foreach ($review->replies as $reply)
                                    <div class="author-reply">
                                        <div class="author-reply__thumb">
                                            <x-author-avatar :author="$review->author" />
                                        </div>
                                        <div class="author-reply__content">
                                            @php @endphp
                                            <h6 class="author-reply__name"><a
                                                    href="{{ route('user.profile', $review->author->username) }}"
                                                    class="link">{{ @$review->author->fullname }}</a></h6>
                                            <span class="author-reply__response">@lang('Author response')</span>
                                            <p class="author-reply__desc">{{ $reply->text }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            @if (!@$review->is_reported)
                                @if (auth()->user() && $review->author_id == auth()->id())
                                    <div class="review-reply mt-0 border-0">
                                        <div class="review-reply__thumb">
                                            <x-author-avatar :author="$review->author" />
                                        </div>
                                        <div class="review-reply__content">
                                            <form
                                                action="{{ route('user.author.review.reply', ['productId' => $product->id, 'reviewId' => $review->id]) }}"
                                                method="POST">
                                                @csrf
                                                <textarea name="reply" class="form--control textarea--sm bg--white" required placeholder="@lang('Write Reply')"></textarea>
                                                <div class="review-reply__button text-end">
                                                    <button type="submit"
                                                        class="btn btn--base btn--sm">@lang('Reply')</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @empty
                        <div class="card mb-3 custom--card">
                            <div class="card-body">
                                <x-empty-list title="This product has no review" />
                            </div>
                        </div>
                    @endforelse
                    <div class="pt-30">
                        {{ paginateLinks($reviews) }}
                    </div>
                    @php
                        echo getAds('728x90');
                    @endphp
                </div>
                @include($activeTemplate . 'partials.common_sidebar')
            </div>
        </div>
        {{-- REPORT MODAL --}}
        <div id="reportModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Report Review')</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <form action="{{ route('user.author.reviews.report') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="review_id" value="">
                        <div class="modal-body">
                            <p class="mb-3">@lang('Please share why you are reporting this review.')</p>
                            <textarea name="description" class="form-control" value="{{ old('description') }}" rows="3"
                                placeholder="@lang('Describe your reason for reporting this review.')" required></textarea>

                            <button type="button" class="btn btn-outline--base btn--sm addAttachment my-2"> <i
                                    class="fas fa-plus"></i> @lang('Add Attachment') </button>
                            <p class="mb-2"><span class="text-muted">@lang('Max 5 files can be uploaded | Maximum upload size is ' . convertToReadableSize(ini_get('upload_max_filesize')) . ' | Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx')</span></p>
                            <div class="row fileUploadsContainer">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            var fileAdded = 0;
            $('.addAttachment').on('click', function() {
                fileAdded++;
                if (fileAdded == 5) {
                    $(this).attr('disabled', true)
                }
                $(".fileUploadsContainer").append(`
                    <div class="col-lg-4 col-md-12 removeFileInput">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="file" name="attachments[]" class="form-control form--control" accept=".jpeg,.jpg,.png,.pdf,.doc,.docx" required>
                                <button type="button" class="input-group-text removeFile bg--danger border--danger"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                `)
            });
            $(document).on('click', '.removeFile', function() {
                $('.addAttachment').removeAttr('disabled', true)
                fileAdded--;
                $(this).closest('.removeFileInput').remove();
            });

            $(document).ready(function() {
                $('.report-review-btn').on('click', function() {
                    const reviewId = $(this).data('review-id');

                    $('#reportModal').find('input[name="review_id"]').val(reviewId);

                });
            });
        })(jQuery);
    </script>
@endpush
