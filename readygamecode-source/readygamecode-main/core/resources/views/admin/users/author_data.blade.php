@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card custom--card">
                <div class="card-header">
                    <h5 class="card-title">@lang('Author Information')</h5>
                </div>
                <div class="card-body">
                    @if ($user->author_info)
                        <ul class="list-group list-group-flush">
                            @foreach ($user->author_info as $val)
                                @continue(!$val->value)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-muted">{{ __($val->name) }}</span>
                                    <span class="text-end">
                                        @if ($val->type == 'checkbox')
                                            <span class="badge bg-primary">{{ implode(', ', $val->value) }}</span>
                                        @elseif($val->type == 'file')
                                            @if ($val->value)
                                                <a href="{{ route('admin.download.attachment', encrypt(getFilePath('verify') . '/' . $val->value)) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fa-regular fa-file me-1"></i>@lang('Download')
                                                </a>
                                            @else
                                                <span class="badge bg-warning">@lang('No File')</span>
                                            @endif
                                        @else
                                            <span class="text-dark">{{ __($val->value) }}</span>
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center">
                            <i class="fas fa-info-circle text-warning mb-2 fs-3"></i>
                            <h5>@lang('Author data not found')</h5>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.author.list') }}" />
@endpush

@push('style')
    <style>
        .custom--card {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            border: none;
        }

        .custom--card .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        .list-group-item {
            border-left: none;
            border-right: none;
        }

        .list-group-item:first-child {
            border-top: none;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }
    </style>
@endpush
