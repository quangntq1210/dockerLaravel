<?php
use App\Jobs\NewJob;
use Illuminate\Support\Facades\DB;
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

Route::get('/', function () {
    return view('welcome');

});
Route::get('/test-db', function () {
    return \DB::table('users')->get();
});

Route::get('/send-mail', function () {
    NewJob::dispatch('congthang1280@gmail.com', 'Vu Cong Thang');
    return 'Job đã được đưa vào queue! Kiểm tra queue worker để xem email được gửi.';
});