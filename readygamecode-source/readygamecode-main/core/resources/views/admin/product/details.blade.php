@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-5 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="m-0">{{ __($product->title) }}</h5>
                            @if ($product->audio_temp_file && @$product->category->file_type == 'audio' && in_array('mp3', $product->category->preview_file_types ?? []))
                                <div class="audio-player-wrapper">
                                    <div class="d-flex align-items-center gap-1 audio-player-left">
                                        <button id="play-button" class="play-button">
                                            <i class="fas fa-play"></i>
                                        </button>
                                        <span id="current-time">00:00</span>
                                    </div>

                                    <div class="audio-player-middle"
                                         data-file-path="{{ asset(getFilePath('previewFile')) . '/' . productFilePath($product, 'temp_audio_file') . '/' . $product->audio_temp_file }}"
                                         id="waveform"></div>

                                    <div class="audio-player-time">
                                        <span id="total-time">00:00</span>
                                    </div>
                                </div>
                            @else
                                <div class="image-upload mt-3">
                                    <img src="{{ getImage(getFilePath('productPreview') . productFilePath($product, 'preview_image')) }}"
                                         alt="@lang('Product Preview')" />
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Category')</span>
                                    <span>{{ __(@$product->category->name) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Subcategory')</span>
                                    <span>{{ __(@$product->subCategory->name) }}</span>
                                </li>
                                @if (!$product->is_free)
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                        <span class="fw-bold">@lang('Price')(@lang('Regular License'))</span>
                                        <span>{{ showAmount($product->price) }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                        <span class="fw-bold">@lang('Price')(@lang('Commercial License'))</span>
                                        <span>{{ showAmount($product->price_cl) }}</span>
                                    </li>
                                @else
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                        <span class="fw-bold">@lang('Price')</span>
                                        <span class="badge badge--success">@lang('Free')</span>
                                    </li>
                                @endif
                                
                                <!-- Additional Services Prices -->
                                @if ($product->reskin_price > 0 || $product->publish_price > 0 || $product->store_optimization_price > 0)
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                        <span class="fw-bold">@lang('Additional Services')</span>
                                        <div class="text-end">
                                            @if ($product->reskin_price > 0)
                                                <div class="mb-1">
                                                    <small class="text-muted">@lang('Reskin'):</small>
                                                    <span class="badge badge--info">{{ showAmount($product->reskin_price) }}</span>
                                                </div>
                                            @endif
                                            @if ($product->publish_price > 0)
                                                <div class="mb-1">
                                                    <small class="text-muted">@lang('Publish'):</small>
                                                    <span class="badge badge--info">{{ showAmount($product->publish_price) }}</span>
                                                </div>
                                            @endif
                                            @if ($product->store_optimization_price > 0)
                                                <div class="mb-1">
                                                    <small class="text-muted">@lang('Store Optimization'):</small>
                                                    <span class="badge badge--info">{{ showAmount($product->store_optimization_price) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </li>
                                @endif
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Status')</span>
                                    <?php echo $product->statusBadge; ?>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Demo')</span>
                                    @if (@$product->category->file_type == 'audio')
                                        <a href="{{ asset(getFilePath('previewFile')) . '/' . productFilePath($product, 'temp_audio_file') . '/' . $product->audio_temp_file }}" download="">@lang('Download Preview')</a>
                                    @else
                                        <a href="{{ $product->demo_url }}" target="_blank">
                                            {{ @$product->demo_url }}
                                        </a>
                                    @endif
                                </li>
                                @foreach ($product->attribute_info as $info)
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                        <span class="fw-bold">{{ __($info->name) }}</span>
                                        @if (is_array($info->value))
                                            <div>
                                                @foreach ($info->value as $val)
                                                    <span>{{ __($val) }}</span>
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            </div>
                                        @else
                                            <span>{{ __(@$info->value) }}</span>
                                        @endif
                                    </li>
                                @endforeach
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Tags')</span>
                                    <div>
                                        @forelse ($product->tags ?? [] as $tag)
                                            <span class="badge badge--primary mb-2">{{ __($tag) }}</span>
                                        @empty
                                            <span class="text-secondary">@lang('No Tags')</span>
                                        @endforelse
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Published Date')</span>
                                    <div>
                                        @if (!$product->status == Status::PRODUCT_APPROVED)
                                            <span>{{ showDateTime($product->created_at) }}</span>
                                        @else
                                            <span>{{ showDateTime($product->published_at) }}</span>
                                        @endif
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="fw-bold">@lang('Last Update')</span>
                                    <div>
                                        @if ($product->last_updated == null)
                                            <span class="text-secondary">@lang('Not updated yet')</span>
                                        @else
                                            <span>{{ showDateTime($product->last_updated) }}</span>
                                        @endif
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />

    {{-- reject modal --}}
    <div id="rejectModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Confirmation Alert!')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="reason" class="form-label">@lang('Reason for rejection')</label>
                            <textarea name="reason" id="reason" class="form--control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.rejectBtn').on('click', function() {
                var modal = $('#rejectModal');
                modal.find('.modal-title').text($(this).data('title'));
                modal.find('form').attr('action', $(this).data('action'));
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush


@push('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/vendor/magnific-popup.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/wavesurfer.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/vendor/jquery.magnific-popup.min.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            $(document).ready(function() {

                if ($('#waveform').length) {
                    var wavesurfer = WaveSurfer.create({
                        container: '#waveform',
                        waveColor: 'rgba(0, 0, 0, 0.7)',
                        progressColor: '#4634FF',
                        backend: 'MediaElement',
                        audioContext: new(window.AudioContext || window.webkitAudioContext)(),
                        height: 100,
                        barWidth: 2
                    });

                    var audioElement = $('#audio')[0];

                    wavesurfer.load(
                        '{{ asset(getFilePath('previewFile') . productFilePath($product, 'temp_audio_file') . $product->audio_temp_file) }}'
                    );


                    $('#play-button').on('click', function() {
                        if (wavesurfer.isPlaying()) {
                            wavesurfer.pause();
                            $(this).html('<i class="fas fa-play"></i>');
                        } else {
                            wavesurfer.play();
                            $(this).html('<i class="fas fa-pause"></i>');
                        }
                    });

                    $(audioElement).on('play', function() {
                        wavesurfer.play();
                        $('#play-button').html('<i class="fas fa-pause"></i>');
                    });

                    $(audioElement).on('pause', function() {
                        wavesurfer.pause();
                        $('#play-button').html('<i class="fas fa-play"></i>');
                    });

                    $(audioElement).on('seeked', function() {
                        wavesurfer.seekTo(audioElement.currentTime / audioElement.duration);
                    });

                    wavesurfer.on('finish', function() {
                        $('#play-button').html('<i class="fas fa-play"></i>');
                    });

                    wavesurfer.on('ready', function() {
                        var totalTime = wavesurfer.getDuration();
                        $('#total-time').text(formatTime(totalTime));
                    });

                    wavesurfer.on('audioprocess', function() {
                        var currentTime = wavesurfer.getCurrentTime();
                        $('#current-time').text(formatTime(currentTime));
                    });

                    function formatTime(seconds) {
                        var minutes = Math.floor(seconds / 60);
                        var seconds = Math.floor(seconds % 60);
                        return (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
                    }
                }

            });

            $('#showPreviewVideo').on('click', function(event) {
                event.preventDefault();

                $('#previewVideo').magnificPopup({
                    delegate: 'a',
                    type: 'iframe',
                    gallery: {
                        enabled: true
                    }
                }).magnificPopup('open');
            });

        })
        (jQuery);
    </script>
@endpush


@push('style')
    <style>
        .audio-player-wrapper {
            min-height: 163px !important;
        }

        .audio-card .product-card__thumb::before {
            display: none;
        }


        .list-view .audio-player-wrapper {
            min-height: 163px !important;
            width: 306px;
        }

        .audio-player-wrapper {
            min-height: 163px !important;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0px 10px;
        }

        .audio-player-wrapper .play-button {
            width: 30px;
            height: 30px;
            background-color: #1801ff !important;
            border-radius: 3px;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            flex-shrink: 0;
        }

        .audio-player-left {
            width: 80px;
            flex-shrink: 0;
            font-size: 12px;
        }

        .audio-player-time {
            width: 50px;
            font-size: 12px;
            flex-shrink: 0;
        }

        .audio-player-middle {
            width: 100%;
        }

        .product-card.audio-card .product-card__thumb {
            border: 1px solid hsl(var(--border-color) / 0.45);
            border-bottom: none;
            border-radius: 8px 8px 0px 0px;
        }


        .product-details__inner.audio-card .product-details__thumb {
            background: #fff;
            border-radius: 12px;
        }

        .product-details__inner.audio-card .audio-player-wrapper {
            border: 1px solid hsl(var(--border-color));
            border-radius: 12px 12px 0px 0px;
            overflow: hidden;
        }


        .product-details__inner.audio-card .audio-player-wrapper.border-radius-add {
            border-radius: 12px;
        }
    </style>
@endpush
