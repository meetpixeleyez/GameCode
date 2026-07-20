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
                                        href="{{ route('admin.product.details', $comment->product->slug) }}">{{ $comment->product->title }}</a>
                                </p>
                            </div>
                            <div>
                                <span class="text-muted">@lang('Comment'):</span>
                                <p class="fw-bold mb-0">{{ $comment->text }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comment Information -->
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <h6 class="border-bottom pb-2 mb-3"><i class="las la-user me-2"></i>@lang('Comment Information')</h6>
                        <div class="d-flex flex-column gap-2">
                            <div>
                                <span class="text-muted">@lang('Commenter'):</span>
                                <p class="fw-bold mb-0">
                                    <a
                                        href="{{ route('admin.users.detail', $comment->user->id) }}">{{ $comment->user->username }}</a>
                                </p>
                            </div>
                            <div>
                                <span class="text-muted">@lang('Report Reason'):</span>
                                <p class="fw-bold mb-0">{{ $comment->report_reason }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="text-end">
                <button class="btn btn-success confirmationBtn me-2" data-question="@lang('Are you sure to show this comment again? This will remove the report.')"
                    data-action="{{ route('admin.comment.show', $comment->id) }}">
                    <i class="las la-eye me-2"></i>@lang('Show Comment')
                </button>
                <button class="btn btn-danger confirmationBtn" data-question="@lang('Are you sure to delete this comment?')"
                    data-action="{{ route('admin.comment.destroy', $comment->id) }}">
                    <i class="las la-trash me-2"></i>@lang('Delete Comment')
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
