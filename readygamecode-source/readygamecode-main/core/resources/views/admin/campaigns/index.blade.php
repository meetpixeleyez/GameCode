@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Minimum Discount')</th>
                                    <th>@lang('Maximum Discount')</th>
                                    <th>@lang('Start Date')</th>
                                    <th>@lang('End Date')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($campaigns as $campaign)
                                    <tr>
                                        <td>{{ __($campaign->name) }}</td>
                                        <td>
                                            {{ $campaign->discount_min }} %
                                        </td>
                                        <td>
                                            {{ $campaign->discount_max }} %
                                        </td>
                                        <td>
                                            {{ showDateTime($campaign->start_date, 'd M, Y') }}
                                        </td>
                                        <td>
                                            {{ showDateTime($campaign->end_date, 'd M, Y') }}
                                        </td>
                                        <td>
                                            @php
                                                echo $campaign->statusBadge;
                                            @endphp
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.campaign.edit', $campaign->id) }}"
                                                   class="btn btn-sm btn-outline--primary @if ($campaign->status == Status::EXPIRE) disabled @endif"><i class="las la-pen"></i>
                                                    @lang('Edit')</a>

                                                @if ($campaign->status == Status::DISABLE)
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline--success confirmationBtn"
                                                            data-action="{{ route('admin.campaign.status', $campaign->id) }}"
                                                            data-question="@lang('Are you sure to enable this campaign?')" @if ($campaign->status == Status::EXPIRE) disabled @endif>
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline--danger confirmationBtn"
                                                            data-action="{{ route('admin.campaign.status', $campaign->id) }}"
                                                            data-question="@lang('Are you sure to disable this campaign?')" @if ($campaign->status == Status::EXPIRE) disabled @endif>
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @endif

                                                <a href="{{ route('admin.campaign.submitted.products', $campaign->id) }}"
                                                   class="btn btn-sm btn-outline--primary"><i class="las la-eye"></i>
                                                    @lang('See Products')</a>
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
                @if ($campaigns->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($campaigns) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection
@push('breadcrumb-plugins')
    <x-search-form placeholder="Search by name" />
    <a href="{{ route('admin.campaign.create') }}" class="btn btn-sm btn-outline--primary"><i
           class="las la-plus"></i>@lang('Add New')</a>
@endpush
