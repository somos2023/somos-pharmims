<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatAppController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/{any?}', function () {
    return view('home');
})->where('any', '^(?!api\/)[\/\w\.-]*');

// Route::get('/', function () {
//     return view('layouts.home');
// });
// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/update-last-activity', [ChatAppController::class, 'updateData'])->middleware('auth');
// });
