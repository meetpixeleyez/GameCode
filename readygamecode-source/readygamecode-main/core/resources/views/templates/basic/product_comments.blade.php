@extends($activeTemplate . 'layouts.frontend')
@section('meta_title', gs()->siteName(__($pageTitle)))
@section('meta_description', __('View user comments and discussion for this Unity game source code product.'))
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
                    <div class="user-comment-wrapper">
                        @forelse ($comments as $comment)
                            <div class="user-comment">


                                <div class="user-comment__content flex-between align-items-start">
                                    <div class="user-comment__profile">
                                        <div class="user-comment__thumb">
                                            <x-author-avatar :author="$comment->user" />
                                        </div>
                                        <div class="user-comment__info">
                                            <h6 class="user-comment__name">
                                                <a
                                                    href="{{ route('user.profile', $comment->user->username) }}">{{ $comment->user->fullname }}</a>
                                            </h6>
                                            @if ($comment->user->orderItems->where('product_id', $product->id)->count())
                                                <span>@lang('Purchased')</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="user-comment__time">{{ diffForHumans($comment->created_at) }}</span>
                                </div>

                                @if (@$comment->is_reported)
                                    <div class="alert alert-light" role="alert">
                                        @lang('This comment is currently under review')
                                    </div>
                                @else
                                    <p class="user-comment__desc">
                                        {{ $comment->text }}
                                    </p>
                                @endif

                                {{-- Report Option for Author --}}
                                @if (auth()->user() && $comment->product->user_id === auth()->id())
                                    <div class="user-comment__report text-end">
                                        @if (!@$comment->is_reported)
                                            <button type="button" class="btn btn--sm btn-outline--warning reportCommentBtn"
                                                data-action="{{ route('user.author.comments.report', $comment->id) }}">
                                                <i class="las la-flag me-0"></i> @lang('Report')
                                            </button>
                                        @endif
                                    </div>
                                @endif


                                {{-- replies of the comment --}}
                                @if (!@$comment->is_reported)
                                    @foreach ($comment->replies as $reply)
                                        <div class="author-reply">
                                            <div class="author-reply__thumb">
                                                <x-author-avatar :author="$reply->user" />
                                            </div>
                                            <div class="author-reply__content">
                                                <div class="flex-between flex-nowrap">
                                                    <div>
                                                        <h6 class="author-reply__name">
                                                            <a
                                                                href="{{ route('user.profile', $reply->user->username) }}">{{ @$reply->user->fullname }}</a>
                                                        </h6>
                                                        <span class="author-reply__response mb-0">
                                                            @if ($reply->author_reply)
                                                                @lang('Author')
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <span
                                                        class="author-reply__time">{{ diffForHumans($reply->created_at) }}</span>
                                                </div>
                                                <p class="author-reply__desc mt-2">{{ $reply->text }}</p>
                                            </div>
                                        </div>
                                    @endforeach

                                    @if (auth()->user() && ($comment->product->user_id === auth()->id() || auth()->id() == $comment->user_id))
                                        @if (!(gs('comment_disable') && @$product->comment_disable == Status::ENABLE))
                                            <div class="author-reply">
                                                <div class="author-reply__thumb">
                                                    <x-author-avatar :author="auth()->user()" />
                                                </div>
                                                <div class="review-reply__content">
                                                    <form action="{{ route('user.author.comment.store', $product->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="hidden" name="parent_id"
                                                            value="{{ $comment->id }}" />
                                                        <textarea name="text" class="form--control textarea--sm bg--white" placeholder="@lang('Write reply...')"></textarea>
                                                        <div class="review-reply__button text-end">
                                                            <button type="submit"
                                                                class="btn btn--base btn--sm">@lang('Reply')</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                @endif

                            </div>
                        @empty
                            <div class="card mb-3 custom--card">
                                <div class="card-body">
                                    <x-empty-list title="This product has no comments" />
                                </div>
                            </div>
                        @endforelse

                        <div class="pt-30">
                            {{ paginateLinks($comments) }}
                        </div>

                        @if (gs('comment_disable') && @$product->comment_disable == Status::ENABLE)
                            <p class="text-center text-muted">@lang('Comments are currently disabled for this product')</p>
                        @else
                            @if (auth()->user() && @$comment->product->user_id !== auth()->id())
                                <div class="user-comment mt-4">
                                    <h6 class="user-comment__name">@lang('Add a comment')</h6>
                                    <div class="author-reply">
                                        <div class="author-reply__thumb">
                                            <x-author-avatar :author="auth()->user()" />
                                        </div>
                                        <div class="review-reply__content">
                                            <form action="{{ route('user.author.comment.store', $product->id) }}"
                                                method="POST">
                                                @csrf
                                                <textarea name="text" class="form--control textarea--sm bg--white" placeholder="@lang('Leave a comment for the author...')"></textarea>
                                                <div class="review-reply__button text-end">
                                                    <button type="submit"
                                                        class="btn btn--base btn--sm">@lang('Post Comment')</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                    </div>
                    @php
                        echo getAds('728x90');
                    @endphp
                </div>

                @include($activeTemplate . 'partials.common_sidebar')
            </div>
        </div>
    </section>

    <div id="reportModal" class="modal fade custom--modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Report Comment')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="question mb-2">@lang('Please provide a reason for reporting this comment:')</p>
                        <textarea name="report_reason" class="form--control" rows="4" placeholder="@lang('Write your reason...')" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--base btn--sm w-100">@lang('Submit Report')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $(document).on('click', '.reportCommentBtn', function() {
                var modal = $('#reportModal');
                let data = $(this).data();
                modal.find('form').attr('action', `${data.action}`);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
