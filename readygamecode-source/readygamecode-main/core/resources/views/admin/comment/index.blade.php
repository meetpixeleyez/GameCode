@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card responsive-filter-card mb-4">
                <div class="card-body">
                    <form>
                        <div class="d-flex flex-wrap gap-4">
                            <div class="flex-grow-1">
                                <label>@lang('Comment Text')</label>
                                <input type="search" name="search" value="{{ request()->search }}" class="form-control"
                                    placeholder="@lang('Search by comment text, username, product title')">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Type')</label>
                                <select name="is_reported" class="form-control select2"
                                    data-minimum-results-for-search="-1">
                                    <option value="">@lang('All')</option>
                                    <option value="1" @selected(request()->is_reported == '1')>@lang('Reported')</option>
                                    <option value="0" @selected(request()->is_reported == '0')>@lang('Non-reported')</option>
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Date')</label>
                                <input name="date" type="search"
                                    class="datepicker-here form-control bg--white pe-2 date-range"
                                    placeholder="@lang('Start Date - End Date')" autocomplete="off" value="{{ request()->date }}">
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i>
                                    @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Product')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Commented At')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($comments as $comment)
                                    <tr>
                                        <td>{{ __(@$comment->product->title) }}</td>
                                        <td>
                                            <span class="fw-bold">{{ __(@$comment->user->fullname) }}</span>
                                            <br>
                                            <span class="small">
                                                <a
                                                    href="{{ route('admin.users.detail', $comment->user->id) }}"><span>@</span>{{ $comment->user->username }}</a>
                                            </span>
                                        </td>
                                        <td>
                                            @if ($comment->is_reported)
                                                <span class="badge badge--warning">@lang('Reported')</span>
                                            @else
                                                <span class="badge badge--success">@lang('Not Reported')</span>
                                            @endif
                                        </td>
                                        <td>{{ showDateTime($comment->created_at) }}</td>
                                        <td>
                                            @if ($comment->is_reported)
                                                <a href="{{ route('admin.comment.details', $comment->id) }}"
                                                    class="btn btn-sm btn-outline--primary me-2">
                                                    <i class="las la-eye"></i>
                                                    @lang('Report Details')
                                                </a>
                                            @endif
                                            <div class="btn-group dropend">
                                                <button class="btn btn-sm btn-outline--info" data-bs-toggle="dropdown"
                                                    type="button" aria-expanded="false">
                                                    <i class="la la-ellipsis-v"></i>@lang('More')
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item view-btn"
                                                        data-comment="{{ __(@$comment->text) }}">
                                                        <i class="las la-eye"></i>
                                                        @lang('View')
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.comment.replies.index', $comment->id) }}">
                                                        <i class="las la-comment-dots"></i>
                                                        @lang('Replies')
                                                    </a>
                                                    <a class="dropdown-item confirmationBtn"
                                                        data-question="@lang('Are you sure to delete this comment?')"
                                                        data-action="{{ route('admin.comment.destroy', $comment->id) }}">
                                                        <i class="la la-trash"></i> @lang('Delete')
                                                    </a>
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
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($comments->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($comments) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="viewModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Comment')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="comment"></p>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"

            $('.view-btn').on('click', function() {
                const comment = $(this).data('comment');
                const modal = $('#viewModal');
                modal.find('p.comment').text(comment);
                modal.modal('show');
            });

            const datePicker = $('.date-range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                },
                showDropdowns: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 15 Days': [moment().subtract(14, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
                        .endOf('month')
                    ],
                    'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                },
                maxDate: moment()
            });
            const changeDatePickerText = (event, startDate, endDate) => {
                $(event.target).val(startDate.format('MMMM DD, YYYY') + ' - ' + endDate.format('MMMM DD, YYYY'));
            }


            $('.date-range').on('apply.daterangepicker', (event, picker) => changeDatePickerText(event, picker
                .startDate, picker.endDate));


            if ($('.date-range').val()) {
                let dateRange = $('.date-range').val().split(' - ');
                $('.date-range').data('daterangepicker').setStartDate(new Date(dateRange[0]));
                $('.date-range').data('daterangepicker').setEndDate(new Date(dateRange[1]));
            }

        })(jQuery)
    </script>
@endpush
