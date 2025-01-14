<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserPasswordController;
use App\Http\Controllers\Api\ApiGlobalController;
use App\Http\Controllers\Api\CustomerCardController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DrawalController;
use App\Http\Controllers\Api\WithdrawalController;
use App\Http\Controllers\Api\BankLogController;
use App\Http\Controllers\Api\BillReturnController;
use App\Http\Controllers\Api\CommandCheckController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\PosController;
use App\Http\Controllers\Api\StaffCustomerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('login', [AuthController::class, 'login']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
Route::middleware(['auth:sanctum','is_admin:admin,manager_vip_2'])->group(function () { 
    Route::post('/checkHistories', [CommandCheckController::class, 'checkHistories']);
    Route::post('/checkBankLogs', [CommandCheckController::class, 'checkBankLogs']);
    Route::post('/checkPosbacks', [CommandCheckController::class, 'checkPosbacks']);
    Route::post('/checkAllCommand', [CommandCheckController::class, 'checkAll']);
    
});
Route::get('branchs/lists', [BranchController::class, 'lists']);
Route::get('provinces', [ApiGlobalController::class, 'provinces'])->name('provinces');
Route::get('districts/{provinceId}', [ApiGlobalController::class, 'districts'])->name('districts');
Route::get('wards/{districtId}', [ApiGlobalController::class, 'wards'])->name('wards');
Route::get('address', [ApiGlobalController::class, 'address'])->name('address');
Route::get('/fund-categories', [ApiGlobalController::class, 'fundCategories'])->name('fundCategories');
Route::get('list-bank', [ApiGlobalController::class, 'listBank']);

Route::middleware(['auth:sanctum','is_admin:admin,mod,manager,manager_vip,manager_vip_2,staff,partner'])->group(function () {
     // customer
     Route::get('/customers/search', [CustomerController::class, 'search']);
     Route::get('/customers', [CustomerController::class, 'index']);
     Route::get('/customers/{customer}', [CustomerController::class, 'show']);
     Route::post('/customers', [CustomerController::class, 'store']);
    Route::get('/transaction-partner', [PartnerController::class, 'partner'])->name('partner'); 

});

Route::middleware(['auth:sanctum','is_admin:admin,manager,manager_vip'])->group(function () {
    Route::post('/activitiPosback', [CommandCheckController::class, 'activitiPosback']);
    Route::post('/bill-return', [BillReturnController::class, 'billReturn']);
});

Route::middleware(['auth:sanctum','is_admin:admin,mod,manager,manager_vip,manager_vip_2,staff'])->group(function () {
    Route::group(['middleware', ['json.force']], function() {
        // global
        
        Route::get('list-pos', [ApiGlobalController::class, 'listPos']);
        Route::get('/pos', [PosController::class, 'index']);
        Route::get('/pos_lists', [PosController::class, 'pos_lists']);
        Route::get('/pos/{pos}', [PosController::class, 'show']);

        Route::apiResource('customers', CustomerController::class)->except(['index','store','show']);
        Route::patch('/users/password/change/{user}', [UserPasswordController::class, 'update']);

        Route::post('/users/{user}', [UserPasswordController::class, 'update']);
        Route::get('/users/search', [UserController::class, 'search']);
        Route::apiResource('users', UserController::class);
        Route::apiResource('branchs', BranchController::class);

        // 
        Route::get('/customer-cards/search', [CustomerCardController::class, 'search']);
        Route::apiResource('customer-cards', CustomerCardController::class);

        // drawal
        Route::post('/drawals/reDone/{drawal}', [DrawalController::class, 'reDone'])->name('reDone');
        Route::post('/drawals/receive/{drawal}', [DrawalController::class, 'receive'])->name('receive');

        Route::post('/drawals/verify/{drawal}', [DrawalController::class, 'verify'])->name('verify');
        Route::apiResource('drawals', DrawalController::class);
        
        // withdrawal
        Route::post('/withdrawals/reDone/{withdrawal}', [WithdrawalController::class, 'reDone'])->name('reDone');
        Route::post('/withdrawals/verify/{withdrawal}', [WithdrawalController::class, 'verify'])->name('verify');
        Route::post('/withdrawals/receive/{withdrawal}', [WithdrawalController::class, 'receive'])->name('receive');


        Route::apiResource('withdrawals', WithdrawalController::class);
        // History
        Route::apiResource('/history', BankLogController::class);
        // ok

        Route::get('/transactions', [DashboardController::class, 'transaction'])->name('transactions');
        Route::get('/expenses', [DashboardController::class, 'expense'])->name('expenses');
        Route::get('/globals', [DashboardController::class, 'indexGlobal'])->name('indexGlobal');
        Route::get('/global-details', [DashboardController::class, 'indexGlobalDetail'])->name('indexGlobalDetail');
        Route::get('/dasboard-pos', [DashboardController::class, 'dashboardPos'])->name('dashboardPos');


    
    });
    
});

Route::middleware(['auth:sanctum','is_admin:tele_sales'])->group(function () {
    // customer
    Route::get('/tele-sales-customers/search', [StaffCustomerController::class, 'search']);
    Route::get('/tele-sales-customers', [StaffCustomerController::class, 'index']);
    Route::get('/tele-sales-customers/{customer}', [StaffCustomerController::class, 'show']);
    Route::post('/tele-sales-customers', [StaffCustomerController::class, 'store']);
    Route::put('/tele-sales-customers/{customer}', [StaffCustomerController::class, 'update']);

    Route::post('/schedule-customer', [StaffCustomerController::class, 'schedule']);

});

