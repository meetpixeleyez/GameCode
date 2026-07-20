<?php

namespace App\Providers;

use App\Constants\Status;
use App\Lib\Searchable;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\Frontend;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        Builder::mixin(new Searchable);
        $this->app->usePublicPath(base_path('..'));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        if (!cache()->get('SystemInstalled')) {
            $envFilePath = base_path('.env');
            if (!file_exists($envFilePath)) {
                header('Location: install');
                exit;
            }
            $envContents = file_get_contents($envFilePath);
            if (empty($envContents)) {
                header('Location: install');
                exit;
            } else {
                cache()->put('SystemInstalled', true);
            }
        }

        $viewShare['emptyMessage'] = 'Data not found';
        view()->share($viewShare);

        view()->composer('admin.partials.sidenav', function ($view) {
            $view->with([
                'bannedUsersCount'           => User::banned()->count(),
                'emailUnverifiedUsersCount'  => User::emailUnverified()->count(),
                'mobileUnverifiedUsersCount' => User::mobileUnverified()->count(),
                'kycUnverifiedUsersCount'    => User::kycUnverified()->count(),
                'kycPendingUsersCount'       => User::kycPending()->count(),
                'pendingTicketCount'         => SupportTicket::whereIN('status', [Status::TICKET_OPEN, Status::TICKET_REPLY])->count(),
                'pendingDepositsCount'       => Deposit::pending()->count(),
                'pendingWithdrawCount'       => Withdrawal::pending()->count(),
                'pendingProductsCount'       => Product::pending()->count(),
                'paidOrdersCount'            => Order::paid()->count(),
                'refundedItemsCount'         => OrderItem::refunded()->count(),
                'updateAvailable'            => version_compare(gs('available_version'), systemDetails()['version'], '>') ? 'v' . gs('available_version') : false,
            ]);
        });

        view()->composer('admin.partials.topnav', function ($view) {
            $view->with([
                'adminNotifications'     => AdminNotification::where('is_read', Status::NO)->with('user')->orderBy('id', 'desc')->take(10)->get(),
                'adminNotificationCount' => AdminNotification::where('is_read', Status::NO)->count(),
            ]);
        });

        view()->composer('reviewer.partials.sidenav', function ($view) {
            $reviewer = auth('reviewer')->user();
            $view->with([
                'waitingProduct' => Product::whereIn('sub_category_id', @$reviewer->subcategories ?? [])->where(function ($q) {
                    $q->where('status', Status::PRODUCT_PENDING)->orWhere('product_updated', Status::PRODUCT_UPDATE_PENDING);
                })->whereIn('assigned_to', [0, (@$reviewer->id ?? 0)])
                    ->where('status', '!=', Status::PRODUCT_PERMANENT_DOWN)
                    ->where('status', '!=', Status::PRODUCT_UPDATE_HARD_REJECT)
                    ->where('status', '!=', Status::PRODUCT_HARD_REJECTED)
                    ->count()
            ]);
        });

        view()->composer('partials.seo', function ($view) {
            $seo = Frontend::where('data_keys', 'seo.data')->first();
            $view->with([
                'seo' => $seo ? $seo->data_values : $seo,
            ]);
        });

        // if (gs('force_ssl')) {
        //     \URL::forceScheme('https');
        // }

        Paginator::useBootstrapFive();
    }
}
