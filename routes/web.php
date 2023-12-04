<?php

use App\Pos;
use App\Product;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



Route::prefix('back')->middleware(['auth'])->group(function () {
    /**
     * *****
     * Unit
     * *****
     */
    
    Route::get('unit/{unit}/get_related','UnitController@get_related')->name('unit.get_related');
    Route::resource('unit', UnitController::class)->except(['show']);

    
    /**
     * *********************
     * Owner & Bank Account
     * *********************
     */
    Route::resource('owners', 'OwnerController')->except(['show']);
    Route::get('bank_account/add_money/{bank_account}', 'BankAccountController@add_money')->name('bank_account.add_money');
    Route::post('bank_account/add_money', 'BankAccountController@add_money_store')->name('bank_account.add_money.store');
    Route::get('bank_account/withdraw_money/{bank_account}', 'BankAccountController@withdraw_money')->name('bank_account.withdraw_money');
    Route::post('bank_account/withdraw_money', 'BankAccountController@withdraw_money_store')->name('bank_account.withdraw_money.store');
    Route::resource('bank_account', 'BankAccountController');
    Route::get('bank_account/transfer/{account}', 'BankAccountController@transfer')->name('bank_account.transfer');
    Route::post('bank_account/transfer/', 'BankAccountController@transfer_store')->name('bank_account.transfer_store');
    Route::get('bank_account/history/{account}', 'BankAccountController@history')->name('bank_account.history');


    /**
     * ****************
     * Brand & Category
     * ****************
     */
    Route::resource('brand', 'BrandController')->except('show');
    Route::resource('category', 'CategoryController')->except('show');

    /**
     * ********
     * PRODUCT
     * ********
     */
    Route::resource('product', 'ProductController')->except('show');
    Route::get('product/sell_history/{product}', 'ProductController@sell_history')->name('product.sell_history');
    /*ajax*/Route::get('product/categories', 'ProductController@categories')->name('product.categories');
    /*ajax*/Route::get('product/brands', 'ProductController@brands')->name('product.brands');
    /*ajax*/Route::get('product/{product}/details', 'ProductController@details')->name('product.details');

    Route::get('product/add_category', 'ProductController@add_category')->name('product.add_category');
    Route::post('product/add_category', 'ProductController@store_category');


    Route::get('product/add_brand', 'ProductController@add_brand')->name('product.add_brand');
    Route::post('product/add_brand', 'ProductController@store_brand');

    /*ajax*/Route::get('product-search', 'PosController@product_search_by_name')->name('product-search');
    /*ajax*/Route::get('product-code-search', 'PosController@product_search_by_code')->name('product-code-search');

    // barcode
    Route::get('product-barcode/{code}', 'ProductController@barcode_generate')->name('product.barcode');


    /**
     * *********
     * PURCHASE
     * *********
     */
    Route::get('purchase/add_payment/{purchase}', 'PurchaseController@add_payment')->name('purchase.add_payment');
    Route::post('purchase/add_payment/{purchase}', 'PurchaseController@store_payment');
    Route::get('purchase/add_supplier', 'PurchaseController@add_supplier')->name('purchase.add_supplier');
    Route::post('purchase/add_supplier', 'PurchaseController@store_supplier');
    Route::resource('purchase', 'PurchaseController');

    Route::get('purchase-receipt/{id}', 'PurchaseController@receipt')->name('purchase.receipt');
    /*purchase/edit*/Route::post('purchase/partial-destroy/{id}', 'PurchaseController@partial_destroy')->name('purchase.partial_destroy');


    /**
     * ******
     * POS
     * ******
     */
    Route::get('pos/purchase-cost-breakdown/{pos}', 'PosController@purchase_cost_breakdown')->name('pos.purchase_cost_breakdown');

    Route::get('pos/add_payment/{pos}', 'PosController@add_payment')->name('pos.add_payment');
    Route::post('pos/add_payment/{pos}', 'PosController@store_payment');

    Route::get('pos/add_customer', 'PosController@add_customer')->name('pos.add_customer');
    Route::post('pos/add_customer', 'PosController@store_customer');

    Route::resource('pos', 'PosController');
    /*pos/edit*/Route::post('pos/partial-destroy/{id}', 'PosController@partial_destroy')->name('pos.partial_destroy');
    /*pos/create*/Route::get('pos-products', 'PosController@pos_products')->name('pos.products');
    Route::get('pos-receipt/{pos_id}', 'PosController@pos_receipt')->name('pos_receipt');
    Route::get('chalan-receipt/{pos_id}', 'PosController@chalan_receipt')->name('chalan_receipt');



    /**
     * ***********
     * Pos Return
     * ***********
     */
    Route::get('return/add_payment/{return}', 'OrderReturnController@add_payment')->name('return.add_payment');
    Route::post('return/add_payment/{return}', 'OrderReturnController@store_payment');

    Route::get('return/{pos}', 'OrderReturnController@create')->name('pos.return');
    Route::post('return/{pos}', 'OrderReturnController@store');
    Route::resource('return', 'OrderReturnController')->except(['create', 'store', 'edit', 'update']);


    /**
     * ******************************
     * Peoples -> Customer & Supplier
     * ******************************
     */
    Route::get('customer/wallet_payment/{customer}', 'CustomerController@wallet_payment')->name('customer.wallet_payment');
    Route::post('customer/wallet_payment/{customer}', 'CustomerController@store_wallet_payment');
    Route::resource('customer', 'CustomerController')->except('show');
    Route::get('customer/{customer}/report','CustomerController@report')->name('customer.report');

    // Supplier
    Route::get('supplier/wallet_payment/{supplier}', 'SupplierController@wallet_payment')->name('supplier.wallet_payment');
    Route::post('supplier/wallet_payment/{supplier}', 'SupplierController@store_wallet_payment');
    Route::resource('supplier', 'SupplierController')->except('show');
    Route::get('supplier/{supplier}/report','SupplierController@report')->name('supplier.report');


    
    /*ajax*/Route::get('customers', 'CustomerController@customers')->name('get_customers');
    /*ajax*/Route::get('customer-due/{id}', 'CustomerController@customer_due')->name('customer_due');
    /*ajax*/Route::get('suppliers', 'SupplierController@suppliers')->name('get_suppliers');
    /*ajax*/Route::get('supplier-due/{id}', 'SupplierController@supplier_due')->name('supplier_due');

    /**
     * ********
     * Expense
     * ********
     */
    Route::resource('expense-category', 'ExpenseCategoryController')->except(['create','show']);
    Route::resource('expense', 'ExpenseController')->except('show');


    /**
     * ******
     * Stock
     * ******
     */
    Route::get('stock', 'StockController@index')->name('stock.index');


    /**
     * *********
     * Payments
     * *********
     */
    // Route::get('payment', 'PaymentController@index')->name('payment.index');
    // Route::get('payment/create', 'PaymentController@create')->name('payment.create');
    // Route::post('payment', 'PaymentController@store')->name('payment.store');
    // Route::delete('payment/{payment}', 'PaymentController@destroy')->name('payment.destroy');
    Route::resource('payment','PaymentController')->except('show','edit','update');
    Route::get('payment-receipt/{actual_payment}', 'PaymentController@payment_receipt')->name('payment_receipt');

    /*payment_delete*/Route::delete('payment/partial_delete/{payment}', 'PaymentController@partial_delete')->name('payment.partial_delete');

    // Payment Method
    // Route::resource('payment_method', 'PaymentMethodController');




    /**
     * *******
     * Damage
     * *******
     */
    Route::resource('damage', 'DamageController')->except('show');




    
    /**
     * ****************
     * Promotional SMS
     * ****************
     */
    Route::get('promotional-sms', 'PromotionController@promotion_sms')->name('promotion.sms');
    Route::post('promotional-sms-send', 'PromotionController@send_promotion_sms')->name('send.promotion.sms');
    
        /**
     * ********
     * Reports
     * ********
     */
    Route::get('report/today_report', 'ReportController@today_report')->name('today_report');
    Route::get('report/current_month_report', 'ReportController@current_month_report')->name('current_month_report');
    Route::get('report/summary-report', 'ReportController@summary_report')->name('summary_report');
	Route::get('report/daily_report', 'ReportController@daily_report')->name('daily_report');
    Route::get('report/customer_due', 'ReportController@customer_due')->name('report.customer_due');
    Route::get('report/supplier_due', 'ReportController@supplier_due')->name('report.supplier_due');
    Route::get('report/low_stock', 'ReportController@low_stock')->name('report.low_stock');
    Route::get('report/top_buying_customer', 'ReportController@top_customer')->name('report.top_customer');
    Route::get('report/top_selling_product', 'ReportController@top_product')->name('report.top_product');
    Route::get('report/top-selling-product-alltime', 'ReportController@top_product_all_time')->name('report.top_product_all_time');
    Route::get('report/purchase_report', 'ReportController@purchase_report')->name('report.purchase_report');
    Route::get('report/customer_ledger', 'ReportController@customer_ledger')->name('report.customer_ledger');
    Route::get('report/supplier_ledger', 'ReportController@supplier_ledger')->name('report.supplier_ledger');
    Route::get('report/profit_loss_report', 'ReportController@profit_loss_report')->name('report.profit_loss_report');


    /**
     * **********
     * Settings
     * **********
     */
    // Route::get('setting', 'SettingController@index')->name('apps.setting');
    // Route::post('setting', 'SettingController@setting_update')->name('apps.setting_update');
    Route::get('setting', 'SettingController@create_pos_setting')->name('pos.pos_setting');
    Route::post('setting', 'SettingController@update_pos_setting')->name('pos.pos_setting_update');
    Route::get('/backup', 'HomeController@backup')->name('backup');

    /**
     * *****************
     * Role & Permission
     * *****************
     */
    Route::resource('roles', 'RoleController')->except('show');
    Route::resource('role_permissions', 'RolePermissionController')->parameters([
        'role_permissions' => 'role'
    ])->only('edit', 'update');

    Route::resource('users','UserController')->except('show');


    /**
     * *********
     * Profile
     * *********
     */

    Route::get('profile', 'ProfileController@index')->name('profile.index');
    Route::post('profile', 'ProfileController@update')->name('profile.update');
    Route::get('change-password', 'ProfileController@change_password')->name('change.password');
    Route::post('update-password', 'ProfileController@update_password')->name('update.password');

});
// Axios Request data
// Route::get('customers', 'CustomerController@customers');
// Route::get('ajax-products', 'ProductController@products')->name('ajax-products');

Route::get('/', 'HomeController@front_home');

Auth::routes();

Route::get('/back', 'HomeController@index')->name('admin');

Route::get('clear', 'MaintenanceController@cache_clear');
Route::get('db_reset', 'MaintenanceController@reset_software');
Route::get('optimize', 'MaintenanceController@optimize');






