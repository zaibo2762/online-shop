<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\PageController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\SettingController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\ShippingController;
use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\TempImagesController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\DiscountCodeController;
use App\Http\Controllers\admin\ProductSubCategoryController;

// Route::get('/', function () {
//     return view('welcome');
// });



// Route::get('/test', function () {
//     orderEmail(14);
// });

Route::get('/', [FrontController::class, 'index'])->name('front.home');
Route::get('/shop/{ctaegorySlug?}/{subCategorySlug?}', [ShopController::class, 'index'])->name('front.shop');
Route::get('/product/{slug}', [ShopController::class, 'product'])->name("front.product");
//cart routes
Route::get('/cart', [CartController::class, 'cart'])->name("front.cart");
Route::post('/addtocart', [CartController::class, 'addToCart'])->name("front.addToCart");
Route::post('/updatecart', [CartController::class, 'updateCart'])->name("front.updateCart");
Route::post('/deleteItem', [CartController::class, 'deleteItem'])->name("front.deleteItem.cart");
//checkout routes
Route::get('/checkout', [CartController::class, 'checkOut'])->name("front.checkout");
Route::post('/processcheckout', [CartController::class, 'processCheckout'])->name("front.processCheckout");
Route::get('/thanks/{orderId}', [CartController::class, 'thankyou'])->name("front.thanks");
Route::post('/get-order-summary', [CartController::class, 'getOrderSummary'])->name("front.ordersummary");
//coupon code routes
Route::post('/apply-discount', [CartController::class, 'applyDiscount'])->name("front.applydiscount");
Route::post('/remove-discount', [CartController::class, 'removeCoupon'])->name("front.removediscount");
//wishlist routes
Route::post('/add-to-wishlist', [FrontController::class, 'addToWishlist'])->name("front.addToWishlist");
//Static Pages
Route::get('/page/{slug}', [FrontController::class, 'page'])->name("front.page");

Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name("front.forgotPassword");
Route::post('/process-forgot-password', [AuthController::class, 'processForgotPassword'])->name("front.processForgotPassword");
Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name("front.resetPassword");
Route::post('/process-reset-password', [AuthController::class, 'processResetPassword'])->name("front.processResetPassword");

//rating route
Route::post('/save-rating/{productId}', [ShopController::class, 'saveRating'])->name("front.saveRating");

Route::post('/send-contact-email', [FrontController::class, 'sendContactEmail'])->name("front.sendContactEmail");
//User Registration
Route::group(['prefix' => 'account'], function () {
    Route::group(['middleware' => 'guest'], function () {
        Route::get('/register', [AuthController::class, 'register'])->name('account.register');
        Route::post('/process-register', [AuthController::class, 'processRegister'])->name('account.processRegister');
        Route::get('/login', [AuthController::class, 'logIn'])->name('account.login');
        Route::post('/login', [AuthController::class, 'authenticate'])->name('account.authenticate');
    });
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/profile', [AuthController::class, 'profile'])->name('account.profile');
        Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('account.changePassword');
        Route::post('/process-change-password', [AuthController::class, 'changePassword'])->name('account.processChangePassword');
        Route::post('/update-profile', [AuthController::class, 'updateProfile'])->name('account.updateProfile');
        Route::post('/update-address', [AuthController::class, 'updateAddress'])->name('account.updateAddress');
        Route::get('/myorders', [AuthController::class, 'orders'])->name('account.myorders');
        Route::get('/orderdetail/{orderId}', [AuthController::class, 'orderDetail'])->name('account.orderDetail');
        Route::get('/logout', [AuthController::class, 'logOut'])->name('account.logout');
        Route::get('/my-wishlist', [AuthController::class, 'wishlist'])->name('account.wishlist');
        Route::post('/my-product-from-wishlist', [AuthController::class, 'removeProductFromWishlist'])->name('account.removeProductFromWishlist');
    });
});


