@php
    $user = auth()->user();
    $cartLength = cartCount();
@endphp

<!-- Paras Code Start Topbar -->

<style>
    .paras-topbar {
      width: 100%;
      background: #111;
      color: #fff;
    }

    .paras-container {
      max-width: 1320px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 5px 20px;
    }

    /* Left side links */
    .paras-left-links {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .paras-left-links a {
      text-decoration: none;
      color: #fff;
      font-size: 14px;
    }

    .paras-divider {
      color: #777;
    }

    /* Center scroll text */
    .paras-scroll-container {
      position: relative;
      overflow: hidden;
      flex: 1;
      text-align: center;
      margin: 0 20px;
      white-space: nowrap;
    }

    .paras-scroll-text {
      display: inline-block;
      padding-right: 100px; /* gap between repeats */
      animation: paras-scroll 20s linear infinite;
      font-size: 14px;
    }

    .paras-scroll-wrapper {
      display: inline-block;
      white-space: nowrap;
    }

    @keyframes paras-scroll {
      0%   { transform: translateX(0); }
      100% { transform: translateX(-50%); }
    }

    /* Right side hire button */
    .paras-hire-btn {
      padding: 5px 20px;
      border-radius: 30px;
      font-weight: bold;
      border: 2px solid #fff;
      background: transparent;
      cursor: pointer;
      color: #fff;
      position: relative;
      overflow: hidden;
    }

    /* Text highlight animation */
    .paras-hire-btn span {
      background: linear-gradient(90deg, #ff7c31, #ffcc70, #ffffff);
      background-size: 200% auto;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      animation: paras-shine 3s linear infinite;
      display: inline-block;
    }

    @keyframes paras-shine {
      0% { background-position: -200% center; }
      100% { background-position: 200% center; }
    }
  </style>
  
  <div class="paras-topbar">
    <div class="paras-container">
      <!-- Left side -->
      <div class="paras-left-links">
        <a href="https://readygamecode.com/contact">Contact</a>
        <span class="paras-divider">|</span>
        <a href="https://readygamecode.com/blog">Blog</a>
      </div>

      <!-- Center scroll -->
      <div class="paras-scroll-container">
        <div class="paras-scroll-wrapper">
          <span class="paras-scroll-text">
            Latest News Here | Updates Coming Soon | Welcome to Our Website | Don’t Miss Out! 
          </span>
          <span class="paras-scroll-text">
            Latest News Here | Updates Coming Soon | Welcome to Our Website | Don’t Miss Out! 
          </span>
        </div>
      </div>

      <!-- Right side -->
      <button class="paras-hire-btn"><a href="https://api.whatsapp.com/send?phone=9194082123108&text=%F0%9F%91%8B%20Hey%20Ready%20Game%20Code,%20can%20you%20help%20me%20with" target="_blank"><span>Hire us</span></a></button>
    </div>
  </div>

<!-- Paras Code End Topbar-->

<div class="header-top">
    <div class="container">
        <div class="top-header__wrapper flex-between">
            <a class="navbar-brand logo site-logo d-lg-block d-none" href="{{ route('home') }}">
                <img src="{{ siteLogo() }}" alt="@lang('logo')">
            </a>
            
            <header class="header" id="header">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">

            <a class="navbar-brand logo d-lg-none d-block" href="{{ route('home') }}">
                <img width="164" src="{{ siteLogo() }}" alt="{{ gs('site_name') }}">
            </a>

            <button class="navbar-toggler header-button" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span id="hiddenNav"><i class="las la-bars"></i></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">

                <ul class="navbar-nav nav-menu me-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="{{ route('products') }}">@lang('All Items')</a>
                    </li>
                    @foreach ($categories ?? [] as $category)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('products', ['category' => $category->id]) }}" aria-expanded="false">
                                {{ $category->name }} </a>
                            @if ($category->subCategories->count())
                                <!--<ul class="dropdown-menu">-->
                                <!--    @foreach ($category->subCategories ?? [] as $subCategory)-->
                                <!--        <li class="dropdown-menu__list">-->
                                <!--            <a class="dropdown-item dropdown-menu__link"-->
                                <!--                href="{{ route('products', ['sub_category' => $subCategory->id]) }}">{{ $subCategory->name }}-->
                                <!--            </a>-->
                                <!--        </li>-->
                                <!--    @endforeach-->
                                <!--</ul>-->
                            @endif
                        </li>
                    @endforeach
                     <li class="nav-item">
                                    <a class="nav-link" href="https://readygamecode.com/policy/privacy-policy"
                                        aria-expanded="false">Privacy Policy</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="https://readygamecode.com/policy/terms-of-service"
                                        aria-expanded="false">Terms &amp; Conditions</a>
                                </li>
                </ul>
                
            </div>
        </nav>
    </div>
