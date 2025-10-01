<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\ForgotPasswordController;
use App\Http\Controllers\API\Auth\CodeCheckController;
use App\Http\Controllers\API\Auth\ResetPasswordController;
use App\Http\Controllers\API\CarController;
use App\Http\Controllers\API\EvaluateController;
use App\Http\Controllers\API\OfferController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\PaymentMethodController;
use App\Http\Controllers\API\PayTransactionController;
use App\Http\Controllers\API\ShipmentTypeController;
use App\Http\Controllers\API\SupportCenterController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UListController;
use App\Http\Controllers\API\SettingController;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\PolicyController;
use App\Http\Controllers\API\CodeScretController;
use App\Services\FCMService;
use App\Http\Controllers\Admin\FcmTopicController;

// routes/api.php (or web.php if you prefer)
Route::post('/admin/fcm-subscribe', [FcmTopicController::class, 'subscribe'])
    ->middleware('auth:sanctum'); // or your admin guard


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
use App\Http\Controllers\API\OrderQRController;
    Route::prefix('cars')->group(function () {
        Route::get('/', [CarController::class, 'index']);
        Route::get('/{id}', [CarController::class, 'show']);
    });
Route::post('/orders/{id}/generate-qr', [OrderQRController::class, 'generateQR']);
Route::post('/orders/verify-qr', [OrderQRController::class, 'verifyQR']);

Route::get('/test-messages/{userId}', [OrderController::class, 'getTestMessages']);


Route::get('/test-notification', [OrderController::class, 'testNotification']);

Route::post('/save-fcm-token', [OrderController::class, 'saveFcmToken']);
Route::post('/send-web-notification', [OrderController::class, 'sendWebNotification']);

           Route::post('driver/arrived', [UserController::class, 'arrived']);
        Route::get('/policies', [PolicyController::class, 'index']);
        Route::post('/users/drivercompany/image/{id}', [RegisterController::class, 'registerDriverCompanyImage']);
        Route::get('/users/terms', [UserController::class, 'terms']);
        Route::get('/users/policy', [UserController::class, 'policy']);
        Route::post('/users/factory/image/{id}', [RegisterController::class, 'registerFactoryImage']);
