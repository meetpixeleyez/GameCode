<?php

use Illuminate\Support\Facades\Route;

Route::get('/clear', function(){
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

Route::get('cron', 'CronController@cron')->name('cron');

// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{id}', 'replyTicket')->name('reply');
    Route::post('close/{id}', 'closeTicket')->name('close');
    Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
});

Route::get('app/deposit/confirm/{hash}', 'Gateway\PaymentController@appDepositConfirm')->name('deposit.app.confirm');

Route::controller('CartController')->prefix('cart')->name('cart.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::delete('/{id}', 'delete')->name('delete');
    Route::get('/toggle-extended/{id}', 'toggleExtended')->name('extended.toggle');
    Route::post('/toggle-additional-service/{id}', 'toggleAdditionalService')->name('additional.service.toggle');
    Route::get('/apply-coupon', 'applyCoupon')->name('apply.coupon');
});

// Checkout Routes
Route::controller('CheckoutController')->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/process', 'processCheckout')->name('process');
    Route::get('/payment', 'payment')->name('payment');
    Route::post('/payment/process', 'processPayment')->name('payment.process');
    Route::get('/thank-you', 'thankYou')->name('thank.you');
});

// Blog
Route::controller('BlogController')->group(function(){
    Route::get('/blog', 'index')->name('blog.index');
    Route::get('/blog/{slug}', 'show')->name('blog.show');
});

Route::controller('SiteController')->group(function () {
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::get('/products', 'products')->name('products');
    Route::get('/free-products', 'freeProducts')->name('free.products');

    // Legacy product detail redirects for SEO and backward compatibility
    Route::get('/products/{slug}', function ($slug) {
        return redirect()->route('product.details', $slug, 301);
    });
    Route::get('/products/{slug}/reviews', function ($slug) {
        return redirect()->route('product.reviews', $slug, 301);
    });
    Route::get('/products/{slug}/comments', function ($slug) {
        return redirect()->route('product.comments', $slug, 301);
    });
    Route::get('/products/{slug}/changelog', function ($slug) {
        return redirect()->route('product.changelog', $slug, 301);
    });

    Route::get('/game-source-code/{slug}', 'productDetails')->name('product.details');
    Route::get('/game-source-code/{slug}/reviews', 'productReviews')->name('product.reviews');
    Route::get('/game-source-code/{slug}/comments', 'productComments')->name('product.comments');
    Route::get('/game-source-code/{slug}/changelog', 'productChangelog')->name('product.changelog');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');
    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');
    Route::get('policy/{slug}', 'policyPages')->name('policy.pages');
    Route::get('/sitemap.xml', 'sitemap')->name('sitemap.xml');

    Route::get('placeholder-image/{size}', 'placeholderImage')->withoutMiddleware('maintenance')->name('placeholder.image');
    Route::get('maintenance-mode','maintenance')->withoutMiddleware('maintenance')->name('maintenance');

    Route::post('click/{id}', 'incrementClick')->name('advertisement.click');

    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('collections/{id}/add-to-cart', 'CartController@collectionToCart')->name('collections.cart');
});