</header>
            
            <div class="header-top__right flex-between gap-2">
                <!--<div class="language_switcher">-->
                    @if (gs('multi_language'))
                        @php
                            $language = App\Models\Language::all();
                            $selectLang = $language->where('code', config('app.locale'))->first();
                            $currentLang = session('lang') ? $language->where('code', session('lang'))->first() : $language->where('is_default', Status::YES)->first();
                        @endphp
                        <!--<div class="language_switcher__caption">-->
                        <!--    <span class="icon">-->
                        <!--        <img src="{{ getImage(getFilePath('language') . '/' . $currentLang->image, getFileSize('language')) }}"-->
                        <!--             alt="@lang('image')">-->
                        <!--    </span>-->
                        <!--    <span class="text"> {{ __(@$selectLang->name) }} </span>-->
                        <!--</div>-->
                        <!--<div class="language_switcher__list">-->
                        <!--    @foreach ($language as $item)-->
                        <!--        <div class="language_switcher__item    @if (session('lang') == $item->code) selected @endif"-->
                        <!--             data-value="{{ $item->code }}">-->
                        <!--            <a href="{{ route('lang', $item->code) }}" class="thumb">-->
                        <!--                <span class="icon">-->
                        <!--                    <img src="{{ getImage(getFilePath('language') . '/' . $item->image, getFileSize('language')) }}"-->
                        <!--                         alt="@lang('image')">-->
                        <!--                </span>-->
                        <!--                <span class="text"> {{ __($item->name) }}</span>-->
                        <!--            </a>-->
                        <!--        </div>-->
                        <!--    @endforeach-->
                        <!--</div>-->
                    @endif
                <!--</div>-->

                <div class="flex-align gap-2">
                    @guest
                        <ul class="top-menu-list flex-between">
                            <li class="top-menu-list__item">
                                <a href="{{ route('user.register') }}" class="top-menu-list__link"> @lang('Register') </a>
                            </li>
                            <li class="top-menu-list__item">
                                <a href="{{ route('user.login') }}" class="top-menu-list__link"> @lang('Login') </a>
                            </li>
                        </ul>
                        
                        <a href="{{ route('cart.index') }}" class="cart-button ms-0 d-none d-lg-block" style="margin-left:15px !important;">
                        <span class="cart-button__icon "><i class="icon-Add-to-Cart-Button"></i></span>
                        <span class="cart-button__qty flex-center">{{ $cartLength }}</span>
                </a>
                        
                    @endguest
                    <a href="{{ route('cart.index') }}" class="cart-button ms-0 d-block d-lg-none">
                        <span class="cart-button__icon "><i class="icon-Add-to-Cart-Button"></i></span>
                        <span class="cart-button__qty flex-center">{{ $cartLength }}</span>
                    </a>
                    @auth
                    
                    <a href="{{ route('cart.index') }}" class="cart-button ms-0 d-none d-lg-block">
                        <span class="cart-button__icon "><i class="icon-Add-to-Cart-Button"></i></span>
                        <span class="cart-button__qty flex-center">{{ $cartLength }}</span>
                    </a>
                        <div class="profile-info">
                            <button type="button" class="profile-info__button flex-align">
                                <span class="profile-info__icon">
                                    @php
                                        $avatarPath = public_path('assets/images/user/' . @$user->avatar);
                                        $hasAvatar = @$user->avatar && file_exists($avatarPath);
                                        $initials = strtoupper(substr($user->firstname ?? 'U', 0, 1) . substr($user->lastname ?? 'S', 0, 1));
                                        $userName = $user->fullname ?? $user->username ?? 'User';
                                        // Generate avatar using DiceBear Avatars API (personas style - person face)
                                        $seed = md5($user->id . $user->username);
                                        $defaultAvatarUrl = 'https://api.dicebear.com/7.x/personas/svg?seed=' . $seed . '&backgroundColor=ff7c31&radius=50';
                                    @endphp
                                    @if($hasAvatar)
                                        <img src="{{ asset('assets/images/user/' . @$user->avatar) }}"
                                             alt="{{ @$user->username }}'s avatar" class="profile-info__avatar">
                                    @else
                                        <img src="{{ $defaultAvatarUrl }}"
                                             alt="{{ $userName }}'s avatar" 
                                             class="profile-info__avatar"
                                             onerror="this.onerror=null; this.src='{{ asset('assets/images/avatar.png') }}';">
                                    @endif
                                </span>
                                <span class="profile-info__content">
                                    <span class="profile-info__name">{{ @$user->username }} </span>
                                    <span class="profile-info__text">{{ showAmount($user->balance) }}</span>
                                </span>
                            </button>
                            <div class="profile-dropdown">
                                <div class="profile-info style-two flex-align">
                                    <span class="profile-info__icon">
                                        @php
                                            $avatarPath = public_path('assets/images/user/' . @$user->avatar);
                                            $hasAvatar = @$user->avatar && file_exists($avatarPath);
                                            $userName = $user->fullname ?? $user->username ?? 'User';
                                            // Generate avatar using DiceBear Avatars API (personas style - person face)
                                            $seed = md5($user->id . $user->username);
                                            $defaultAvatarUrl = 'https://api.dicebear.com/7.x/personas/svg?seed=' . $seed . '&backgroundColor=ff7c31&radius=50';
                                        @endphp
                                        @if($hasAvatar)
                                            <img src="{{ asset('assets/images/user/' . @$user->avatar) }}"
                                                 alt="{{ @$user->fullname }}'s avatar" class="profile-info__avatar">
                                        @else
                                            <img src="{{ $defaultAvatarUrl }}"
                                                 alt="{{ $userName }}'s avatar" 
                                                 class="profile-info__avatar"
                                                 onerror="this.onerror=null; this.src='{{ asset('assets/images/avatar.png') }}';">
                                        @endif
                                    </span>
                                    <span class="profile-info__content">
                                        <span class="profile-info__name">{{ @$user->fullname }} </span>
                                        <span class="profile-info__text">{{ @$user->email }}</span>
                                    </span>
                                </div>

                                <ul class="profile-dropdown-list">
                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('user.home') }}"
                                           class="profile-dropdown-list__link {{ menuActive('user.home') }}">
                                            <span class="icon"><i class="la la-home"></i></span>
                                            @lang('Dashboard')
                                        </a>
                                    </li>
                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('user.profile.my') }}"
                                           class="profile-dropdown-list__link {{ menuActive('user.profile.my') }}">
                                            <span class="icon">
                                                <i class="la la-user"></i>
                                            </span>
                                            @lang('Profile')
                                        </a>
                                    </li>
                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('user.author.download') }}"
                                           class="profile-dropdown-list__link {{ menuActive('user.author.download') }} ">
                                            <span class="icon"> <i
                                                   class="la la-shopping-cart"></i></span>@lang('Purchased Item')</a>
                                    </li>
                                    <!--<li class="profile-dropdown-list__item">-->
                                    <!--    <a href="{{ route('user.author.free.download') }}"-->
                                    <!--       class="profile-dropdown-list__link {{ menuActive('user.author.free.download') }} ">-->
                                    <!--        <span class="icon"> <i class="la la-gift"></i></span>@lang('Free Item')</a>-->
                                    <!--</li>-->
                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('user.order.list') }}"
                                           class="profile-dropdown-list__link {{ menuActive('user.order.list') }}">
                                            <span class="icon"><i class="la la-list"></i></span>
                                            @lang('Purchase History')
                                        </a>
                                    </li>
                                    @if (auth()->check() && auth()->user()->isAuthor())
                                        <li class="profile-dropdown-list__item">
                                            <a href="{{ route('user.product.upload') }}"
                                               class="profile-dropdown-list__link {{ menuActive('user.product.upload') }}">
                                                <span class="icon"> <i class="la la-upload"></i></span>
                                                @lang('Upload Item')</a>
                                        </li>
                                    @endif
                                    <!--<li class="profile-dropdown-list__item">-->
                                    <!--    <a href="{{ route('user.withdraw.history') }}"-->
                                    <!--       class="profile-dropdown-list__link {{ menuActive('user.withdraw.*') }}">-->
                                    <!--        <span class="icon"><i class="la la-bank"></i></span>-->
                                    <!--        @lang('Withdraw History')-->
                                    <!--    </a>-->
                                    <!--</li>-->
                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('user.transactions') }}"
                                           class="profile-dropdown-list__link {{ menuActive('user.transactions') }}">
                                            <span class="icon"><i class="la la-exchange-alt"></i></span>
                                            @lang('Transactions')
                                        </a>
                                    </li>
                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('ticket.index') }}"
                                           class="profile-dropdown-list__link {{ menuActive('ticket.*') }}">
                                            <span class="icon"><i class="la la-ticket"></i></span>
                                            @lang('Support Ticket')
                                        </a>
                                    </li>

                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('user.author.favorites') }}"
                                           class="profile-dropdown-list__link {{ menuActive('user.author.favorites') }}">
                                            <span class="icon"><i class="la la-heart-o"></i></span>@lang('Favorites')
                                        </a>
                                    </li>

                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('user.author.collections') }}"
                                           class="profile-dropdown-list__link {{ menuActive('user.author.collections') }}">
                                            <span class="icon"><i class="la la-copy"></i></span>@lang('Collections')
                                        </a>
                                    </li>

                                    <!--<li class="profile-dropdown-list__item">-->
                                    <!--    <a href="{{ route('user.api.key.index') }}"-->
                                    <!--       class="profile-dropdown-list__link {{ menuActive('user.api.key.*') }}">-->
                                    <!--        <span class="icon"><i class="las la-code"></i></span>-->
                                    <!--        @lang('API Key')-->
                                    <!--    </a>-->
                                    <!--</li>-->

                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('user.profile.setting') }}"
                                           class="profile-dropdown-list__link {{ menuActive('user.profile.setting') }}">
                                            <span class="icon"> <i class="la la-gear"></i></span> @lang('Settings')</a>
                                    </li>
                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('user.twofactor') }}"
                                           class="profile-dropdown-list__link {{ menuActive('user.twofactor') }}">
                                            <span class="icon"> <i class="la la-fingerprint"></i></span>
                                            @lang('2FA Security')</a>
                                    </li>
                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('user.change.password') }}"
                                           class="profile-dropdown-list__link {{ menuActive('user.change.password') }}">
                                            <span class="icon"> <i class="la la-key"></i></span> @lang('Change Password')</a>
                                    </li>
                                    <li class="profile-dropdown-list__item">
                                        <a href="{{ route('user.logout') }}" class="profile-dropdown-list__link">
                                            <span class="icon"> <i class="la la-sign-out-alt"></i></span>
                                            @lang('Logout')</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endauth

                </div>
            </div>
        </div>
    </div>
</div>