Route::prefix('auth')->group(function () {
    Route::prefix('register')->group(function () {
        Route::post('user', [RegisterController::class, 'registerUser']);
        Route::post('factory', [RegisterController::class, 'registerFactory']);
        Route::post('driver', [RegisterController::class, 'registerDriver']);
        Route::post('drivercompany', [RegisterController::class, 'registerDriverCompany']);
    });
    Route::post('login', [RegisterController::class, 'login']);
});
Route::middleware('auth:api')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/sendOtp',   [RegisterController::class, 'sendOtp']);
        Route::post('/verifyOtp',   [RegisterController::class, 'verifyOtp']);
        Route::post('/verifyPhone/{id}', [UserController::class, 'verifyPhone']);
    });
    Route::get('getProfile', [RegisterController::class, 'userInfo']);
    Route::put('/editProfile', [RegisterController::class, 'userEdit']);
   Route::prefix('chat/')->group(function () {
        Route::get('/getByOrderId/{id}', [ChatController::class, 'getByOrderId']);
        Route::get('/getByUserId/{id}', [ChatController::class, 'getByUserId']);
        Route::post('/getNewMessagesId/{id}', [ChatController::class, 'getNewMessagesId']);
        Route::post('/sendMessage', [ChatController::class, 'store']);
   });
   Route::prefix('users/')->group(function () {

        Route::get('/driver/company-relation/{driver_id}', [UserController::class, 'driverCompanyRelation']);
        Route::get('/getNotifications/{id}', [UserController::class, 'getNotifications']);
        Route::put('/setLocation/{id}', [UserController::class, 'setLocation']);
        Route::get('/getLocation/{id}', [UserController::class, 'getLocation']);
        Route::post('/setOnline/{id}', [UserController::class, 'setOnline']);
        Route::get('/getOnline/{id}', [UserController::class, 'getOnline']);
        Route::post('setFcmToken/{id}', [UserController::class, 'updateUserFCMToken']);
        Route::get('normal', [UserController::class, 'showUsers']);
        Route::get('normal/{id}', [UserController::class, 'show']);
        Route::post('normal/image/{id}', [RegisterController::class, 'registerUserImage']);
        Route::put('normal/{id}', [UserController::class, 'updateUser']);
        Route::get('factories', [UserController::class, 'showFactories']);
        Route::get('factory/{id}', [UserController::class, 'showFactory']);
        Route::put('factory/{id}', [UserController::class, 'updateFactory']);
        Route::post('factoryinfo/{id}', [RegisterController::class, 'registerFactoryInfo']);
        Route::get('drivers', [UserController::class, 'showDrivers']);
        Route::get('/showCompanyDrivers/{id}', [UserController::class, 'showCompanyDrivers']);
        Route::get('driver/{id}', [UserController::class, 'showDriver']);
        Route::post('driverinfo/{id}', [RegisterController::class, 'registerDriverInfo']);
        Route::post('addDriverByCompany/{id}', [RegisterController::class, 'registerDriverFromCompany']);
        Route::post('driver/image/{id}',[RegisterController::class, 'registerDriverImage']);

//Some Platforms Faceing Issues WIth Patch * PUT So Here Same Methods With POST

        Route::post('driver-edit/{id}', [UserController::class, 'updateDriver']);
        Route::put('driver/{id}', [UserController::class, 'updateDriver']);
        Route::put('driver/changeStatus/{id}', [UserController::class, 'driverStatus']);
        Route::get('drivercompanies', [UserController::class, 'showDriverCompanies']);
        Route::get('drivercompany/{id}', [UserController::class, 'showDriverCompany']);
        Route::put('drivercompany/{id}', [UserController::class, 'updateDriverCompany']);
        Route::post('drivercompany-edit/{id}', [UserController::class, 'updateDriverCompany']);
        Route::post('drivercompanyinfo/{id}', [RegisterController::class, 'registerDriverCompanyInfo']);
        Route::get('/checkUserStatus', [UserController::class, 'checkUserStatus']); // /*add by mohammed*/
       Route::post('driver/arrived', [UserController::class, 'arrived']);

    });

    Route::prefix('shipmentTypes')->group(function () {
        Route::get('/', [ShipmentTypeController::class, 'index']);
        Route::get('/{id}', [ShipmentTypeController::class, 'show']);
    });
    Route::prefix('paymentMethods')->group(function () {
        Route::get('/', [PaymentMethodController::class, 'index']);
        Route::get('/{id}', [PaymentMethodController::class, 'show']);
    });

    Route::prefix('careers')->name('careers.')->group(
    function () {
        Route::get('/index', [\App\Http\Controllers\API\CareerController::class, 'index'])->name('index');
        Route::get('/show/{id}', [\App\Http\Controllers\API\CareerController::class, 'show'])->name('show');
        Route::post('/store', [\App\Http\Controllers\API\CareerController::class, 'store'])->name('store');
        Route::put('/update/{id}', [\App\Http\Controllers\API\CareerController::class, 'update'])->name('update');
        Route::post('/changeStatus/{id}', [\App\Http\Controllers\API\CareerController::class, 'changeStatus'])->name('changeStatus');
        Route::get('/getcategories', [\App\Http\Controllers\API\CareerController::class, 'getcategories'])->name('getcategories');
        }
    );

   Route::prefix('articles')->name('articles.')->group(
    function () {
       // Route::get('/index', [\App\Http\Controllers\API\ArticleController::class, 'index'])->name('index');
        //Route::get('/show/{id}', [\App\Http\Controllers\API\ArticleController::class, 'show'])->name('show');
        Route::post('/store', [\App\Http\Controllers\API\ArticleController::class, 'store'])->name('store');
        Route::put('/update/{id}', [\App\Http\Controllers\API\ArticleController::class, 'update'])->name('update');
        Route::put('/restore/{id}', [\App\Http\Controllers\API\ArticleController::class, 'restore'])->name('restore');
        Route::delete('/destroy/{id}', [\App\Http\Controllers\API\ArticleController::class, 'destroy'])->name('destroy');
        Route::post('/addimg', [\App\Http\Controllers\API\ArticleController::class, 'addimg']);
        Route::delete('/removeimg/{id}', [\App\Http\Controllers\API\ArticleController::class, 'removeimg']);
        Route::get('/getcategories', [\App\Http\Controllers\API\ArticleController::class, 'getcategories'])->name('getcategories');
        }
    );