Route::group(['prefix' => 'admin'], function () {
    Route::group(['middleware' => 'admin.guest'], function () {
        Route::get('/login', [AdminLoginController::class, 'index'])->name('admin.login');
        Route::post('/authenticate', [AdminLoginController::class, 'authenticate'])->name('admin.authenticate');
    });
    Route::group(['middleware' => 'admin.auth'], function () {
        Route::get('/dashboard', [HomeController::class, 'index'])->name('admin.dashboard');
        Route::get('/logout', [HomeController::class, 'logout'])->name('admin.logout');
        //Category Routes
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.delete');

        //sub category routes
        Route::get('/sub-categories', [SubCategoryController::class, 'index'])->name('sub-categories.index');
        Route::get('/sub-categories/create', [SubCategoryController::class, 'create'])->name('sub-categories.create');
        Route::post('/sub-categories', [SubCategoryController::class, 'store'])->name('sub-categories.store');
        Route::get('/sub-categories/{subCategory}/edit', [SubCategoryController::class, 'edit'])->name('sub-categories.edit');
        Route::put('/sub-categories/{subCategory}', [SubCategoryController::class, 'update'])->name('sub-categories.update');
        Route::delete('/sub-categories/{subCategory}', [SubCategoryController::class, 'destroy'])->name('sub-categories.delete');


        //Brands Routes
        Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
        Route::get('/brands/create', [BrandController::class, 'create'])->name('brands.create');
        Route::get('/brands/{brands}/edit', [BrandController::class, 'edit'])->name('brands.edit');
        Route::put('/brands/{brands}', [BrandController::class, 'update'])->name('brands.update');
        Route::post('/brands', [BrandController::class, 'store'])->name('brands.store');
        Route::delete('/brands/{brands}', [BrandController::class, 'destroy'])->name('brands.delete');

        //Products Routes
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{products}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{products}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{products}', [ProductController::class, 'destroy'])->name('products.delete');
        Route::get('/product-subcategories', [ProductSubCategoryController::class, 'index'])->name('product-subcategories.index');
        Route::get('/get-products', [ProductController::class, 'getProducts'])->name('products.getProducts');
        //shipping routes
        Route::get('/shipping/create', [ShippingController::class, 'create'])->name('shipping.create');
        Route::post('/shipping', [ShippingController::class, 'store'])->name('shipping.store');
        Route::get('/shipping/{id}', [ShippingController::class, 'edit'])->name('shipping.edit');
        Route::put('/shipping/{id}', [ShippingController::class, 'update'])->name('shipping.update');
        Route::delete('/shipping/{id}', [ShippingController::class, 'destroy'])->name('shipping.delete');
        //Coupon code Routes
        Route::get('/coupon', [DiscountCodeController::class, 'index'])->name('coupons.index');
        Route::get('/coupons/create', [DiscountCodeController::class, 'create'])->name('coupons.create');
        Route::post('/coupons', [DiscountCodeController::class, 'store'])->name('coupons.store');
        Route::get('/coupons/{coupon}/edit', [DiscountCodeController::class, 'edit'])->name('coupons.edit');
        Route::put('/coupons/{coupon}', [DiscountCodeController::class, 'update'])->name('coupons.update');
        Route::delete('/coupons/{coupons}', [DiscountCodeController::class, 'destroy'])->name('coupons.delete');

        //Order Routes
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'detail'])->name('orders.detail');
        Route::post('/orders/change-status/{id}', [OrderController::class, 'changeOrderStatus'])->name('orders.changeOrderStatus');
        Route::post('/orders/send-email/{id}', [OrderController::class, 'sendInvoiceEmail'])->name('orders.sendInvoiceEmail');

        //user routes
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::get('/users/{users}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{users}', [UserController::class, 'update'])->name('users.update');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::delete('/users/{users}', [UserController::class, 'destroy'])->name('users.delete');
        //page routes
        Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
        Route::get('/pages/create', [PageController::class, 'create'])->name('pages.create');
        Route::get('/pages/{pages}/edit', [PageController::class, 'edit'])->name('pages.edit');
        Route::put('/pages/{pages}', [PageController::class, 'update'])->name('pages.update');
        Route::post('/pages', [PageController::class, 'store'])->name('pages.store');
        Route::delete('/pages/{pages}', [PageController::class, 'destroy'])->name('pages.delete');

        //settings routes
        Route::get('/change-password', [SettingController::class, 'showChangePasswordForm'])->name('admin.showChangePasswordForm');
        Route::post('/process-change-password', [SettingController::class, 'processChangePassword'])->name('admin.processChangePassword');
        //Create Temp-Image
        Route::post('/upload-temp-image', [TempImagesController::class, 'create'])->name('temp-images.create');

        Route::get('/getSlug', function (Request $request) {
            $slug = '';
            if (!empty($request->title)) {
                $slug = Str::slug($request->title);
            }
            return response()->json([
                'status' => true,
                'slug' => $slug,

            ]);
        })->name('getSlug');
    });
});
