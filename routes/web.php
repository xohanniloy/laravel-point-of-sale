<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\SessionAuthenicate;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;

Route::get( '/', [HomeController::class, 'homePage'] )->name( 'home' );

Route::post( '/user-registration', [UserController::class, 'userRegistration'] )->name( 'user.registration' );
Route::post( '/user-login', [UserController::class, 'userLogin'] )->name( 'user.login' );
Route::post( '/send-otp', [UserController::class, 'sendOtp'] )->name( 'send.otp' );
Route::post( '/verify-otp', [UserController::class, 'verifyOtp'] )->name( 'verify.otp' );

Route::middleware( [SessionAuthenicate::class] )->group( function () {
    Route::get( '/dashboard', [UserController::class, 'dashboardPage'] )->name( 'dashboard.page' );
    Route::get( '/user-logout', [UserController::class, 'userLogout'] )->name( 'user.logout' );
    Route::post( '/reset-password', [UserController::class, 'resetPassword'] )->name( 'reset.password' );

    // Category All Route
    Route::post( '/create-category', [CategoryController::class, 'createCategory'] )->name( 'create.category' );
    Route::get( '/category-list', [CategoryController::class, 'categoryList'] )->name( 'category.list' );
    Route::post( '/category-by-id', [CategoryController::class, 'categoryById'] )->name( 'category.by.id' );
    Route::post( '/update-category', [CategoryController::class, 'updateCategory'] )->name( 'update.category' );
    Route::get( '/delete-category/{id}', [CategoryController::class, 'deleteCategory'] )->name( 'delete.category' );
    //CAtegory page Route
    Route::get( '/category-page', [CategoryController::class, 'categoryPage'] )->name( 'category.page' );
    Route::get( '/category-save', [CategoryController::class, 'categorySavePage'] )->name( 'category.save' );

    // Product All Route
    Route::post( '/create-product', [ProductController::class, 'createProduct'] )->name( 'create.product' );
    Route::get( '/product-list', [ProductController::class, 'productList'] )->name( 'product.list' );
    Route::post( '/product-by-id', [ProductController::class, 'productById'] )->name( 'product.by.id' );
    Route::post( '/update-product', [ProductController::class, 'updateProduct'] )->name( 'update.product' );
    Route::get( '/delete-product/{id}', [ProductController::class, 'deleteProduct'] )->name( 'delete.product' );
    Route::get( '/product-page', [ProductController::class, 'productPage'] )->name( 'product.page' );
    Route::get( '/product-save', [ProductController::class, 'productSavePage'] )->name( 'product.save' );

    // Customer All Route
    Route::post( '/create-customer', [CustomerController::class, 'createCustomer'] )->name( 'create.customer' );
    Route::get( '/customer-list', [CustomerController::class, 'customerList'] )->name( 'customer.list' );
    Route::post( '/customer-by-id', [CustomerController::class, 'customerById'] )->name( 'customer.by.id' );
    Route::post( '/update-customer', [CustomerController::class, 'updateCustomer'] )->name( 'update.customer' );
    Route::get( '/delete-customer/{id}', [CustomerController::class, 'deleteCustomer'] )->name( 'delete.customer' );
    Route::get( '/customer-page', [CustomerController::class, 'customerPage'] )->name( 'customer.page' );
    Route::get( '/customer-save', [CustomerController::class, 'customerSavePage'] )->name( 'customer.save' );

    // Invoice All Route
    Route::post( '/create-invoice', [InvoiceController::class, 'createInvoice'] )->name( 'create.invoice' );
    Route::get( '/invoice-list', [InvoiceController::class, 'invoiceList'] )->name( 'invoice.list' );
    Route::get( '/invoice-details', [InvoiceController::class, 'invoiceDetails'] )->name( 'invoice.details' );
    Route::get( '/delete-invoice/{id}', [InvoiceController::class, 'deleteInvoice'] )->name( 'delete.invoice' );
    Route::get( '/invoice-page', [InvoiceController::class, 'invoicePage'] )->name( 'invoice.page' );

    //sale route
    Route::get('/create-sale', [SaleController::class, 'createSalePage'])->name('create.sale.page');

    //Dashboard Route
    Route::get( '/dashboard-summary', [DashboardController::class, 'dashboardSummery'] )->name( 'dashboard.summery' );

    //Reset Password
    Route::get( '/reset-password', [UserController::class, 'resetPasswordPage'] )->name( 'reset.password.page' );

    Route::get('/profile', [UserController::class, 'profilePage'])->name('profile.page');
    Route::post('/update-profile', [UserController::class, 'updateProfile'])->name('update.profile');
} );

//All PageRoute
Route::get( '/login', [UserController::class, 'loginPage'] )->name( 'login.page' );
Route::get( '/register', [UserController::class, 'registerPage'] )->name( 'register.page' );
Route::get( '/send-otp-page', [UserController::class, 'sendOtpPage'] )->name( 'send.otp.page' );
Route::get( '/verify-otp', [UserController::class, 'verifyOtpPage'] )->name( 'verify.otp.page' );
