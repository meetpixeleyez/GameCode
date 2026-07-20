@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Email-Phone')</th>
                                    <th>@lang('Country')</th>
                                    <th>@lang('Joined At')</th>
                                    <th>@lang('Balance')</th>
                                    <th>@lang('Featured')</th>
                                    <th>@lang('Rating')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($authors as $user)
                                    <tr>
                                        <td>
                                            <div class="user d-flex">
                                                <div class="thumb">
                                                    <x-author-avatar :author="$user" />
                                                </div>
                                                <div class="ms-2">
                                                    <span class="fw-bold">{{ __($user->fullname) }}</span>
                                                    <br>
                                                    <span class="small">
                                                        <a
                                                           href="{{ route('admin.users.detail', $user->id) }}"><span>@</span>{{ $user->username }}</a>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td> {{ $user->email }}<br>{{ $user->mobile }} </td>
                                        <td>
                                            <span class="fw-bold"
                                                  title="{{ @$user->country_name }}">{{ $user->country_code }}</span>
                                        </td>
                                        <td> {{ showDateTime($user->created_at) }} <br>
                                            {{ diffForHumans($user->created_at) }} </td>
                                        <td>
                                            <span class="fw-bold">
                                                {{ showAmount($user->balance) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php echo $user->featureBadge @endphp
                                        </td>
                                        <td>@php echo displayRating($user->avg_rating) @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <button
                                                        class="btn btn-outline--{{ $user->is_author_featured == Status::NO ? 'info' : 'danger' }} btn-sm confirmationBtn"
                                                        data-question="@lang('Are you sure to ' . ($user->is_author_featured == Status::NO ? 'feature' : 'unfeature') . ' this author?')"
                                                        data-action="{{ route('admin.author.feature.toggle', $user->id) }}"
                                                        type="button">
                                                    <i
                                                       class="las la-{{ $user->is_author_featured == Status::NO ? 'eye' : 'eye-slash' }} d-none d-sm-inline-block"></i>
                                                    @lang($user->is_author_featured == Status::NO ? 'Feature' : 'Unfeature')
                                                </button>

                                                <button class="btn btn-outline--info btn-sm" data-bs-toggle="dropdown"
                                                        type="button" aria-expanded="false">
                                                    <i
                                                       class="la la-ellipsis-v d-none d-sm-inline-block"></i>@lang('More')
                                                </button>

                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item"
                                                       href="{{ route('admin.product.all', ['author_id' => $user->id]) }}">
                                                        <i class="las la-box"></i>
                                                        @lang('Products')
                                                    </a>
                                                    <a class="dropdown-item"
                                                       href="{{ route('admin.users.detail', $user->id) }}">
                                                        <i class="las la-desktop"></i>
                                                        @lang('Details')
                                                    </a>
                                                    @if ($user->author_info)
                                                        <a class="dropdown-item"
                                                           href="{{ route('admin.author.data', $user->id) }}">
                                                            <i class="las la-user-check"></i>
                                                            @lang('Author Data')
                                                        </a>
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
                @if ($authors->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($authors) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Username / Email" />
@endpush
