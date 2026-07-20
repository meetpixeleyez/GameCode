@extends('admin.layouts.app')

@section('panel')
    <div class="row gy-4">

        <div class="col-xxl-3 col-sm-6">

            <x-widget style="6" link="{{ route('admin.users.all') }}" icon="las la-users" title="Total Users"
                      value="{{ $widget['total_users'] }}" bg="primary" />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.users.active') }}" icon="las la-user-check" title="Active Users"
                      value="{{ $widget['verified_users'] }}" bg="success" />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.users.email.unverified') }}" icon="lar la-envelope"
                      title="Email Unverified Users" value="{{ $widget['email_unverified_users'] }}" bg="danger" />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.users.mobile.unverified') }}" icon="las la-comment-slash"
                      title="Mobile Unverified Users" value="{{ $widget['mobile_unverified_users'] }}" bg="warning" />
        </div><!-- dashboard-w1 end -->
    </div><!-- row end-->

    <div class="row mt-2 gy-4">
        <div class="col-xxl-6">
            <div class="card box-shadow3 h-100">
                <div class="card-body">
                    <h5 class="card-title">@lang('Deposits')</h5>
                    <div class="widget-card-wrapper">

                        <div class="widget-card bg--success">
                            <a href="{{ route('admin.deposit.list') }}" class="widget-card-link"></a>
                            <div class="widget-card-left">
                                <div class="widget-card-icon">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h6 class="widget-card-amount">{{ showAmount($deposit['total_deposit_amount']) }}</h6>
                                    <p class="widget-card-title">@lang('Total Deposited')</p>
                                </div>
                            </div>
                            <span class="widget-card-arrow">
                                <i class="las la-angle-right"></i>
                            </span>
                        </div>

                        <div class="widget-card bg--warning">
                            <a href="{{ route('admin.deposit.pending') }}" class="widget-card-link"></a>
                            <div class="widget-card-left">
                                <div class="widget-card-icon">
                                    <i class="fas fa-spinner"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h6 class="widget-card-amount">{{ $deposit['total_deposit_pending'] }}</h6>
                                    <p class="widget-card-title">@lang('Pending Deposits')</p>
                                </div>
                            </div>
                            <span class="widget-card-arrow">
                                <i class="las la-angle-right"></i>
                            </span>
                        </div>

                        <div class="widget-card bg--danger">
                            <a href="{{ route('admin.deposit.rejected') }}" class="widget-card-link"></a>
                            <div class="widget-card-left">
                                <div class="widget-card-icon">
                                    <i class="fas fa-ban"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h6 class="widget-card-amount">{{ $deposit['total_deposit_rejected'] }}</h6>
                                    <p class="widget-card-title">@lang('Rejected Deposits')</p>
                                </div>
                            </div>
                            <span class="widget-card-arrow">
                                <i class="las la-angle-right"></i>
                            </span>
                        </div>

                        <div class="widget-card bg--primary">
                            <a href="{{ route('admin.deposit.list') }}" class="widget-card-link"></a>
                            <div class="widget-card-left">
                                <div class="widget-card-icon">
                                    <i class="fas fa-percentage"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h6 class="widget-card-amount">{{ showAmount($deposit['total_deposit_charge']) }}</h6>
                                    <p class="widget-card-title">@lang('Deposited Charge')</p>
                                </div>
                            </div>
                            <span class="widget-card-arrow">
                                <i class="las la-angle-right"></i>
                            </span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-6">
            <div class="card box-shadow3 h-100">
                <div class="card-body">
                    <h5 class="card-title">@lang('Withdrawals')</h5>
                    <div class="widget-card-wrapper">
                        <div class="widget-card bg--success">
                            <a href="{{ route('admin.withdraw.data.all') }}" class="widget-card-link"></a>
                            <div class="widget-card-left">
                                <div class="widget-card-icon">
                                    <i class="lar la-credit-card"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h6 class="widget-card-amount">{{ showAmount($withdrawals['total_withdraw_amount']) }}
                                    </h6>
                                    <p class="widget-card-title">@lang('Total Withdrawn')</p>
                                </div>
                            </div>
                            <span class="widget-card-arrow">
                                <i class="las la-angle-right"></i>
                            </span>
                        </div>

                        <div class="widget-card bg--warning">
                            <a href="{{ route('admin.withdraw.data.pending') }}" class="widget-card-link"></a>
                            <div class="widget-card-left">
                                <div class="widget-card-icon">
                                    <i class="fas fa-spinner"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h6 class="widget-card-amount">{{ $withdrawals['total_withdraw_pending'] }}</h6>
                                    <p class="widget-card-title">@lang('Pending Withdrawals')</p>
                                </div>
                            </div>
                            <span class="widget-card-arrow">
                                <i class="las la-angle-right"></i>
                            </span>
                        </div>

                        <div class="widget-card bg--danger">
                            <a href="{{ route('admin.withdraw.data.rejected') }}" class="widget-card-link"></a>
                            <div class="widget-card-left">
                                <div class="widget-card-icon">
                                    <i class="las la-times-circle"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h6 class="widget-card-amount">{{ $withdrawals['total_withdraw_rejected'] }}</h6>
                                    <p class="widget-card-title">@lang('Rejected Withdrawals')</p>
                                </div>
                            </div>
                            <span class="widget-card-arrow">
                                <i class="las la-angle-right"></i>
                            </span>
                        </div>

                        <div class="widget-card bg--primary">
                            <a href="{{ route('admin.withdraw.data.all') }}" class="widget-card-link"></a>
                            <div class="widget-card-left">
                                <div class="widget-card-icon">
                                    <i class="las la-percent"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h6 class="widget-card-amount">{{ showAmount($withdrawals['total_withdraw_charge']) }}
                                    </h6>
                                    <p class="widget-card-title">@lang('Withdrawal Charge')</p>
                                </div>
                            </div>
                            <span class="widget-card-arrow">
                                <i class="las la-angle-right"></i>
                            </span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row gy-4 mt-2">
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="7" link="{{ route('admin.sell.items') }}" icon="las la-check-double f-size--56"
                      title="Sales Amount" value="{{ showAmount($widget['total_sale_amount']) }}" bg="success" />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="7" link="{{ route('admin.report.transaction') }}?remark=seller_fee"
                      icon="la la-percentage f-size--56" title="Seller Charge"
                      value="{{ showAmount($widget['total_seller_charge']) }}" bg="warning" />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="7" link="{{ route('admin.report.transaction') }}" icon="la la-percentage f-size--56"
                      title="Buyer Charge" value="{{ showAmount($widget['total_buyer_charge']) }}" bg="warning" />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="7" link="{{ route('admin.sell.refunded') }}" icon="las la-redo-alt f-size--56"
                      title="Total Refunded" value="{{ showAmount($widget['total_refunded']) }}" bg="danger" />
        </div><!-- dashboard-w1 end -->
    </div><!-- row end-->

    <div class="row gy-4 mt-2">
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.deposit.list') }}" icon="fas fa-hand-holding-usd"
                      title="Total Payment" value="{{ showAmount($deposit['total_deposit_amount']) }}" bg="success"
                      outline="true" />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.deposit.pending') }}" icon="fas fa-spinner"
                      title="Pending Payments " value="{{ $deposit['total_deposit_pending'] }}" bg="warning"
                      outline="true" />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.deposit.rejected') }}" icon="fas fa-ban"
                      title="Rejected Payments" value="{{ $deposit['total_deposit_rejected'] }}" bg="danger"
                      outline="true" />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.deposit.list') }}" icon="fas fa-percentage"
                      title="Paid Charge" value="{{ showAmount($deposit['total_deposit_charge']) }}" bg="primary"
                      outline="true" />
        </div><!-- dashboard-w1 end -->
    </div><!-- row end-->


    <div class="row mb-none-30 mt-30">
        <div class="col-xxl-5 col-xl-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between">
                        <h5 class="card-title">@lang('Top Selling Items')</h5>

                        <div class="mb-2">
                            <select class="time-period form-control">
                                <option value="all">@lang('All Time')</option>
                                <option value="4">@lang('This Month')</option>
                                <option value="1">@lang('Last 1 Month')</option>
                                <option value="2">@lang('Last 6 Months')</option>
                                <option value="3">@lang('Last 1 Year')</option>
                            </select>
                        </div>
                    </div>
                    <div class="top-sell-item">
                        <ul class="list-group list-group-flush">
                            @forelse ($topSellingItems as $product)
                                <li class="list-group-item d-flex flex-wrap align-items-center px-2">
                                    <a href="{{ route('admin.product.details', $product->slug) }}"
                                       class="top-sell-item__thumb" title="{{ __($product->title) }}">
                                        <img class="top-sell-item__thumb-img"
                                             src="{{ getImage(getFilePath('productInlinePreview') . productFilePath($product, 'inline_preview_image'), getFileSize('productInlinePreview')) }}"
                                             alt="@lang('Product Image')">
                                    </a>
                                    <div class="top-sell-item__content">
                                        <a href="{{ route('admin.product.details', $product->slug) }}"
                                           class="top-sell-item__title d-block">
                                            {{ __(Str::limit($product->title, 50, '...')) }}
                                        </a>
                                        <span class="top-sell-item__desc d-block">@lang('Sales')
                                            ({{ $product->total_sold }})
                                        </span>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item d-flex flex-wrap align-items-center px-2">
                                    <span class="top-sell-item__title d-block">@lang('No data found')</span>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-7 col-xl-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <h5 class="card-title mb-0">@lang('Sales Statistics')</h5>
                        <div>
                            <select class="form-control sale-period">
                                <option value="1_month">@lang('Last 1 Month')</option>
                                <option value="today">@lang('Today')</option>
                                <option value="7_days">@lang('Last 7 Days')</option>
                                <option value="15_days">@lang('Last 15 Days')</option>
                                <option value="6_months">@lang('Last 6 Months')</option>
                                <option value="1_year">@lang('Last 1 Year')</option>
                            </select>
                        </div>
                    </div>
                    <div id="salesChart"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-none-30 mt-30">
        <div class="col-xl-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between">
                        <h5 class="card-title">@lang('Deposit & Withdraw Report')</h5>

                        <div id="dwDatePicker" class="border p-1 cursor-pointer rounded">
                            <i class="la la-calendar"></i>&nbsp;
                            <span></span> <i class="la la-caret-down"></i>
                        </div>
                    </div>
                    <div id="dwChartArea"> </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between">
                        <h5 class="card-title">@lang('Transactions Report')</h5>

                        <div id="trxDatePicker" class="border p-1 cursor-pointer rounded">
                            <i class="la la-calendar"></i>&nbsp;
                            <span></span> <i class="la la-caret-down"></i>
                        </div>
                    </div>

                    <div id="transactionChartArea"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-none-30 mt-5">
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <h5 class="card-title">@lang('Login By Browser') (@lang('Last 30 days'))</h5>
                    <canvas id="userBrowserChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Login By OS') (@lang('Last 30 days'))</h5>
                    <canvas id="userOsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Login By Country') (@lang('Last 30 days'))</h5>
                    <canvas id="userCountryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    @include('admin.partials.cron_modal')
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-outline--primary btn-sm" data-bs-toggle="modal" data-bs-target="#cronModal">
        <i class="las la-server"></i>@lang('Cron Setup')
    </button>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/chart.js.2.8.0.js') }}"></script>
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/charts.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush

