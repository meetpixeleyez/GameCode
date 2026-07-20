<div id="screenshotsGallery" class="hidden">
    @foreach ($product->screenshots() as $screenshotPath)
        <a href="{{ getImage($screenshotPath) }}">@lang('Image')</a>
    @endforeach
</div>

<div id="previewVideo" class="mfp-hide">
    <div class="video-popup-wrapper">
        <button title="Close (Esc)" type="button" class="mfp-close">×</button>
        <video id="plyr-preview-player" playsinline controls>
            <source src="{{ getImage(getFilePath('previewVideo') . '/' . productFilePath($product, 'preview_video')) }}" type="video/mp4" />
        </video>
    </div>
</div>

<div class="product-details__inner @if ($product->audio_temp_file) audio-card @endif">
    <div class="product-details__thumb">
        @if ($product->audio_temp_file && in_array('mp3', $product->category->preview_file_types))
            <div class="audio-player-wrapper  @if ($product->demo_url == null && $product->category->file_type == 'audio') border-radius-add @endif">
                <div class="d-flex align-items-center gap-1 audio-player-left">
                    <button id="play-button-{{ $product->id }}" class="play-button">
                        <i class="fas fa-play"></i>
                    </button>
                    <span id="current-time-{{ $product->id }}">00:00</span>
                </div>

                <div class="audio-player-middle"
                     data-file-path="{{ asset(getFilePath('previewFile')) . '/' . productFilePath($product, 'temp_audio_file') . '/' . $product->audio_temp_file }}"
                     id="waveform-{{ $product->id }}"></div>

                <div class="audio-player-time">
                    <span id="total-time-{{ $product->id }}">00:00</span>
                </div>
            </div>
        @else
            <img src="{{ getImage(getFilePath('productPreview') . '/' . productFilePath($product, 'preview_image'), getFileSize('productPreview')) }}"
                 alt="@lang('Product Image')" />
        @endif
        <div class="product-details__buttons">
            @if ($product->category->file_type !== 'audio')
                @if ($product->demo_url)
                    <a href="{{ $product->demo_url }}" target="_blank" class="btn btn--base">@lang('Download APK')</a>
                @endif
            @else
                <a href="{{ asset(getFilePath('previewFile')) . '/' . productFilePath($product, 'temp_audio_file') . '/' . $product->audio_temp_file }}" download="" class="btn btn--base">@lang('Download Preview')</a>
            @endif
            @if ($product->category->file_type !== 'audio')
                <a href="#" id="showScreenshots" class="btn btn-outline--base">@lang('Screenshots')</a>
                <!--@if ($product->preview_video)-->
                <!--    <a href="#previewVideo" id="showPreviewVideo" class="btn btn-outline--base open-video">-->
                <!--        <i class="las la-play"></i> @lang('Preview Video')-->
                <!--    </a>-->
                <!--@endif-->
            @endif
            
            <!-- Preview Button -->
            @if($product->preview_video)
                <button type="button" class="btn btn-outline--base" data-bs-toggle="modal" data-bs-target="#videoModal">
                     <i class="las la-play"></i> @lang('Preview Video')
                </button>
            @endif
            
            <!-- Modal -->
            <div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                  <div class="modal-body p-0">
                    <div class="ratio ratio-16x9">
                      <iframe id="youtubeFrame" src="" title="YouTube video" frameborder="0" allowfullscreen></iframe>
                    </div>
                  </div>
                </div>
              </div>
            </div>

        </div>
        @if ($product->isTrending())
            <span class="icon">
                @php
                    $trendingIconPath = base_path('../assets/images/trending.svg');
                @endphp
                {!! file_exists($trendingIconPath) ? file_get_contents($trendingIconPath) : '' !!}
            </span>
        @endif
    </div>
    @php
        $attributeInfoArray = [];
        if (is_object($product->attribute_info) || is_array($product->attribute_info)) {
            foreach ((array)$product->attribute_info as $item) {
                if (is_object($item)) {
                    $item = (array)$item;
                }
                if (isset($item['name'])) {
                    $attributeInfoArray[] = $item;
                }
            }
        }

        $platformItems = [];
        $featureItems = [];
        $systemRequirements = [];
        $softwareVersion = null;

        foreach ($attributeInfoArray as $attr) {
            $name = strtolower(trim($attr['name'] ?? ''));
            $value = $attr['value'] ?? '';

            if (in_array($name, ['platform', 'platforms', 'os', 'operating system', 'supported platforms', 'supported os'])) {
                $platformItems[] = $value;
            } elseif (str_contains($name, 'system requirement') || str_contains($name, 'system requirements') || str_contains($name, 'minimum requirement') || str_contains($name, 'recommended requirement')) {
                $systemRequirements[] = ['name' => $attr['name'], 'value' => $value];
            } elseif (in_array($name, ['features', 'feature list', 'game features', 'highlights', 'key features', 'feature'])) {
                $featureItems[] = $value;
            } elseif (in_array($name, ['version', 'software version'])) {
                $softwareVersion = $value;
            }
        }

        $platformList = collect($platformItems)
            ->flatMap(function ($item) {
                if (is_array($item)) {
                    return $item;
                }
                return array_filter(array_map('trim', explode(',', $item)));
            })
            ->unique()
            ->values()
            ->all();

        $featureList = collect($featureItems)
            ->flatMap(function ($item) {
                if (is_array($item)) {
                    return $item;
                }
                return array_filter(array_map('trim', preg_split('/[\r\n]+/', $item)));
            })
            ->unique()
            ->values()
            ->all();

        $screenshotCount = count($product->screenshots());
        $hasPreviewVideo = !empty(trim($product->preview_video ?? ''));
    @endphp

    <div class="product-details__content" style="padding: 20px;background-color: #fff;margin-top: 20px;border: 1px solid hsl(var(--border-color));border-radius: 12px;">
        <div class="product-details-item mb-4">
            <h5 class="mb-3">@lang('Game Overview')</h5>
            <hr/>
            <div class="row g-4">
                <div class="col-md-6">
                    <ul class="list-unstyled product-overview-list">
                        <li>
                            <span class="fw-semibold">@lang('Genre'):</span>
                            <span>{{ optional($product->category)->name ?: __('N/A') }}</span>
                        </li>
                        <li>
                            <span class="fw-semibold">@lang('Platform'):</span>
                            <span>{{ $platformList ? implode(', ', $platformList) : __('N/A') }}</span>
                        </li>
                        @if ($softwareVersion)
                            <li>
                                <span class="fw-semibold">@lang('Version'):</span>
                                <span>{{ $softwareVersion }}</span>
                            </li>
                        @endif
                        <li>
                            <span class="fw-semibold">@lang('Published'):</span>
                            <span>{{ showDateTime($product->published_at, 'd M Y') }}</span>
                        </li>
                        <li>
                            <span class="fw-semibold">@lang('Last Updated'):</span>
                            <span>{{ showDateTime($product->last_updated, 'd M Y') }}</span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    @if ($product->total_review)
                        <div class="product-details-summary">
                            <h6 class="mb-1">@lang('User Reviews')</h6>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <div class="rating-list mb-0">
                                    @php echo displayRating($product->avg_rating ?? 0); @endphp
                                </div>
                                <div class="p-t-5">
                                    <p class="mb-1 d-inline-block"><strong>{{ number_format($product->avg_rating ?: 0, 1) }}</strong> / 5</p>
                                    <p class="mb-0 text-muted d-inline-block">{{ __($product->total_review) }} @lang('reviews')</p>
                                </div>
                            </div>
                            <a href="{{ route('product.reviews', $product->slug) }}" class="text--base text-decoration-underline">@lang('Read all reviews')</a>
                        </div>
                    @endif

                    <div class="product-details-summary mt-3">
                        <h6 class="mb-2">@lang('Media')</h6>
                        <ul class="list-unstyled mb-0">
                            @if ($screenshotCount)
                                <li><strong>@lang('Screenshots'):</strong> {{ $screenshotCount }}</li>
                            @endif
                            @if ($hasPreviewVideo)
                                <li><strong>@lang('Preview Video'):</strong> @lang('Available')</li>
                            @endif
                            @if (!$screenshotCount && !$hasPreviewVideo)
                                <li class="text-muted">@lang('No additional media available')</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        @if ($featureList)
            <div class="product-details-item mb-4">
                <h5 class="mb-3">@lang('Key Features')</h5>
                <ul class="product-feature-list">
                    @foreach ($featureList as $feature)
                        <li>{{ $feature }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="product-details-item">
            @php echo html_entity_decode($product->description); @endphp
        </div>

        @if ($systemRequirements)
            <div class="product-details-item mb-4">
                <h5 class="mb-3">@lang('System Requirements')</h5>
                <div class="row">
                    @foreach ($systemRequirements as $requirement)
                        <div class="col-md-6 mb-3">
                            <h6 class="mb-1">{{ $requirement['name'] }}</h6>
                            <p class="mb-0">{!! nl2br(e($requirement['value'])) !!}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @php
            $productFaqItems = @$product->attribute_info->faq_items ?? [];
            if (is_object($productFaqItems)) {
                $productFaqItems = (array) $productFaqItems;
            }
            $productFaqItems = array_filter($productFaqItems, function ($item) {
                $question = is_object($item) ? ($item->question ?? '') : ($item['question'] ?? '');
                $answer = is_object($item) ? ($item->answer ?? '') : ($item['answer'] ?? '');
                return trim($question) && trim($answer);
            });
        @endphp

        @if (!empty($productFaqItems))
            <div class="product-details-item mt-4">
                <h5 class="mb-3">@lang('Frequently Asked Questions')</h5>
                <div class="accordion" id="productFaqAccordion">
                    @foreach ($productFaqItems as $index => $faqItem)
                        @php
                            $question = is_object($faqItem) ? ($faqItem->question ?? '') : ($faqItem['question'] ?? '');
                            $answer = is_object($faqItem) ? ($faqItem->answer ?? '') : ($faqItem['answer'] ?? '');
                        @endphp
                        <div class="accordion-item mb-2 border rounded">
                            <h2 class="accordion-header" id="faqHeading{{ $index }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $index }}" aria-expanded="false" aria-controls="faqCollapse{{ $index }}">
                                    {{ $question }}
                                </button>
                            </h2>
                            <div id="faqCollapse{{ $index }}" class="accordion-collapse collapse" aria-labelledby="faqHeading{{ $index }}" data-bs-parent="#productFaqAccordion">
                                <div class="accordion-body">
                                    {!! nl2br(e($answer)) !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="product-details-item mb-3">
            <div class="product-details-item__title flex-between">
                <h6 class="mb-0">@lang('More items by') {{ @$product->author->fullname }}</h6>
                <a href="{{ route('user.profile', $product->author->username) }}"
                   class="text--base text-decoration-underline">
                    @lang('View author profile')
                </a>
            </div>
            <div class="more-product-thumbs">
                @foreach ($product->author->products()->approved()->where('id', '!=', $product->id)->orderBy('id', 'desc')->limit(8)->get() as $otherProduct)
                    <div class="more-product-thumbs__item">
                        <a href="{{ route('product.details', $otherProduct->slug) }}"
                           title="{{ __($otherProduct->title) }}">
                            <img src="{{ getImage(getFilePath('productThumbnail') . productFilePath($otherProduct, 'thumbnail')) }}"
                                 alt="@lang('Product Thumbnail')" />
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#showPreviewVideo').on('click', function (e) {
            e.preventDefault();
            $.magnificPopup.open({
                items: {
                    src: '#previewVideo'
                },
                type: 'inline'
            });
        });
    });
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var videoUrl = "{{ $product->preview_video }}";
    var iframe = document.getElementById("youtubeFrame");
    var modal = document.getElementById("videoModal");

    modal.addEventListener("show.bs.modal", function () {
        // Convert normal URL to embed
        if(videoUrl.includes("watch?v=")){
            videoUrl = videoUrl.replace("watch?v=", "embed/");
        }
        iframe.src = videoUrl + "?autoplay=1";
    });

    modal.addEventListener("hidden.bs.modal", function () {
        iframe.src = "";
    });
});
</script>


