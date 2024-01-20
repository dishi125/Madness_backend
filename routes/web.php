<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('admin',[\App\Http\Controllers\admin\AuthController::class,'index'])->name('admin.login');
Route::post('adminpostlogin', [\App\Http\Controllers\admin\AuthController::class, 'postLogin'])->name('admin.postlogin');
Route::get('logout', [\App\Http\Controllers\admin\AuthController::class, 'logout'])->name('admin.logout');

Route::group(['prefix'=>'admin','middleware'=>['auth','userpermission'],'as'=>'admin.'],function (){
    Route::get('dashboard',[\App\Http\Controllers\admin\DashboardController::class,'index'])->name('dashboard');

    Route::get('attributes',[\App\Http\Controllers\admin\AttributeController::class,'index'])->name('attributes.list');
    Route::post('addorupdateattribute',[\App\Http\Controllers\admin\AttributeController::class,'addorupdateattribute'])->name('attributes.addorupdate');
    Route::post('allattributeslist',[\App\Http\Controllers\admin\AttributeController::class,'allattributeslist'])->name('allattributeslist');
    Route::get('attribute/{id}/edit',[\App\Http\Controllers\admin\AttributeController::class,'editattribute'])->name('attributes.edit');
    Route::get('attribute/{id}/delete',[\App\Http\Controllers\admin\AttributeController::class,'deleteattribute'])->name('attributes.delete');
    Route::get('chageattributestatus/{id}',[\App\Http\Controllers\admin\AttributeController::class,'chageattributestatus'])->name('attributes.chageattributestatus');

    Route::get('attributeTerms/{id}',[\App\Http\Controllers\admin\AttributeTermsController::class,'index'])->name('attributeTerms.list');
    Route::post('addorupdateattributeTerm',[\App\Http\Controllers\admin\AttributeTermsController::class,'addorupdateattributeTerm'])->name('attributeTerms.addorupdate');
    Route::post('allattributesTermlist',[\App\Http\Controllers\admin\AttributeTermsController::class,'allattributesTermlist'])->name('allattributesTermlist');
    Route::get('chageattributeTermstatus/{id}',[\App\Http\Controllers\admin\AttributeTermsController::class,'chageattributeTermstatus'])->name('attributeTerms.chageattributeTermstatus');
    Route::get('attributeTerm/{id}/edit',[\App\Http\Controllers\admin\AttributeTermsController::class,'editattributeTerm'])->name('attributeTerms.edit');
    Route::get('attributeTerm/{id}/delete',[\App\Http\Controllers\admin\AttributeTermsController::class,'deleteattributeTerm'])->name('attributeTerms.delete');

    Route::get('users',[\App\Http\Controllers\admin\UserController::class,'index'])->name('users.list');
    Route::post('addorupdateuser',[\App\Http\Controllers\admin\UserController::class,'addorupdateuser'])->name('users.addorupdate');
    Route::post('alluserslist',[\App\Http\Controllers\admin\UserController::class,'alluserslist'])->name('alluserslist');
    Route::get('changeuserstatus/{id}',[\App\Http\Controllers\admin\UserController::class,'changeuserstatus'])->name('users.changeuserstatus');
    Route::get('users/{id}/edit',[\App\Http\Controllers\admin\UserController::class,'edituser'])->name('users.edit');
    Route::get('users/{id}/delete',[\App\Http\Controllers\admin\UserController::class,'deleteuser'])->name('users.delete');
    Route::get('users/{id}/permission',[\App\Http\Controllers\admin\UserController::class,'permissionuser'])->name('users.permission');
    Route::post('savepermission',[\App\Http\Controllers\admin\UserController::class,'savepermission'])->name('users.savepermission');

    Route::get('categories',[\App\Http\Controllers\admin\CategoryController::class,'index'])->name('categories.list');
    Route::get('categories/create',[\App\Http\Controllers\admin\CategoryController::class,'create'])->name('categories.add');
    Route::post('categories/save',[\App\Http\Controllers\admin\CategoryController::class,'save'])->name('categories.save');
    Route::post('allcategorylist',[\App\Http\Controllers\admin\CategoryController::class,'allcategorylist'])->name('allcategorylist');
    Route::get('changecategorystatus/{id}',[\App\Http\Controllers\admin\CategoryController::class,'changecategorystatus'])->name('categories.changecategorystatus');
    Route::get('categories/{id}/delete',[\App\Http\Controllers\admin\CategoryController::class,'deletecategory'])->name('categories.delete');
    Route::get('categories/{id}/edit',[\App\Http\Controllers\admin\CategoryController::class,'editcategory'])->name('categories.edit');
    Route::post('categories/uploadfile',[\App\Http\Controllers\admin\CategoryController::class,'uploadfile'])->name('categories.uploadfile');
    Route::post('categories/removefile',[\App\Http\Controllers\admin\CategoryController::class,'removefile'])->name('categories.removefile');
    Route::get('categories/checkparentcat/{id}',[\App\Http\Controllers\admin\CategoryController::class,'checkparentcat'])->name('categories.checkparentcat');

    Route::get('products',[\App\Http\Controllers\admin\ProductController::class,'index'])->name('products.list');
    Route::get('products/create',[\App\Http\Controllers\admin\ProductController::class,'create'])->name('products.add');
    Route::get('getAttrVariation/{id}',[\App\Http\Controllers\admin\ProductController::class,'getAttrVariation'])->name('getAttrVariation');
    Route::get('addVariantbox/{id}',[\App\Http\Controllers\admin\ProductController::class,'addVariantbox'])->name('addVariantbox');
    Route::post('products/save',[\App\Http\Controllers\admin\ProductController::class,'save'])->name('products.save');
    Route::post('variant/uploadfile',[\App\Http\Controllers\admin\ProductController::class,'uploadfile'])->name('products.uploadfile');
    Route::post('variant/removefile',[\App\Http\Controllers\admin\ProductController::class,'removefile'])->name('products.removefile');
    Route::post('allproductlist',[\App\Http\Controllers\admin\ProductController::class,'allproductlist'])->name('allproductlist');
    Route::get('products/{id}/edit',[\App\Http\Controllers\admin\ProductController::class,'editproduct'])->name('products.edit');
    Route::get('changeproductstatus/{id}',[\App\Http\Controllers\admin\ProductController::class,'changeproductstatus'])->name('products.changeproductstatus');
    Route::get('products/{id}/delete',[\App\Http\Controllers\admin\ProductController::class,'deleteproduct'])->name('products.delete');

    Route::get('end_users',[\App\Http\Controllers\admin\EndUserController::class,'index'])->name('end_users.list');
    Route::post('addorupdateEnduser',[\App\Http\Controllers\admin\EndUserController::class,'addorupdateEnduser'])->name('end_users.addorupdate');
    Route::post('allEnduserlist',[\App\Http\Controllers\admin\EndUserController::class,'allEnduserlist'])->name('allEnduserlist');
    Route::get('changeEnduserstatus/{id}',[\App\Http\Controllers\admin\EndUserController::class,'changeEnduserstatus'])->name('end_users.changeEnduserstatus');
    Route::get('end_users/{id}/edit',[\App\Http\Controllers\admin\EndUserController::class,'editEnduser'])->name('end_users.edit');
    Route::get('end_users/{id}/delete',[\App\Http\Controllers\admin\EndUserController::class,'deleteEnduser'])->name('end_users.delete');

    Route::get('settings',[\App\Http\Controllers\admin\SettingsController::class,'index'])->name('settings.list');
    Route::get('settings/user_discount_percentage/edit',[\App\Http\Controllers\admin\SettingsController::class,'editUserDiscountPercentage'])->name('settings.editUserDiscountPercentage');
    Route::post('updateUserDiscountPercentage',[\App\Http\Controllers\admin\SettingsController::class,'updateUserDiscountPercentage'])->name('settings.updateUserDiscountPercentage');
    Route::get('settings/shipping_cost/edit',[\App\Http\Controllers\admin\SettingsController::class,'editShippingCost'])->name('settings.editShippingCost');
    Route::post('updateShippingCost',[\App\Http\Controllers\admin\SettingsController::class,'updateShippingCost'])->name('settings.updateShippingCost');
    Route::get('settings/premium_user_membership_fee/edit',[\App\Http\Controllers\admin\SettingsController::class,'editPremiumUserMembershipFee'])->name('settings.editPremiumUserMembershipFee');
    Route::post('updatePremiumUserMembershipFee',[\App\Http\Controllers\admin\SettingsController::class,'updatePremiumUserMembershipFee'])->name('settings.updatePremiumUserMembershipFee');
    Route::get('settings/min_order_amount/edit',[\App\Http\Controllers\admin\SettingsController::class,'editMinOrderAmount'])->name('settings.editMinOrderAmount');
    Route::post('updateMinOrderAmount',[\App\Http\Controllers\admin\SettingsController::class,'updateMinOrderAmount'])->name('settings.updateMinOrderAmount');

    Route::get('homepagebanners',[\App\Http\Controllers\admin\HomePageBannerController::class,'index'])->name('homepagebanners.list');
    Route::get('homepagebanners/create',[\App\Http\Controllers\admin\HomePageBannerController::class,'create'])->name('homepagebanners.add');
    Route::post('homepagebanners/uploadfile',[\App\Http\Controllers\admin\HomePageBannerController::class,'uploadfile'])->name('homepagebanners.uploadfile');
    Route::post('homepagebanners/removefile',[\App\Http\Controllers\admin\HomePageBannerController::class,'removefile'])->name('homepagebanners.removefile');
    Route::post('homepagebanners/getBannerInfoVal',[\App\Http\Controllers\admin\HomePageBannerController::class,'getBannerInfoVal'])->name('homepagebanners.getBannerInfoVal');
    Route::post('homepagebanners/save',[\App\Http\Controllers\admin\HomePageBannerController::class,'save'])->name('homepagebanners.save');
    Route::post('allbannerlist',[\App\Http\Controllers\admin\HomePageBannerController::class,'allbannerlist'])->name('allbannerlist');
    Route::get('homepagebanners/{id}/edit',[\App\Http\Controllers\admin\HomePageBannerController::class,'editbanner'])->name('homepagebanners.edit');
    Route::get('homepagebanners/{id}/delete',[\App\Http\Controllers\admin\HomePageBannerController::class,'deletebanner'])->name('homepagebanners.delete');
    Route::get('homepagebanners/getproducts/{cat_id}',[\App\Http\Controllers\admin\HomePageBannerController::class,'getproducts'])->name('homepagebanners.getproducts');
    Route::get('changeBannerStatus/{id}',[\App\Http\Controllers\admin\HomePageBannerController::class,'changeBannerStatus'])->name('homepagebanners.changeBannerStatus');

    Route::get('adgroups',[\App\Http\Controllers\admin\AdGroupController::class,'index'])->name('adgroups.list');
    Route::get('adgroups/create',[\App\Http\Controllers\admin\AdGroupController::class,'create'])->name('adgroups.add');
    Route::post('adgroups/uploadfile',[\App\Http\Controllers\admin\AdGroupController::class,'uploadfile'])->name('adgroups.uploadfile');
    Route::post('adgroups/removefile',[\App\Http\Controllers\admin\AdGroupController::class,'removefile'])->name('adgroups.removefile');
    Route::post('adgroups/addBannerForm',[\App\Http\Controllers\admin\AdGroupController::class,'addBannerForm'])->name('adgroups.addBannerForm');
    Route::post('adgroups/save',[\App\Http\Controllers\admin\AdGroupController::class,'save'])->name('adgroups.save');
    Route::post('adgroups/getBannerInfoVal',[\App\Http\Controllers\admin\AdGroupController::class,'getBannerInfoVal'])->name('adgroups.getBannerInfoVal');
    Route::get('adgroups/getproducts/{cat_id}',[\App\Http\Controllers\admin\AdGroupController::class,'getproducts'])->name('adgroups.getproducts');
    Route::post('alladgroupslist',[\App\Http\Controllers\admin\AdGroupController::class,'alladgroupslist'])->name('alladgroupslist');
    Route::get('changeAdGroupstatus/{id}',[\App\Http\Controllers\admin\AdGroupController::class,'changeAdGroupstatus'])->name('adgroups.changeAdGroupstatus');
    Route::get('adgroups/{id}/delete',[\App\Http\Controllers\admin\AdGroupController::class,'deleteadgroup'])->name('adgroups.delete');
    Route::get('adgroups/{id}/edit',[\App\Http\Controllers\admin\AdGroupController::class,'editadgroup'])->name('adgroups.edit');

    Route::get('levels',[\App\Http\Controllers\admin\LevelController::class,'index'])->name('levels.list');
    Route::post('addorupdateLevel',[\App\Http\Controllers\admin\LevelController::class,'addorupdateLevel'])->name('levels.addorupdate');
    Route::post('allLevelList',[\App\Http\Controllers\admin\LevelController::class,'allLevelList'])->name('allLevelList');
    Route::get('levels/{id}/edit',[\App\Http\Controllers\admin\LevelController::class,'editLevel'])->name('levels.edit');

    Route::get('orders',[\App\Http\Controllers\admin\OrderController::class,'index'])->name('orders.list');
    Route::post('allOrderlist',[\App\Http\Controllers\admin\OrderController::class,'allOrderlist'])->name('allOrderlist');
    Route::post('updateOrdernote',[\App\Http\Controllers\admin\OrderController::class,'updateOrdernote'])->name('updateOrdernote');
    Route::get('viewOrder/{orderid}',[\App\Http\Controllers\admin\OrderController::class,'viewOrder'])->name('orders.view');
    Route::post('orders/save',[\App\Http\Controllers\admin\OrderController::class,'save'])->name('orders.save');
    Route::post('change_order_status',[\App\Http\Controllers\admin\OrderController::class,'change_order_status'])->name('change_order_status');
    Route::post('change_order_item_status',[\App\Http\Controllers\admin\OrderController::class,'change_order_item_status'])->name('change_order_item_status');
    Route::get('orders/pdf/{id}',[\App\Http\Controllers\admin\OrderController::class,'generate_pdf'])->name('orders.pdf');
    Route::get('orders/{order_id}/play_video',[\App\Http\Controllers\admin\OrderController::class,'order_play_video'])->name('orders.play_video');

    Route::get('coupons',[\App\Http\Controllers\admin\CouponController::class,'index'])->name('coupons.list');
    Route::get('coupons/create',[\App\Http\Controllers\admin\CouponController::class,'create'])->name('coupons.add');
    Route::post('coupons/save',[\App\Http\Controllers\admin\CouponController::class,'save'])->name('coupons.save');
    Route::post('allcouponlist',[\App\Http\Controllers\admin\CouponController::class,'allcouponlist'])->name('allcouponlist');
    Route::get('coupons/{id}/edit',[\App\Http\Controllers\admin\CouponController::class,'editcoupon'])->name('coupons.edit');
    Route::get('coupons/{id}/delete',[\App\Http\Controllers\admin\CouponController::class,'deletecoupon'])->name('coupons.delete');

    Route::get('return_requests',[\App\Http\Controllers\admin\OrderController::class,'return_requests'])->name('return_requests.list');
    Route::post('allReturnRequestlist',[\App\Http\Controllers\admin\OrderController::class,'allReturnRequestlist'])->name('allReturnRequestlist');
    Route::get('return_requests/{order_item_id}/play_video',[\App\Http\Controllers\admin\OrderController::class,'orderitem_play_video'])->name('return_requests.play_video');

    Route::get('collections',[\App\Http\Controllers\admin\CollectionController::class,'index'])->name('collections.list');
    Route::get('collections/create',[\App\Http\Controllers\admin\CollectionController::class,'create'])->name('collections.add');
    Route::post('collections/uploadfile',[\App\Http\Controllers\admin\CollectionController::class,'uploadfile'])->name('collections.uploadfile');
    Route::post('collections/removefile',[\App\Http\Controllers\admin\CollectionController::class,'removefile'])->name('collections.removefile');
    Route::post('collections/getCollectionInfoVal',[\App\Http\Controllers\admin\CollectionController::class,'getCollectionInfoVal'])->name('collections.getCollectionInfoVal');
    Route::post('collections/save',[\App\Http\Controllers\admin\CollectionController::class,'save'])->name('collections.save');
    Route::post('allcollectionlist',[\App\Http\Controllers\admin\CollectionController::class,'allcollectionlist'])->name('allcollectionlist');
    Route::get('collections/{id}/edit',[\App\Http\Controllers\admin\CollectionController::class,'editcollection'])->name('collections.edit');
    Route::get('collections/{id}/delete',[\App\Http\Controllers\admin\CollectionController::class,'deletecollection'])->name('collections.delete');
    Route::get('collections/getproducts/{cat_id}',[\App\Http\Controllers\admin\CollectionController::class,'getproducts'])->name('collections.getproducts');
    Route::get('changeCollectionStatus/{id}',[\App\Http\Controllers\admin\CollectionController::class,'changeCollectionStatus'])->name('collections.changeCollectionStatus');

    Route::get('monthly_commissions',[\App\Http\Controllers\admin\CommissionController::class,'index'])->name('monthly_commissions.list');
    Route::post('allMonthlyCommissionlist',[\App\Http\Controllers\admin\CommissionController::class,'allMonthlyCommissionlist'])->name('allMonthlyCommissionlist');
    Route::get('viewMonthlyCommission/{id}',[\App\Http\Controllers\admin\CommissionController::class,'viewMonthlyCommission'])->name('monthly_commissions.view');
    Route::post('allCommissionlist',[\App\Http\Controllers\admin\CommissionController::class,'allCommissionlist'])->name('allCommissionlist');
    Route::get('monthly_commissions/{id}/pay',[\App\Http\Controllers\admin\CommissionController::class,'pay_commission'])->name('monthly_commissions.pay_commission');

    Route::get('notifications',[\App\Http\Controllers\admin\NotificationController::class,'index'])->name('notifications.list');
    Route::get('notifications/create',[\App\Http\Controllers\admin\NotificationController::class,'create'])->name('notifications.add');
    Route::post('notifications/getNotificationInfoVal',[\App\Http\Controllers\admin\NotificationController::class,'getNotificationInfoVal'])->name('notifications.getNotificationInfoVal');
    Route::get('notifications/getproducts/{cat_id}',[\App\Http\Controllers\admin\NotificationController::class,'getproducts'])->name('notifications.getproducts');
    Route::post('notifications/uploadfile',[\App\Http\Controllers\admin\NotificationController::class,'uploadfile'])->name('notifications.uploadfile');
    Route::post('notifications/removefile',[\App\Http\Controllers\admin\NotificationController::class,'removefile'])->name('notifications.removefile');
    Route::post('notifications/save',[\App\Http\Controllers\admin\NotificationController::class,'save'])->name('notifications.save');
    Route::post('allnotificationlist',[\App\Http\Controllers\admin\NotificationController::class,'allnotificationlist'])->name('allnotificationlist');
    Route::get('notifications/{id}/edit',[\App\Http\Controllers\admin\NotificationController::class,'editnotification'])->name('notifications.edit');
    Route::get('notifications/{id}/delete',[\App\Http\Controllers\admin\NotificationController::class,'deletenotification'])->name('notifications.delete');
    Route::get('notifications/{id}/send',[\App\Http\Controllers\admin\NotificationController::class,'sendnotification'])->name('notifications.send');

    Route::get('faq',[\App\Http\Controllers\admin\FaqController::class,'index'])->name('faq.list');
    Route::get('faq/create',[\App\Http\Controllers\admin\FaqController::class,'create'])->name('faq.add');
    Route::post('faq/save',[\App\Http\Controllers\admin\FaqController::class,'save'])->name('faq.save');
    Route::post('allFaqlist',[\App\Http\Controllers\admin\FaqController::class,'allFaqlist'])->name('allFaqlist');
    Route::get('faq/{id}/edit',[\App\Http\Controllers\admin\FaqController::class,'edit'])->name('faq.edit');
    Route::get('faq/{id}/delete',[\App\Http\Controllers\admin\FaqController::class,'delete'])->name('faq.delete');

    Route::get('suggestions',[\App\Http\Controllers\admin\SuggestionController::class,'index'])->name('suggestions.list');
    Route::post('allSuggestionslist',[\App\Http\Controllers\admin\SuggestionController::class,'allSuggestionslist'])->name('allSuggestionslist');

    Route::get('order_report',[\App\Http\Controllers\admin\OrderReportController::class,'index'])->name('order_report.list');
    Route::post('allOrderReportlist',[\App\Http\Controllers\admin\OrderReportController::class,'allOrderReportlist'])->name('allOrderReportlist');

    Route::get('commission_report',[\App\Http\Controllers\admin\CommissionReportController::class,'index'])->name('commission_report.list');
    Route::post('allCommissionReportlist',[\App\Http\Controllers\admin\CommissionReportController::class,'allCommissionReportlist'])->name('allCommissionReportlist');
});

Route::group(['middleware'=>['auth']],function (){
    Route::get('profile',[\App\Http\Controllers\admin\ProfileController::class,'profile'])->name('profile');
    Route::get('profile/{id}/edit',[\App\Http\Controllers\admin\ProfileController::class,'edit'])->name('profile.edit');
    Route::post('profile/update',[\App\Http\Controllers\admin\ProfileController::class,'update'])->name('profile.update');
});

Route::get('admin/403_page',[\App\Http\Controllers\admin\AuthController::class,'invalid_page'])->name('admin.403_page');

