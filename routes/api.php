<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('job-card','JobcardController');
Route::apiResource('machines','MachineController');
Route::get('import-machines','MachineController@importMachines');
Route::apiResource('users','UserController');
Route::apiResource('track-by','TrackByController');
Route::apiResource('service-providers','ServiceProviderController');
Route::get('import-suppliers','ServiceProviderController@importSuppliers');
Route::apiResource('service-provider-types','ServiceProviderTypeController');
Route::apiResource('service-types','ServiceTypeController');
Route::apiResource('fuel-balance','FuelBalanceController');
Route::apiResource('expense','ExpensesController');
Route::apiResource('fuel','FuelController');
Route::apiResource('fuel-types','FuelTypeController');
Route::apiResource('fuel-supplier','FuelSupplierController');
Route::apiResource('checklists','ChecklistsController');
Route::apiResource('expiry-types','ExpiryDatesController');
Route::apiResource('assign-checklist','AssignChecklistController');
Route::apiResource('external-services','ExternalServiceController');
Route::apiResource('parts','PartsController');
Route::apiResource('categories','CategoriesController');
Route::get('import-parts','PartsController@importParts');
Route::post('job-report','ReportsController@JobReport');
Route::post('fuel-report','ReportsController@FuelReport');

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function () {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

});
