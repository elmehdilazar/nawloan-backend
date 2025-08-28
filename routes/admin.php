<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\Admin\FcmTopicController;

Route::post('/admin/fcm-subscribe', [FcmTopicController::class, 'subscribe'])
    ->middleware(['web','auth']);
Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {
        Auth::routes();
        Route::get('/admin/policies/index', [\App\Http\Controllers\Admin\PolicyController::class, 'index'])->name('index');
        Route::prefix('admin')->name('admin.')
            ->middleware(['auth', 'is_admin'])->group(
                function () {
                    /*add by mohammed*/
                    Route::post('/logout', [\App\Http\Controllers\Admin\AdminController::class, 'logout'])->name('logout');

                    Route::get('/', [\App\Http\Controllers\Admin\AdminController::class, 'index']);
                    Route::get('/search', [\App\Http\Controllers\Admin\AdminController::class, 'search'])->name('search');
                    Route::get('/clear_views', [\App\Http\Controllers\Admin\AdminController::class, 'clear_views'])->name('clear.views');
                    Route::get('/clear_cache', [\App\Http\Controllers\Admin\AdminController::class, 'clear_cache'])->name('clear.cache');
                    Route::get('/clear_routes', [\App\Http\Controllers\Admin\AdminController::class, 'clear_routes'])->name('clear.routes');
                    Route::get('/clear_optimize', [\App\Http\Controllers\Admin\AdminController::class, 'clear_optimize'])->name('clear.optimize');
                    Route::get('/clear_config', [\App\Http\Controllers\Admin\AdminController::class, 'clear_config'])->name('clear.config');
                    Route::prefix('messages')->group(function() {
                        Route::get('/mail', [\App\Http\Controllers\Admin\AdminController::class, 'send_mail'])->name('messages.send_mail');
                        Route::post('/sendMail', [\App\Http\Controllers\Admin\AdminController::class, 'sendMail'])->name('messages.sendMail');
                        Route::get('/sms', [\App\Http\Controllers\Admin\AdminController::class, 'send_sms'])->name('messages.send_sms');
                        Route::post('/sendSms', [\App\Http\Controllers\Admin\AdminController::class, 'sendSms'])->name('messages.sendSms');
                        Route::get('/support', [\App\Http\Controllers\Admin\AdminController::class, 'customerMessages'])->name('messages.customer_messages');
                        Route::post('/support/replay/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'messageReplay'])->name('messages.customer_messages.replay');
                    });

                     Route::prefix('policies')->name('policies.')->group(
                        function () {
                            Route::get('/create', [\App\Http\Controllers\Admin\PolicyController::class, 'create'])->name('create');
                            Route::post('/store', [\App\Http\Controllers\Admin\PolicyController::class, 'store'])->name('store');
                            Route::get('/edit/{id}', [\App\Http\Controllers\Admin\PolicyController::class, 'edit'])->name('edit');
                            Route::put('/update/{id}', [\App\Http\Controllers\Admin\PolicyController::class, 'update'])->name('update');
                            Route::put('/restore/{id}', [\App\Http\Controllers\Admin\PolicyController::class, 'restore'])->name('restore');
                            Route::delete('/destroy/{id}', [\App\Http\Controllers\Admin\PolicyController::class, 'destroy'])->name('destroy');
                        }
                    );

                    Route::get('/MarkAsRead/{id}', [\App\Http\Controllers\Admin\UserController::class, 'MarkAsRead'])->name('MarkAsRead');
                    Route::get('/showAndRead/{id}', [\App\Http\Controllers\Admin\UserController::class, 'showAndRead'])->name('showAndRead');
                    Route::get('/MarkAsRead_all', [\App\Http\Controllers\Admin\UserController::class, 'MarkAsRead_all'])->name('MarkAsRead_all');
                    Route::get('/unreadNotifications_count', [\App\Http\Controllers\Admin\UserController::class, 'unreadNotifications_count'])->name('unreadNotifications_count');
                    Route::get('/unreadNotifications', [\App\Http\Controllers\Admin\UserController::class, 'unreadNotifications'])->name('unreadNotifications');
                    Route::get('/notifications', [\App\Http\Controllers\Admin\UserController::class, 'allNotifications'])->name('allNotifications');

                    Route::get('/account/show', [\App\Http\Controllers\Admin\UserController::class, 'accountShow'])->name('account.show');
                    Route::get('/account/edit', [\App\Http\Controllers\Admin\UserController::class, 'accountEdit'])->name('account.edit');
                    Route::put('/account/update/{id}', [\App\Http\Controllers\Admin\UserController::class, 'accountUpdate'])->name('account.update');
                    Route::get('/account/change-password', [\App\Http\Controllers\Admin\UserController::class, 'changePassword'])->name('account.edit.password');
                    Route::post('/account/change-password', [\App\Http\Controllers\Admin\UserController::class, 'storePassword'])->name('account.change.password');

                    Route::prefix('orders')->name('orders.')->group(
                    function () {
                        Route::get('/export', [\App\Http\Controllers\Admin\OrderController::class, 'export'])->name('export');
                        Route::get('/pend', [\App\Http\Controllers\Admin\OrderController::class, 'pend'])->name('pend');
                        Route::get('/progress', [\App\Http\Controllers\Admin\OrderController::class, 'progress'])->name('progress');
                        Route::get('/complete', [\App\Http\Controllers\Admin\OrderController::class, 'complete'])->name('complete');
                        Route::get('/cancel', [\App\Http\Controllers\Admin\OrderController::class, 'cancel'])->name('cancel');
                        Route::put('/{id}/changeStatus', [\App\Http\Controllers\Admin\OrderController::class, 'changeStatus'])->name('changeStatus');
                        Route::put('/{id}/changeOfferStatus', [\App\Http\Controllers\Admin\OrderController::class, 'changeOfferStatus'])->name('changeOfferStatus');
                        Route::put('/{id}/approveOffer', [\App\Http\Controllers\Admin\OrderController::class, 'approveOffer'])->name('approveOffer');
                        Route::put('/{id}/completeOrder', [\App\Http\Controllers\Admin\OrderController::class, 'completeOrder'])->name('completeOrder');
                    });
                Route::prefix('chat')->name('chat.')->group(
                    function () {
                        Route::get('/index', [\App\Http\Controllers\Admin\ChatController::class, 'index'])->name('index');
                        Route::post('/store', [\App\Http\Controllers\Admin\ChatController::class, 'store'])->name('store');
                        Route::get('/rooms', [\App\Http\Controllers\Admin\ChatController::class, 'roomsAjax'])->name('rooms');
                        Route::get('/messages', [\App\Http\Controllers\Admin\ChatController::class, 'messagesAjax'])->name('messages');
                        // Route::put('/{id}/changeStatus', [\App\Http\Controllers\Admin\OrderController::class, 'changeStatus'])->name('changeStatus');
                        // Route::put('/{id}/changeOfferStatus', [\App\Http\Controllers\Admin\OrderController::class, 'changeOfferStatus'])->name('changeOfferStatus');
                        // Route::put('/{id}/approveOffer', [\App\Http\Controllers\Admin\OrderController::class, 'approveOffer'])->name('approveOffer');
                        // Route::put('/{id}/completeOrder', [\App\Http\Controllers\Admin\OrderController::class, 'completeOrder'])->name('completeOrder');
                    }
                );

                Route::prefix('careers')->name('careers.')->group(
                    function () {
                        Route::get('/index', [\App\Http\Controllers\Admin\CareerController::class, 'index'])->name('index');
                        Route::get('/show/{id}', [\App\Http\Controllers\Admin\CareerController::class, 'show'])->name('show');
                        Route::get('/create', [\App\Http\Controllers\Admin\CareerController::class, 'create'])->name('create');
                        Route::post('/store', [\App\Http\Controllers\Admin\CareerController::class, 'store'])->name('store');
                        Route::get('/edit/{id}', [\App\Http\Controllers\Admin\CareerController::class, 'edit'])->name('edit');
                        Route::put('/update/{id}', [\App\Http\Controllers\Admin\CareerController::class, 'update'])->name('update');
                        Route::post('/changeStatus/{id}', [\App\Http\Controllers\Admin\CareerController::class, 'changeStatus'])->name('changeStatus');
                        Route::get('/export', [\App\Http\Controllers\Admin\CareerController::class, 'export'])->name('export');

                    }
                );

                Route::prefix('career_categories')->name('career_categories.')->group(
                    function () {
                        Route::get('/index', [\App\Http\Controllers\Admin\Career_categoryController::class, 'index'])->name('index');
                        Route::get('/show/{id}', [\App\Http\Controllers\Admin\Career_categoryController::class, 'show'])->name('show');
                        Route::get('/create', [\App\Http\Controllers\Admin\Career_categoryController::class, 'create'])->name('create');
                        Route::post('/store', [\App\Http\Controllers\Admin\Career_categoryController::class, 'store'])->name('store');
                        Route::get('/edit/{id}', [\App\Http\Controllers\Admin\Career_categoryController::class, 'edit'])->name('edit');
                        Route::put('/update/{id}', [\App\Http\Controllers\Admin\Career_categoryController::class, 'update'])->name('update');
                        Route::put('/restore/{id}', [\App\Http\Controllers\Admin\Career_categoryController::class, 'restore'])->name('restore');
                        Route::delete('/destroy/{id}', [\App\Http\Controllers\Admin\Career_categoryController::class, 'destroy'])->name('destroy');
                    }
                );


                // Route::prefix('Careers')->name('Careers.')->group(
                //     function () {
                //         Route::get('/create', [\App\Http\Controllers\Admin\CareerController::class, 'create'])->name('create');
                //         Route::post('/store', [\App\Http\Controllers\Admin\CareerController::class, 'store'])->name('store');
                //     }
                // );

                Route::prefix('article_categories')->name('article_categories.')->group(
                    function () {
                        Route::get('/index', [\App\Http\Controllers\Admin\Article_categoryController::class, 'index'])->name('index');
                        Route::get('/show/{id}', [\App\Http\Controllers\Admin\Article_categoryController::class, 'show'])->name('show');
                        Route::get('/create', [\App\Http\Controllers\Admin\Article_categoryController::class, 'create'])->name('create');
                        Route::post('/store', [\App\Http\Controllers\Admin\Article_categoryController::class, 'store'])->name('store');
                        Route::get('/edit/{id}', [\App\Http\Controllers\Admin\Article_categoryController::class, 'edit'])->name('edit');
                        Route::put('/update/{id}', [\App\Http\Controllers\Admin\Article_categoryController::class, 'update'])->name('update');
                        Route::put('/restore/{id}', [\App\Http\Controllers\Admin\Article_categoryController::class, 'restore'])->name('restore');
                        Route::delete('/destroy/{id}', [\App\Http\Controllers\Admin\Article_categoryController::class, 'destroy'])->name('destroy');
                        Route::get('/export', [\App\Http\Controllers\Admin\Article_categoryController::class, 'export'])->name('export');
                        Route::post('/changeStatus/{id}', [\App\Http\Controllers\Admin\Article_categoryController::class, 'changeStatus'])->name('changeStatus');

                    }
                );

                Route::prefix('articles')->name('articles.')->group(
                    function () {
                        Route::get('/index', [\App\Http\Controllers\Admin\ArticleController::class, 'index'])->name('index');
                        Route::get('/show/{id}', [\App\Http\Controllers\Admin\ArticleController::class, 'show'])->name('show');
                        Route::get('/create', [\App\Http\Controllers\Admin\ArticleController::class, 'create'])->name('create');
                        Route::post('/store', [\App\Http\Controllers\Admin\ArticleController::class, 'store'])->name('store');
                        Route::get('/edit/{id}', [\App\Http\Controllers\Admin\ArticleController::class, 'edit'])->name('edit');
                        Route::put('/update/{id}', [\App\Http\Controllers\Admin\ArticleController::class, 'update'])->name('update');
                        Route::put('/restore/{id}', [\App\Http\Controllers\Admin\ArticleController::class, 'restore'])->name('restore');
                        Route::delete('/destroy/{id}', [\App\Http\Controllers\Admin\ArticleController::class, 'destroy'])->name('destroy');
                        Route::post('/addimg', [\App\Http\Controllers\Admin\ArticleController::class, 'addimg']);
                        Route::delete('/removeimg/{id}', [\App\Http\Controllers\Admin\ArticleController::class, 'removeimg'])->name('removeimg');
                        Route::post('/changeStatus/{id}', [\App\Http\Controllers\Admin\ArticleController::class, 'changeStatus'])->name('changeStatus');


                    }
                );

                Route::prefix('coupons')->name('coupons.')->group(
                    function () {
                        Route::get('/index', [\App\Http\Controllers\Admin\CouponController::class, 'index'])->name('index');
                        Route::get('/show/{id}', [\App\Http\Controllers\Admin\CouponController::class, 'show'])->name('show');
                        Route::post('/store', [\App\Http\Controllers\Admin\CouponController::class, 'store'])->name('store');
                        Route::get('/edit/{id}', [\App\Http\Controllers\Admin\CouponController::class, 'edit'])->name('edit');
                        Route::get('/create', [\App\Http\Controllers\Admin\CouponController::class, 'create'])->name('create');
                        Route::put('/update/{id}', [\App\Http\Controllers\Admin\CouponController::class, 'update'])->name('update');
                        Route::put('/restore/{id}', [\App\Http\Controllers\Admin\CouponController::class, 'restore'])->name('restore');
                        Route::delete('/destroy/{id}', [\App\Http\Controllers\Admin\CouponController::class, 'destroy'])->name('destroy');
                        Route::get('/export', [\App\Http\Controllers\Admin\CouponController::class, 'export'])->name('export');
                        Route::post('/changeStatus/{id}', [\App\Http\Controllers\Admin\CouponController::class, 'changeStatus'])->name('changeStatus');

                    }
                );

                Route::resource('/orders', '\App\Http\Controllers\Admin\OrderController');
                Route::get('/offers/export', [\App\Http\Controllers\Admin\OfferController::class, 'export'])->name('offers.export');
                    Route::get('/offers', [\App\Http\Controllers\Admin\OfferController::class, 'index'])->name('offers.index');
                    Route::get('/offers/{id}', [\App\Http\Controllers\Admin\OfferController::class, 'show'])->name('offers.show');
                    Route::get('/offers/{id}/ednit', [\App\Http\Controllers\Admin\OfferController::class, 'edit'])->name('offers.edit');
                    Route::put('/offers/{id}', [\App\Http\Controllers\Admin\OfferController::class, 'update'])->name('offers.update');
                  //  Route::put('offers/{id}/changeOfferStatus', [\App\Http\Controllers\Admin\OfferController::class, 'changeOfferStatus'])->name('offers.changeOfferStatus');
                    Route::get('/gateway/export', [\App\Http\Controllers\Admin\GatewayController::class, 'export'])->name('gateway.export');
                    Route::resource('/gateway', '\App\Http\Controllers\Admin\GatewayController');
                    Route::post('/gateway/{id}/changeStatus', [\App\Http\Controllers\Admin\GatewayController::class, 'changeStatus'])->name('gateway.changeStatus');

                    Route::resource('/transactions', '\App\Http\Controllers\Admin\TransactionController')->except(['create','store','edit','show','update','destroy']);
                    Route::get('/transactions/export', [\App\Http\Controllers\Admin\TransactionController::class, 'export'])->name('transactions.export');
                    Route::get('/users/export', [\App\Http\Controllers\Admin\UserController::class, 'export'])->name('users.export');

                    Route::resource('/users', '\App\Http\Controllers\Admin\UserController');
                    Route::post('/users/{id}/changeStatus', [\App\Http\Controllers\Admin\UserController::class, 'changeStatus'])->name('users.changeStatus');
                    Route::resource('/ulists', '\App\Http\Controllers\Admin\UListController');
                    Route::post('/ulists/{id}/changeStatus', [\App\Http\Controllers\Admin\UListController::class, 'changeStatus'])->name('ulists.changeStatus');
                Route::get('/ulists/export', [\App\Http\Controllers\Admin\UListController::class, 'export'])->name('ulists.export');
                Route::get('/countries/export', [\App\Http\Controllers\Admin\CountryController::class, 'export'])->name('countries.export');
                Route::resource('/countries', '\App\Http\Controllers\Admin\CountryController');
                Route::post('/countries/{id}/changeStatus', [\App\Http\Controllers\Admin\CountryController::class, 'changeStatus'])->name('countries.changeStatus');

                Route::get('/factories/export', [\App\Http\Controllers\Admin\FactoryController::class, 'export'])->name('factories.export');
                    Route::resource('/factories', '\App\Http\Controllers\Admin\FactoryController');
                    Route::post('/factories/{id}/changeStatus', [\App\Http\Controllers\Admin\FactoryController::class, 'changeStatus'])->name('factories.changeStatus');
                    Route::get('/customers/export', [\App\Http\Controllers\Admin\CustomerController::class, 'export'])->name('customers.export');
                    Route::resource('/customers', '\App\Http\Controllers\Admin\CustomerController');
                    Route::post('/customers/{id}/changeStatus', [\App\Http\Controllers\Admin\CustomerController::class, 'changeStatus'])->name('customers.changeStatus');

                Route::get('/drivers/{id}/evaluate', [\App\Http\Controllers\Admin\DriverController::class, 'evaluates'])->name('drivers.evaluate');
                Route::get('/drivers/{id}/evaluate/changeStatus', [\App\Http\Controllers\Admin\DriverController::class, 'EvalchangeStatus'])->name('drivers.evaluate.changeStatus');
                Route::get('/drivers/export', [\App\Http\Controllers\Admin\DriverController::class, 'export'])->name('drivers.export');
                    Route::resource('/drivers', '\App\Http\Controllers\Admin\DriverController');
                    Route::post('/drivers/{id}/changeStatus', [\App\Http\Controllers\Admin\DriverController::class, 'changeStatus'])->name('drivers.changeStatus');

                Route::get('/companies/{id}/evaluate/changeStatus', [\App\Http\Controllers\Admin\CompanyController::class, 'EvalchangeStatus'])->name('companies.evaluate.changeStatus');
                Route::get('/companies/{id}/evaluate', [\App\Http\Controllers\Admin\CompanyController::class, 'evaluates'])->name('companies.evaluate');
                Route::get('/companies/export', [\App\Http\Controllers\Admin\CompanyController::class, 'export'])->name('companies.export');
                    Route::resource('/companies', '\App\Http\Controllers\Admin\CompanyController');
                    Route::post('/companies/{id}/changeStatus', [\App\Http\Controllers\Admin\CompanyController::class, 'changeStatus'])->name('companies.changeStatus');
                Route::get('/shipment/export', [\App\Http\Controllers\Admin\ShipmentTypeController::class, 'export'])->name('shipment.export');

                    Route::resource('/shipment', '\App\Http\Controllers\Admin\ShipmentTypeController');
                    Route::post('/shipment/{id}/changeStatus', [\App\Http\Controllers\Admin\ShipmentTypeController::class, 'changeStatus'])->name('shipment.changeStatus');

                Route::get('/trucks/export', [\App\Http\Controllers\Admin\CarController::class, 'export'])->name('trucks.export');
                    Route::resource('/trucks', '\App\Http\Controllers\Admin\CarController');
                    Route::post('/trucks/{id}/changeStatus', [\App\Http\Controllers\Admin\CarController::class, 'changeStatus'])->name('trucks.changeStatus');
                Route::get('/trucks/getById', [\App\Http\Controllers\Admin\CarController::class, 'getById'])->name('trucks.getById');
                    Route::prefix('settings')->name('setting.')->group(function () {

                        Route::get('/themes', [\App\Http\Controllers\Admin\SettingController::class, 'theme'])->name('theme');
                        Route::post('/themes/store', [\App\Http\Controllers\Admin\SettingController::class, 'storeTheme'])->name('theme.store');
                        Route::get('/general', [\App\Http\Controllers\Admin\SettingController::class, 'general'])->name('general');
                        Route::post('/general/store', [\App\Http\Controllers\Admin\SettingController::class, 'generalStore'])->name('general.store');
                        Route::get('/seo', [\App\Http\Controllers\Admin\SettingController::class, 'seo'])->name('seo');
                        Route::post('/seo/store', [\App\Http\Controllers\Admin\SettingController::class, 'seoStore'])->name('seo.store');
                        Route::get('/social', [\App\Http\Controllers\Admin\SettingController::class, 'social'])->name('social');
                        Route::post('/social/store', [\App\Http\Controllers\Admin\SettingController::class, 'socialStore'])->name('social.store');
                        Route::get('/costs', [\App\Http\Controllers\Admin\SettingController::class, 'costs'])->name('costs');
                        Route::post('/costs/store', [\App\Http\Controllers\Admin\SettingController::class, 'costsStore'])->name('costs.store');
                        Route::get('/api', [\App\Http\Controllers\Admin\SettingController::class, 'api'])->name('api');
                        Route::post('/api/store', [\App\Http\Controllers\Admin\SettingController::class, 'apiStore'])->name('api.store');
                    });
                Route::patch('/fcm-token', [\App\Http\Controllers\Admin\AdminController::class, 'updateToken'])->name('fcmToken');
                }
            );
    }
);