Route::prefix('coupons')->name('coupons.')->group(
    function () {
        Route::get('/index', [\App\Http\Controllers\API\CouponController::class, 'index'])->name('index');
        Route::get('/show/{id}', [\App\Http\Controllers\API\CouponController::class, 'show'])->name('show');
        Route::post('/store', [\App\Http\Controllers\API\CouponController::class, 'store'])->name('store');
        Route::put('/update/{id}', [\App\Http\Controllers\API\CouponController::class, 'update'])->name('update');
        Route::put('/restore/{id}', [\App\Http\Controllers\API\CouponController::class, 'restore'])->name('restore');
        Route::delete('/destroy/{id}', [\App\Http\Controllers\API\CouponController::class, 'destroy'])->name('destroy');
    }
);

    Route::prefix('orders')->group(function () {
        Route::post('/sendNotificatonToDrivers/{id}',[OrderController::class,'sendNotificatonToDrivers']);
        Route::get('/getByUserId/{id}', [OrderController::class, 'getByUserId']);
        Route::get('/getByDriverId/{id}', [OrderController::class, 'getByDriverId']);

        Route::get('/getByStatus', [OrderController::class, 'getByStatus']);
        Route::get('/getPickUpAndDropDown', [OrderController::class, 'getPickUpAndDropDown']);
         Route::get('/getOrderDetails/{id}', [OfferController::class, 'getOrderDetails']);
        Route::post('/arrived', [OrderController::class, 'arrived']);
        Route::get('/orders-invites/driver/{driver_id}', [OrderController::class, 'getByDriverInvite']);

Route::get('/orders-invites/order/{order_id}', [OrderController::class, 'getByOrder']);
Route::get('/orders-invites/user/{user_id}', [OrderController::class, 'getByUser']);
Route::get('/orders-invites/orderD/{order_id}', [OrderController::class, 'getOrdersInvitesByOrderId']);

        Route::get('/getByStatusData', [OrderController::class, 'getByStatusData']);
        Route::get('/getLimtedOrders', [OrderController::class, 'getLimtedOrders']);
        Route::post('/', [OrderController::class, 'store']);
        Route::put('/{id}', [OrderController::class, 'update']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::put('/changeStatus/{id}', [OrderController::class, 'changeStatus']);
        Route::get('/tracking/{order}', [OrderController::class, 'Tracking_order']);

        /**
         *
         * @Add By   mdMandoinfo (Mohammed Hussein)
         * @github   https://github.com/mdMandoinfo
         * @linkedin https://www.linkedin.com/in/md-mohammed-408457242
         * @website  https://mdmando.info
        */
        Route::post('/generate-code', [CodeScretController::class, 'store']);

        /**
         *
         * @Add By   mdMandoinfo (Mohammed Hussein)
         * @github   https://github.com/mdMandoinfo
         * @linkedin https://www.linkedin.com/in/md-mohammed-408457242
         * @website  https://mdmando.info
        */
        Route::put('/check-code/{id}', [OrderController::class, 'checkOrderCode']);

    });
     Route::get('/read/qr/code', [OfferController::class, 'readAndUpdate']);

    Route::prefix('offers')->group(function () {
        Route::get('/getByUserId/{id}', [OfferController::class, 'getByUserId']);
        Route::get('/getByOrderId/{id}', [OfferController::class, 'getByOrderId']);
        Route::get('/getByDriverId/{id}', [OfferController::class, 'getByDriverId']);
       // Route::get('/getByDriverIdAssigned/{id}', [OfferController::class, 'getByDriverIdAssigned']);
        Route::get('/getByAgencyId/{id}', [OfferController::class, 'getByAgencyId']);

        Route::get('/getByStatus', [OfferController::class, 'getByStatus']);
        Route::post('/', [OfferController::class, 'store']);

        Route::put('/{id}', [OfferController::class, 'update']);
        Route::get('/{id}', [OfferController::class, 'show']);
        Route::put('/changeStatus/{id}', [OfferController::class, 'changeStatus']);


        //Company Assign Driver


      //  Route::post('/assignDriver', [OfferController::class, 'addAssignDriver']);
        Route::post('/assignDriver/{id}', [OrderController::class, 'sendNotificatonToDrivers']);

        Route::get('/assignDriver/{id}', [OfferController::class, 'getAssignedDrivers']);

    });
    Route::prefix('supportCenter')->group(function () {
        Route::get('/getByUserId/{id}', [SupportCenterController::class, 'getByUserId']);
        Route::post('/', [SupportCenterController::class, 'store']);
        Route::get('/{id}', [SupportCenterController::class, 'show']);
    });
        Route::prefix('qrCodeSecret')->group(function () {
         Route::post('/gen', [CodeScretController::class, 'store']);
    });
    Route::prefix('evaluates')->group(function () {
        Route::get('/showByOrder/{id}', [EvaluateController::class, 'showByOrder']);
        Route::get('/showByUser/{id}', [EvaluateController::class, 'showByUser']);
        Route::get('/showByUser2/{id}', [EvaluateController::class, 'showByUser2']);
        Route::get('/', [EvaluateController::class, 'index']);
        Route::post('/', [EvaluateController::class, 'store']);
        Route::put('/{id}', [EvaluateController::class, 'commentReplay']);
        Route::post('/check-rating', [EvaluateController::class, 'checkIfRatingExists']);

    });
    Route::prefix('ulists')->group(function () {
      //  Route::get('/showByOrder/{id}', [UListController::class, 'showByOrder']);
        //Route::get('/showByUser/{id}', [UListController::class, 'showByUser']);
        Route::get('/{id}', [UListController::class, 'show']);
        Route::get('/', [UListController::class, 'index']);
    //    Route::post('/', [UListController::class, 'store']);
      //  Route::put('/{id}', [UListController::class, 'commentReplay']);
    });
    Route::prefix('transactions')->group(function () {
        Route::post('/saveFawryStellement', [PayTransactionController::class, 'saveFawryStellement']);
        Route::post('/', [PayTransactionController::class, 'store']);
        Route::get('/showByOrder/{id}', [PayTransactionController::class, 'showByOrder']);
        Route::get('/showByUser/{id}', [PayTransactionController::class, 'showByUser']);
        Route::get('/showByFactory/{id}', [PayTransactionController::class, 'showByFactory']);
        Route::get('/showByDriver/{id}', [PayTransactionController::class, 'showByDriver']);
        Route::get('/showByDriverCompany/{id}', [PayTransactionController::class, 'showByDriverCompany']);
       // Route::get('/', [PayTransactionController::class, 'index']);
      //  Route::put('/{id}', [PayTransactionController::class, 'update']);
    });
    Route::prefix('settings')->group(function() {
        Route::get('/', [SettingController::class, 'index']);
    });
});

    Route::post('/auth/password/otp',  ForgotPasswordController::class);
    Route::post('/auth/password/checkCode', CodeCheckController::class);
    Route::post('/auth/password/reset', ResetPasswordController::class);
