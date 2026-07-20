@php
    if (!isset($author)) {
        $author = auth()->user();
    }
@endphp

<div class="profile-banner__tab">
    @php
        $commentsCount = $author->comments()->where('review_id', Status::NO)->where('parent_id', Status::NO)->count();
        $reivewCount = $author->reviews->count();
        $refundItemsCount = $author->refundRequests->where('status', Status::NO)->count();
        $submittedRefundItemsCount = $author->submittedRefundRequests->where('status', Status::NO)->count();
        $refundItemsCount += $submittedRefundItemsCount;
        $hiddenItems = $author
            ->products()
            ->whereIn('status', [Status::PRODUCT_PENDING, Status::PRODUCT_SOFT_REJECTED, Status::PRODUCT_DOWN])
            ->count();
        $requestUsername = request()->username;
        $isAuthUser = auth()->check();
        $user = auth()->user();
        $referralCount = $author->referrals()->count();
    @endphp

    <button class="custom-tab__prev"><i class="las la-angle-left"></i></button>
    <div class="custom-tab-wrapper">
        <ul class="custom-tab style-two mb-0">
            @if ($isAuthUser && $user->username == $author->username)
                <li class="custom-tab__item {{ menuActive('user.home') }}">
                    <a href="{{ route('user.home') }}" class="custom-tab__link dashboard-style">@lang('Dashboard')</a>
                </li>
            @endif

            @auth
                @if ($isAuthUser && $user->username == $author->username)
                    <li class="custom-tab__item {{ menuActive('user.profile.my') }}">
                        <a href="{{ route('user.profile.my') }}" class="custom-tab__link">@lang('Profile')</a>
                    </li>
                @else
                    <li class="custom-tab__item {{ menuActive('user.profile.my') }}">
                        <a href="{{ route('user.profile', $author->username) }}"
                            class="custom-tab__link">@lang('Profile')</a>
                    </li>
                @endif
            @else
                <li class="custom-tab__item {{ menuActive('user.profile') }}">
                    <a href="{{ route('user.profile', $author->username) }}" class="custom-tab__link">@lang('Profile')</a>
                </li>
            @endauth

            @if ($author->is_author)
                <li class="custom-tab__item {{ menuActive('user.portfolio') }}">
                    <a href="{{ route('user.portfolio', $author->username) }}"
                        class="custom-tab__link">@lang('Portfolio')</a>
                </li>
                <li class="custom-tab__item {{ menuActive('user.followers') }}">
                    <a href="{{ route('user.followers', $author->username) }}" class="custom-tab__link">
                        @lang('Followers')
                        @if ($author->followers->count() > 0)
                            <span class="notification">{{ $author->followers->count() }}</span>
                        @endif
                    </a>
                </li>
            @endif

            <li class="custom-tab__item {{ menuActive('user.following') }}">
                <a href="{{ route('user.following', $author->username) }}" class="custom-tab__link">
                    @lang('Following')
                    @if ($author->follows->count() > 0)
                        <span class="notification"> {{ @$author->follows->count() }} </span>
                    @endif
                </a>
            </li>

            @if ($isAuthUser && $user->username == $author->username)
                <li class="custom-tab__item {{ menuActive('user.author.refunds') }}">
                    <a href="{{ route('user.author.download') }}" class="custom-tab__link">
                        @lang('Purchased Items')
                        @if ($refundItemsCount > 0)
                            <span class="notification">{{ $refundItemsCount }}</span>
                        @endif
                    </a>
                </li>
            @endif

            @if ($isAuthUser && $author->isAuthor() && $user->username == $author->username)
                <li class="custom-tab__item {{ menuActive('user.author.hidden_items') }}">
                    <a href="{{ route('user.author.hidden_items') }}" class="custom-tab__link">
                        @lang('Hidden Items')
                        @if ($hiddenItems > 0)
                            <span class="notification">{{ $hiddenItems }}</span>
                        @endif
                    </a>
                </li>
                <li class="custom-tab__item {{ menuActive('user.author.earning') }}">
                    <a href="{{ route('user.author.earning') }}" class="custom-tab__link">@lang('Earning')</a>
                </li>
                <li class="custom-tab__item {{ menuActive('user.author.sells') }}">
                    <a href="{{ route('user.author.sells') }}" class="custom-tab__link">@lang('Sales')</a>
                </li>
                <li class="custom-tab__item {{ menuActive('user.author.comments.index') }}">
                    <a href="{{ route('user.author.comments.index') }}" class="custom-tab__link">
                        @lang('Comments')
                        @if ($commentsCount > 0)
                            <span class="notification">{{ $commentsCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="custom-tab__item {{ menuActive('user.author.reviews.index') }}">
                    <a href="{{ route('user.author.reviews.index') }}" class="custom-tab__link">
                        @lang('Reviews')
                        @if ($reivewCount > 0)
                            <span class="notification">{{ $reivewCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="custom-tab__item {{ menuActive('user.author.license.index') }}">
                    <a href="{{ route('user.author.license.index') }}" class="custom-tab__link">@lang('Check licenses')</a>
                </li>

                @if (gs('referral'))
                    <li class="custom-tab__item {{ menuActive('user.author.referral.index') }}">
                        <a href="{{ route('user.author.referral.index') }}" class="custom-tab__link">
                            @lang('Referral')
                            @if ($referralCount > 0)
                                <span class="notification">{{ $referralCount }}</span>
                            @endif
                        </a>
                    </li>
                @endif

                @php
                    $campaign = App\Models\Campaign::active()->latest()->first();
                @endphp
                @if ($campaign)
                    <li class="custom-tab__item {{ menuActive('user.author.campaign.index') }}">
                        <a href="{{ route('user.author.campaign.index') }}" class="custom-tab__link">
                            @lang('Campaign')
                            <span class="badge badge--base blinking">@lang('New')</span>
                        </a>
                    </li>
                @endif
            @endif
        </ul>
    </div>
    <button class="custom-tab__next"><i class="las la-angle-right"></i></button>

</div>

<style>
    .profile-banner__tab {
        display: flex;
        align-items: center;
        position: relative;
        margin-bottom: 15px;
    }

    .custom-tab-wrapper {
        white-space: nowrap;
        overflow: hidden;
    }

    .custom-tab.style-two {
        display: flex;
        flex-wrap: nowrap;
        transition: transform 0.3s ease-in-out;
        gap: 0;
    }

    .custom-tab.style-two .custom-tab__item {
        margin-right: 45px;
        flex-shrink: 0;
    }

    .custom-tab__prev,
    .custom-tab__next {
        border: none;
        cursor: pointer;
        display: none;
        color: hsl(var(--body-color));
        padding-right: 15px;
        background: linear-gradient(90deg, rgb(242 242 242) 25%, rgb(242 242 242) 60%);
    }
    .custom-tab__next {
        padding-left: 15px;
        padding-right: 0;
        background: linear-gradient(-90deg, rgb(242 242 242) 25%, rgb(242 242 242) 60%);
    }

    .custom-tab__prev:hover,
    .custom-tab__next:hover {
        color: hsl(var(--base));
    }

    @media (max-width: 1399px) {
        .custom-tab.style-two .custom-tab__item {
            margin-right: 38px;
        }
    }
    @media (max-width: 1199px) {
        .custom-tab.style-two .custom-tab__item {
            margin-right: 30px;
        }
    }
    @media (max-width: 991px) {
        .profile-banner__tab {
            margin-bottom: 12px;
        }

        .custom-tab.style-two .custom-tab__item {
            margin-right: 25px;
        }

        .custom-tab__prev,
        .custom-tab__next {
            font-size: 15px;
        }
    }
    @media (max-width: 767px) {
        .custom-tab.style-two .custom-tab__item {
            margin-right: 20px;
        }
    }
    @media (max-width: 575px) {
        .profile-banner__tab {
            margin-bottom: 10px;
        }
        .custom-tab.style-two .custom-tab__item {
            margin-right: 18px;
        }
        .custom-tab__prev,
        .custom-tab__next {
            font-size: 14px;
        }
    }
    @media (max-width: 424px) {}
</style>




@push('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const wrapper = document.querySelector(".custom-tab-wrapper");
            const track = document.querySelector(".custom-tab.style-two");
            const prevBtn = document.querySelector(".custom-tab__prev");
            const nextBtn = document.querySelector(".custom-tab__next");
            const scrollAmount = 200;

            function updateButtonVisibility() {
                prevBtn.style.display = wrapper.scrollLeft > 0 ? "block" : "none";
                nextBtn.style.display =
                    wrapper.scrollLeft + wrapper.clientWidth < track.scrollWidth ?
                    "block" :
                    "none";
            }

            prevBtn.addEventListener("click", function() {
                wrapper.scrollBy({
                    left: -scrollAmount,
                    behavior: "smooth"
                });
            });

            nextBtn.addEventListener("click", function() {
                wrapper.scrollBy({
                    left: scrollAmount,
                    behavior: "smooth"
                });
            });

            wrapper.addEventListener("scroll", updateButtonVisibility);
            window.addEventListener("resize", updateButtonVisibility);

            updateButtonVisibility();
        });
    </script>
    {{-- <script>
        "use strict";
        (function($) {
            let navbarWrapper = $('.navbar--categories .custom-tab-wrapper');
            let navbarMenu = $('.navbar--categories .custom-tab');
            let navbarMenuItems = navbarMenu.find('.custom-tab__item');
            let navbarMenuFirstItem = navbarMenu.find('.custom-tab__item:first-child')[0];
            let navbarMenuLastItem = navbarMenu.find('.custom-tab__item:last-child')[0];
            let navbarMenuPrev = $('.custom-tab__prev');
            let navbarMenuNext = $('.custom-tab__next');
            let count = 0;
            let observerOptions = {
                root: $('.navbar--categories')[0],
                rootMargin: "1px",
                threshold: 1
            }

            function moveTrack(action = '') {

                //store the length of button in track
                let totalItems = navbarMenuItems.length;

                //generate a avg width for track
                let navbarMenuAvgWidth = Math.ceil(navbarMenu[0].clientWidth / totalItems);

                //increment || decrement = next || prev
                count = action == 'next' ? count + 1 : count - 1;

                //change the css transform value
                navbarMenu.css('transform', `translateX(-${(navbarMenuAvgWidth * count)}px)`);
            }

            function setIntersectionObserver(element, ctrlBtn) {

                let observer = new IntersectionObserver((entries) => {

                    entries.forEach(entry => {

                        if (entry.intersectionRatio >= 1) {

                            //disappear the prev button when the first button appears entirely
                            $(ctrlBtn).removeClass('d-block');

                        } else {

                            //appear the prev button when the first button starts disapearing
                            $(ctrlBtn).addClass('d-block');
                        }

                    });

                }, observerOptions);

                return observer.observe(element);
            }

            setIntersectionObserver(navbarMenuFirstItem, navbarMenuPrev[0]);
            setIntersectionObserver(navbarMenuLastItem, navbarMenuNext[0]);

            $(navbarMenuPrev).on('click', function() {
                moveTrack()
            })

            $(navbarMenuNext).on('click', function() {
                moveTrack('next')
            })

        })(jQuery);
    </script> --}}
@endpush
