@php
    $banner = getContent('banner.content', true);
    $bannerElements = getContent('banner.element');
@endphp
<section class="banner bg-img" data-background-image="{{ frontendImage('banner', 'rgc-banner.png', '1920x850') }}">
    <div class="container">
        <div class="banner-wrapper">
            <div class="banner-content">
                <h1 class="banner-content__title">{{ __(@$banner->data_values->title) }}</h1>
                <p class="banner-content__desc">{{ __(@$banner->data_values->subtitle) }}</p>
                <form action="{{ route('products') }}" class="hero-search">
                    <input type="text" class="form--control" name="search" placeholder="@lang('Search here')">
                    <button type="submit">
                        <!--<span class="icon"><i class="icon-Search"></i></span>-->
                        @lang('Search')
                    </button>
                </form>
                <ul class="tech-list flex-align">
                    @foreach ($bannerElements as $bannerElement)
                        <li class="tech-list__item flex-center">
                            <img src="{{ frontendImage('banner', @$bannerElement->data_values->tech_image, '20x20') }}" alt="@lang('Image')" class="icon">
                        </li>
                    @endforeach
                </ul>
            </div>
            <!--<div class="banner-thumb d-none d-lg-block">-->
            <!--    <img src="{{ frontendImage('banner', @$banner->data_values->image, '680x450') }}" alt="@lang('Image')">-->
            <!--    <img src="{{ asset($activeTemplateTrue . 'images/curve-shape.png') }}" alt="@lang('Image')" class="banner-thumb__element one">-->
            <!--    <img src="{{ asset($activeTemplateTrue . 'images/banner-shape2.png') }}" alt="@lang('Image')" class="banner-thumb__element two">-->
            <!--    <div class="design-qty flex-center">-->
            <!--        <div class="design-qty__content">-->
            <!--            <span class="design-qty__icon"> <img src="{{ frontendImage('banner',@$banner->data_values->counter_image, '30x20') }}" alt="@lang('Image')"></span>-->
            <!--            <span class="design-qty__number text--base">{{ __(@$banner->data_values->counter_title) }}</span>-->
            <!--            <span class="design-qty__text">{{ __(@$banner->data_values->counter_subtitle) }}</span>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
        </div>
    </div>
</section>


<style>

.hero-search {
  display: flex;
  max-width: 500px;
  margin: 0 auto;
  background: #fff;
  border-radius: 50px;
  overflow: hidden;
  box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}

.hero-search input {
  flex: 1;
  border: none;
  padding: 15px 20px;
  font-size: 1rem;
  outline: none;
}

.hero-search button {
  background: #ff6b00;
  color: #fff;
  border: none;
  padding: 15px 25px;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.3s ease;
}

.hero-search button:hover {
  background: #e85b00;
}

</style>
