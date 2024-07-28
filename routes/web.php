<?php
use Carbon\Carbon;
use App\Models\Room;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Motel;

use App\Models\Booking;
use App\Models\GuestType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\MotelController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\GuestTypeController;
use App\Http\Controllers\RapairRoomController;
use App\Http\Controllers\AddMotelUserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReCheckInUploadContoller;
use App\Http\Controllers\RoomRepairController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\TractorUserController;
use App\Http\Controllers\UploadVoucherController;
use App\Models\TractorStatusUpdate;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Middlewares\PermissionMiddleware;

Route::get('/', function () {
    if(Auth::check()){
        return redirect()->route('user.home');
    }
    return Inertia::render('Auth/Login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::middleware(['role:Super Admin'])->group(function() {
        Route::resource('/motel',MotelController::class)->except('create','edit');
    });
    Route::resource('/guest_type',GuestTypeController::class);
    Route::resource('/room_type', RoomTypeController::class);
    Route::middleware(['role:Super Admin'])->group(function() {
        Route::post('/add_motel_user/{motel}', [AddMotelUserController::class,'store'])->name('store.motel.user');
        Route::resource('/users',UserController::class)->except('create','edit','show','update');
        Route::get('/users/{id}', [UserController::class,'show'])->name('users.show');
        Route::patch('/users/{user}/{motel}',[UserController::class,'update'])->name('users.update');
        Route::get('/all/vouchers',[VoucherController::class,'all'])->name('all.vouchers');
        Route::patch('/all/vouchers/{voucher}',[VoucherController::class,'update'])->name('all.voucher.update');
        Route::resource('/roles', RoleController::class);
    });
    Route::middleware(['role:Yard Admin'])->group(function() {
        Route::resource('/user/tractor', TractorUserController::class);
    });
    Route::get('/track/{id}',[TrackingController::class,'activity'])->name('track.activity');
    Route::resource('/track',TrackingController::class);
    Route::get('/room_voucher_details/{room}',[RoomController::class,'roomVoucherDetails'])->name('room.bookings.details');
    Route::resource('/guest', GuestController::class)->except('create','edit');
    Route::resource('/bookings', BookingController::class);
    Route::resource('/rates', RateController::class)->except('create','edit');
    Route::resource('/rooms', RoomController::class)->except('create','edit');
    Route::patch('/room_update_via_form/{room}', [RoomController::class,'updateViaForm'])->name('room.update.via.form');
    Route::post('/rooms/repair',[RoomController::class,'repair'])->name('room.repair');
    Route::resource('/vouchers', VoucherController::class)->except('edit');
    Route::get('/rooms_available',[RoomController::class,'availableRooms'])->name('rooms.available');

    Route::get('/home',[HomeController::class,'index'])->name('home.index');

    Route::get('/scan/voucher/{room}', [ScanController::class,'index'])->name('scan.index');

    Route::get('/upload/voucher/{room}',[UploadVoucherController::class,'index'])->name('upload.voucher.index');

    Route::post('/upload/voucher',[UploadVoucherController::class,'store'])->name('upload.voucher');
    Route::post('/guest/store/multi_client/{room}',[GuestController::class,'storeBulk'])->name('guest.store.multi_client');
    Route::get('/guest_type/show',[GuestTypeController::class,'showAll'])->name('guest.show.all');
    Route::get('/user/home', [UserController::class,'home'])->name('user.home');
    Route::post('/store/multi_client/voucher/{room}', [VoucherController::class,'storeMultiClient'])->name('store.multi.client');
    Route::get('/upload/voucher/home',[UploadController::class,'voucherIndex'])->name('upload.voucher.home');

    Route::patch('/re_check_in/upload/{room}',[ReCheckInUploadContoller::class,'update'])->name('re_check_in.upload');
    Route::get('/re_check_in/upload/{room}',[ReCheckInUploadContoller::class,'index'])->name('re_check_in.upload.index');
    
});
Route::get('/test',function() {
    $activities = TractorStatusUpdate::where('user_id',1)->where('tractor_id',1)->get();
    $grouped = $activities->groupBy(function ($date) {
        return \Carbon\Carbon::parse($date->created_at)->format('Y-m-d');
    });

    return $grouped->all();
});
require __DIR__.'/auth.php';