/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
 */
    ///////////////
   /* Route::prefix('clients/orders') ->group(function () {
//        Route::post('/sendNotificatonToDrivers/{id}',[OrderController::class,'sendNotificatonToDrivers']);
        Route::get('/getByUserId/{id}', [App\Http\Controllers\Clients\OrderController::class, 'getByUserId']);
        Route::get('/getByDriverId', [App\Http\Controllers\Clients\OrderController::class, 'getByDriverId']);
        Route::post('/getByStatus', [App\Http\Controllers\Clients\OrderController::class, 'getByStatus']);
        Route::post('/', [App\Http\Controllers\Clients\OrderController::class, 'store']);
        Route::put('/{id}', [OrderController::class, 'update']);
        Route::get('/{id}', [App\Http\Controllers\Clients\OrderController::class, 'show']);
        Route::put('/changeStatus/{id}', [App\Http\Controllers\Clients\OrderController::class, 'changeStatus']);
    });*/

  /* Route::prefix('clients/offers')->group(function () {
        Route::get('/getByUserId', [App\Http\Controllers\Clients\OfferController::class, 'getByUserId']);
        Route::get('/getByOrderId/{id}', [App\Http\Controllers\Clients\OfferController::class, 'getByOrderId']);
        Route::get('/getByDriverId/{id}', [App\Http\Controllers\Clients\OfferController::class, 'getByDriverId']);
        Route::get('/getByStatus', [App\Http\Controllers\Clients\OfferController::class, 'getByStatus']);
        Route::post('/', [App\Http\Controllers\Clients\OfferController::class, 'store']);
        Route::put('/{id}', [App\Http\Controllers\Clients\OfferController::class, 'update']);
        Route::get('/{id}', [App\Http\Controllers\Clients\OfferController::class, 'show']);
        Route::put('/changeStatus/{id}', [App\Http\Controllers\Clients\OfferController::class, 'changeStatus']);
    });*/

 /*   Route::prefix('clients/transactions')->group(function () {
        Route::post('/', [App\Http\Controllers\Clients\PayTransactionController::class, 'store']);
        Route::get('/showByOrder/{id}', [App\Http\Controllers\Clients\PayTransactionController::class, 'showByOrder']);
        Route::get('/showByUser/{id}', [App\Http\Controllers\Clients\PayTransactionController::class, 'showByUser']);
        Route::get('/showByFactory/{id}', [App\Http\Controllers\Clients\PayTransactionController::class, 'showByFactory']);
        Route::get('/showByDriver/{id}', [App\Http\Controllers\Clients\PayTransactionController::class, 'showByDriver']);
        Route::get('/showByDriverCompany/{id}', [App\Http\Controllers\Clients\PayTransactionController::class, 'showByDriverCompany']);
    });
    Route::prefix('clients/evaluates')->group(function () {
        Route::get('/showByOrder/{id}', [App\Http\Controllers\Clients\EvaluateController::class, 'showByOrder']);
        Route::get('/showByUser/{id}', [App\Http\Controllers\Clients\EvaluateController::class, 'showByUser']);
        Route::get('/showByUser2/{id}', [App\Http\Controllers\Clients\EvaluateController::class, 'showByUser2']);
        Route::get('/', [App\Http\Controllers\Clients\EvaluateController::class, 'index']);
        Route::post('/', [App\Http\Controllers\Clients\EvaluateController::class, 'store']);
        Route::put('/{id}', [App\Http\Controllers\Clients\EvaluateController::class, 'commentReplay']);
    });*/
    /*
Route::get('/pickup/{token}', [App\Http\Controllers\Clients\QRController::class,'confirmPickup'])->name('pickup.confirm');
Route::get('/qrcode/{token}', [App\Http\Controllers\Clients\QRController::class,'generateQrCode'])->name('pickup.qrcode');
Route::get('/processQRCode', [App\Http\Controllers\Clients\QRController::class,'processQRCode']);*/
///
   /*Route::prefix('clients/users/')->group(function () {
    Route::get('drivers', [App\Http\Controllers\Clients\UserController::class, 'showDrivers']);
    Route::get('changeStatus', [App\Http\Controllers\Clients\UserController::class, 'ChangeStatus']);
});*/
   Route::prefix('articles')->group(
    function () {
        Route::get('/index', [\App\Http\Controllers\API\ArticleController::class, 'index'])->name('index');
        Route::get('/show/{id}', [\App\Http\Controllers\API\ArticleController::class, 'show'])->name('show');});

    Route::prefix('careers')->name('careers.')->group(
    function () {
        Route::get('/index', [\App\Http\Controllers\API\CareerController::class, 'index'])->name('index');
        Route::get('/show/{id}', [\App\Http\Controllers\API\CareerController::class, 'show'])->name('show');});


use App\Notifications\FcmPushNotification;
use Illuminate\Support\Facades\Notification;
Route::get('/test-fcm', function () {
    $testToken = 'fMYK1Y4aImtQRe5Tqhru6A:APA91bGaUdFv2G_U5nuiHhjrWfrzpMrKgQ2sxPgh8NRy1-c56KWwrqaOm4GAQtFwgJuQ2-L4gVcO39b8TGIXhdxd96AMI4N4FkcFyOFkGix-sqw_KL4tzZg';

    $notification = [
        'title' => 'Test Notification',
        'body'  => 'This is a test from Laravel',
        'sound' => 'default'
    ];

 Notification::send($testToken, new FcmPushNotification('Test Notification', 'This is a test from Laravel', [ $testToken]));
    return 'Notification sent!';
});
