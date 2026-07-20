@extends('admin.layouts.app')

@section('panel')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">@lang('Details')</h5>
        </div>
        <div class="card-body">
            <div class="row gy-4">
                <!-- Product Information -->
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <h6 class="border-bottom pb-2 mb-3"><i class="las la-shopping-cart me-2"></i>@lang('Product Information')</h6>
                        <div class="d-flex flex-column gap-2">
                            <div>
                                <span class="text-muted">@lang('Product Name'):</span>
                                <p class="fw-bold mb-0"><a
                                        href="{{ route('admin.product.details', $review->product->slug) }}">{{ $review->product->title }}</a>
                                </p>
                            </div>
                            <div>
                                <span class="text-muted">@lang('Author'):</span>
                                <p class="fw-bold mb-0"><a
                                        href="{{ route('admin.author.data', $review->author->id) }}">{{ $review->author->username }}</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Review Information -->
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <h6 class="border-bottom pb-2 mb-3"><i class="las la-user me-2"></i>@lang('Review Information')</h6>
                        <div class="d-flex flex-column gap-2">
                            <div>
                                <span class="text-muted">@lang('Reviewer'):</span>
                                <p class="fw-bold mb-0"><a
                                        href="{{ route('admin.reviewer.all', $review->user->id) }}">{{ $review->user->username }}</a>
                                </p>
                            </div>
                            <div>
                                <span class="text-muted">@lang('Report Reason'):</span>
                                <p class="fw-bold mb-0">{{ $review->reportDetails->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attachments -->
                <div class="col-12">
                    <div class="border rounded p-3">
                        <h6 class="border-bottom pb-2 mb-3"><i class="las la-paperclip me-2"></i>@lang('Attachments')</h6>
                        @if ($review->reportDetails->attachments->isNotEmpty())
                            <div class="list-group">
                                @foreach ($review->reportDetails->attachments as $k => $attachment)
                                    <a href="{{ route('admin.review.attachment.download', encrypt($attachment->id)) }}"
                                        class="list-group-item list-group-item-action d-flex align-items-center gap-3">
                                        <i class="las la-file fs-2"></i>
                                        <span>@lang('Attachment') {{ ++$k }}</span>
                                        <i class="las la-download ms-auto"></i>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">@lang('No attachments provided')</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="text-end">
                <button class="btn btn-success confirmationBtn me-2" data-question="@lang('Are you sure to show this review again? This will remove the report.')"
                    data-action="{{ route('admin.review.show', $review->id) }}">
                    <i class="las la-eye me-2"></i>@lang('Show Review')
                </button>
                <button class="btn btn-danger confirmationBtn" data-question="@lang('Are you sure to delete this review?')"
                    data-action="{{ route('admin.review.destroy', $review->id) }}">
                    <i class="las la-trash me-2"></i>@lang('Delete Review')
                </button>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('style')
    <style>
        .card-title {
            font-size: 1.1rem;
            font-weight: 500;
        }

        .border-bottom {
            border-color: #e5e5e5 !important;
        }

        .list-group-item:hover {
            background-color: #f8f9fa;
        }
    </style>
@endpush
