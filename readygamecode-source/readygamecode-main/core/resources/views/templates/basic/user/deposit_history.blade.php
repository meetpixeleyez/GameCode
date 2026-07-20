@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center gy-3">
        <div class="col-12">
            <div class="d-flex align-items justify-content-between flex-wrap gap-2">
                <h6 class="mb-0">@lang('Payment History')</h6>
                <x-search-form inputClass="form--control form--control--sm search" btn="btn--base btn--sm"
                    placeholder="Search by transactions" />
            </div>
        </div>
        <div class="col-md-12">
            <div class="card custom--card">
                <div class="card-body p-0">
                    @if ($deposits->count() == 0)
                        <x-empty-list title="No Transactions" />
                    @else
                        <div class="table-responsive">
                            <table class="table table--responsive--md">
                                <thead>
                                    <tr>
                                        <th>@lang('Gateway | Transaction')</th>
                                        <th class="text-center">@lang('Initiated')</th>
                                        <th class="text-center">@lang('Amount')</th>
                                        <th class="text-center">@lang('Conversion')</th>
                                        <th class="text-center">@lang('Status')</th>
                                        <th>@lang('Details')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($deposits as $deposit)
                                        <tr>
                                            <td>
                                                <div>
                                                    <span class="fw-bold"> <span
                                                            class="text-primary">{{ __($deposit->gateway ? $deposit->gateway?->name : 'Account Balance') }}</span>
                                                    </span>
                                                    <br>
                                                    <small> {{ $deposit->trx }} </small>
                                                </div>
                                            </td>

                                            <td class="text-end text-md-center">
                                                <div>
                                                    {{ showDateTime($deposit->created_at) }}<br>{{ diffForHumans($deposit->created_at) }}
                                                </div>
                                            </td>
                                            <td class="text-end text-md-center">
                                                <div>
                                                    {{ showAmount($deposit->amount) }} + <span class="text-danger"
                                                        title="@lang('charge')">{{ showAmount($deposit->charge) }} </span>
                                                    <br>
                                                    <strong title="@lang('Amount with charge')">
                                                        {{ showAmount($deposit->amount + $deposit->charge) }}
                                                    </strong>
                                                </div>
                                            </td>
                                            <td class="text-end text-md-center">
                                                <div>
                                                    1 {{ __(gs('cur_text')) }} =
                                                    {{ showAmount($deposit->gateway ? $deposit->rate : 1, currencyFormat: false) }}
                                                    {{ __($deposit->method_currency) }}
                                                    <br>
                                                    <strong>{{ showAmount($deposit->final_amount, currencyFormat: false) }}
                                                        {{ __($deposit->method_currency) }}</strong>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @php echo $deposit->statusBadge @endphp
                                            </td>
                                            @php
                                                $details =
                                                    $deposit->detail != null ? json_encode($deposit->detail) : null;
                                            @endphp
                                            <td>
                                                @if ($deposit->method_code >= 1000 && $deposit->method_code <= 5000)
                                                    <a href="javascript:void(0)" class="btn btn--base btn--sm detailBtn"
                                                        data-info="{{ $details }}"
                                                        @if ($deposit->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $deposit->admin_feedback }}" @endif>
                                                        <i class="fas fa-desktop"></i>
                                                    </a>
                                                @else
                                                    <button type="button" class="btn btn--success btn--sm"
                                                        data-bs-toggle="tooltip" title="@lang('Automatically processed')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                @if ($deposits->hasPages())
                    <div class="pt-30">
                        <div class="card-footer">
                            {{ paginateLinks($deposits) }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- APPROVE MODAL --}}
    <div id="detailModal" class="modal fade custom--modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <ul class="list-group userData mb-2">
                    </ul>
                    <div class="feedback"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark btn-sm" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');

                var userData = $(this).data('info');

                var html = '';
                if (userData) {
                    userData.forEach(element => {
                        if (element.type != 'file') {
                            html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${element.name}</span>
                                <span">${element.value}</span>
                            </li>`;
                        }
                    });
                }

                modal.find('.userData').html(html);

                if ($(this).data('admin_feedback') != undefined) {
                    var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
                } else {
                    var adminFeedback = '';
                }

                modal.find('.feedback').html(adminFeedback);

                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