@push('script')
    <script>
        "use strict";

        const start = moment().subtract(14, 'days');
        const end = moment();

        const dateRangeOptions = {
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 15 Days': [moment().subtract(14, 'days'), moment()],
                'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                    'month')],
                'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
            },
            maxDate: moment()
        }

        const changeDatePickerText = (element, startDate, endDate) => {
            $(element).html(startDate.format('MMMM D, YYYY') + ' - ' + endDate.format('MMMM D, YYYY'));
        }

        let dwChart = barChart(
            document.querySelector("#dwChartArea"),
            @json(__(gs('cur_text'))),
            [{
                    name: 'Deposited',
                    data: []
                },
                {
                    name: 'Withdrawn',
                    data: []
                }
            ],
            [],
        );

        let trxChart = lineChart(
            document.querySelector("#transactionChartArea"),
            [{
                    name: "Plus Transactions",
                    data: []
                },
                {
                    name: "Minus Transactions",
                    data: []
                }
            ],
            []
        );


        const depositWithdrawChart = (startDate, endDate) => {

            const data = {
                start_date: startDate.format('YYYY-MM-DD'),
                end_date: endDate.format('YYYY-MM-DD')
            }

            const url = @json(route('admin.chart.deposit.withdraw'));

            $.get(url, data,
                function(data, status) {
                    if (status == 'success') {
                        dwChart.updateSeries(data.data);
                        dwChart.updateOptions({
                            xaxis: {
                                categories: data.created_on,
                            }
                        });
                    }
                }
            );
        }

        const transactionChart = (startDate, endDate) => {

            const data = {
                start_date: startDate.format('YYYY-MM-DD'),
                end_date: endDate.format('YYYY-MM-DD')
            }

            const url = @json(route('admin.chart.transaction'));

            $.get(url, data,
                function(data, status) {
                    if (status == 'success') {

                        trxChart.updateSeries(data.data);
                        trxChart.updateOptions({
                            xaxis: {
                                categories: data.created_on,
                            }
                        });
                    }
                }
            );
        }

        $(document).on('change', '.time-period', function() {
            const timePeriod = $(this).val();

            $.ajax({
                url: '{{ route('admin.top.selling.items') }}',
                method: 'GET',
                data: {
                    timePeriod: timePeriod
                },
                beforeSend: function() {
                    $('.top-sell-item').html(
                        '<div class="loading"><img src="{{ asset($activeTemplateTrue . 'images/beforeloader.gif') }}"></div>'
                    );
                },
                success: function(response) {

                    $('.top-sell-item').html(response.content);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        });


        document.addEventListener("DOMContentLoaded", function() {
            const chartContainer = document.querySelector("#salesChart");
            const timePeriodSelect = document.querySelector(".sale-period");

            function fetchAndRenderChart(timePeriod = '1_month') {
                fetch(`{{ route('admin.sales.chart.data') }}?timePeriod=${timePeriod}`)
                    .then(response => response.json())
                    .then(data => {
                        const options = {
                            chart: {
                                type: 'line',
                                height: 350,
                            },
                            series: data.series,
                            xaxis: {
                                categories: data.categories,
                                title: {
                                    text: timePeriod === '1_year' ? 'Month' : 'Date',
                                },
                            },
                            yaxis: {
                                title: {
                                    text: 'Total Sales',
                                },
                            },
                            title: {
                                text: `Sales Report (${timePeriod.replace('_', ' ').toUpperCase()})`,
                                align: 'center',
                            },
                        };

                        if (chartContainer.innerHTML !== "") {
                            chartContainer.innerHTML = "";
                        }

                        const chart = new ApexCharts(chartContainer, options);
                        chart.render();
                    })
                    .catch(error => console.error('Error fetching sales data:', error));
            }

            fetchAndRenderChart();

            timePeriodSelect.addEventListener("change", function() {
                const selectedPeriod = this.value;
                fetchAndRenderChart(selectedPeriod);
            });
        });




        $('#dwDatePicker').daterangepicker(dateRangeOptions, (start, end) => changeDatePickerText('#dwDatePicker span',
            start, end));
        $('#trxDatePicker').daterangepicker(dateRangeOptions, (start, end) => changeDatePickerText('#trxDatePicker span',
            start, end));

        changeDatePickerText('#dwDatePicker span', start, end);
        changeDatePickerText('#trxDatePicker span', start, end);

        depositWithdrawChart(start, end);
        transactionChart(start, end);

        $('#dwDatePicker').on('apply.daterangepicker', (event, picker) => depositWithdrawChart(picker.startDate, picker
            .endDate));
        $('#trxDatePicker').on('apply.daterangepicker', (event, picker) => transactionChart(picker.startDate, picker
            .endDate));

        piChart(
            document.getElementById('userBrowserChart'),
            @json(@$chart['user_browser_counter']->keys()),
            @json(@$chart['user_browser_counter']->flatten())
        );

        piChart(
            document.getElementById('userOsChart'),
            @json(@$chart['user_os_counter']->keys()),
            @json(@$chart['user_os_counter']->flatten())
        );

        piChart(
            document.getElementById('userCountryChart'),
            @json(@$chart['user_country_counter']->keys()),
            @json(@$chart['user_country_counter']->flatten())
        );
    </script>
@endpush
@push('style')
    <style>
        .apexcharts-menu {
            min-width: 120px !important;
        }

        .style {
            width: 50px;
            height: 50px;
        }

        .top-sell-item ul {
            height: 357px;
            overflow-y: auto;
            padding: 0px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

        .top-sell-item ul::-webkit-scrollbar {
            width: 4px !important;
            height: 4px;
            border-radius: 4px;
            background: #edeef3;
        }

        .top-sell-item ul::-webkit-scrollbar-thumb {
            width: 4px !important;
            height: 4px;
            border-radius: 4px;
            background: #dedee3;
        }

        .top-sell-item li.list-group-item {
            border: 0;
            border-bottom: 1px solid rgba(108, 117, 125, 0.15);
        }

        .top-sell-item li.list-group-item:last-child {
            border-bottom: 0;
        }

        .top-sell-item__thumb {
            height: 50px;
            width: 50px;
            border-radius: 3px;
            overflow: hidden;
        }

        .top-sell-item__thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .top-sell-item__content {
            width: calc(100% - 50px);
            padding-left: 15px;
        }

        .top-sell-item__title {
            font-size: 0.875rem;
            font-weight: 500;
            color: hsl(var(--heading-color));
        }

        .top-sell-item__desc {
            font-size: 0.8125rem;
        }

        @media (max-width: 767px) {
            .top-sell-item__thumb {
                height: 44px;
                width: 44px;
            }

            .top-sell-item__content {
                width: calc(100% - 44px);
                padding-left: 12px;
            }
        }

        @media (max-width: 575px) {
            .top-sell-item__thumb {
                height: 40px;
                width: 40px;
            }

            .top-sell-item__content {
                width: calc(100% - 40px);
                padding-left: 10px;
            }
        }

        .apexcharts-reset-icon,
        .apexcharts-zoomin-icon,
        .apexcharts-zoomout-icon,
        .apexcharts-pan-icon,
        .apexcharts-zoom-icon {
            display: none
        }

        .loading {
            text-align: center;
            height: 357px;
            align-content: center
        }

        .loading img {
            max-height: 120px;
        }
    </style>
@endpush
