<?php

use Maatwebsite\Excel\Row;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NUR\NUR2GController;
use App\Http\Controllers\NUR\NUR3GController;
use App\Http\Controllers\NUR\NUR4GController;
use App\Http\Controllers\User\LoginController;
use App\Http\Controllers\NUR\ShowNURController;
use App\Http\Controllers\User\LogoutController;
use App\Http\Controllers\NUR\NurIndexController;
use App\Http\Controllers\Sites\NodalsController;
use App\Http\Controllers\User\RegisterController;
use App\Http\Controllers\Sites\CascadesController;
use App\Http\Controllers\NUR\DownloadNURController;

use App\Http\Controllers\EnergySheet\EnergyController;
use App\Http\Controllers\User\ResetPasswordController;
use App\Http\Controllers\Sites\SuperAdminSitesController;
use App\Http\Controllers\Sites\NormalUsersSitesController;
use App\Http\Controllers\Modifications\ModificationsController;
use App\Http\Controllers\EnergySheet\EnergyStatesticsController;
use App\Http\Controllers\EnergySheet\EnergySiteStatesticsController;
use App\Http\Controllers\EnergySheet\EnergyZoneStatesticsController;

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

Route::middleware('auth:sanctum')->prefix("user")->group(function(){
    Route::post('/logout',[LogoutController::class,"logout"]);
    Route::get("refreshsession",[LoginController::class,"refresh_session"]);

});


Route::prefix("energysheet")->middleware(['auth:sanctum',"role:admin|super-admin"])->group(function(){
    Route::get('/index',[EnergyController::class,"index"] )->name("energysheet_index");
    Route::post('/index',[EnergyController::class,"store_alarms"] )->name("energysheet_store_alarms");

});
Route::prefix("energysheet")->middleware(['auth:sanctum'])->group(function(){

    Route::post("/alarms",[EnergyStatesticsController::class,"siteAlarms"]);
 
    Route::get('/statestics/{week}/{year}',[EnergyStatesticsController::class,"statestics"]);
    Route::post("/sitePowerAlarms",[EnergySiteStatesticsController::class,"sitePowerAlarms"]);
    Route::post("/siteHighTempAlarms",[EnergySiteStatesticsController::class,"siteHighTempAlarms"]);
    Route::post("/siteGenAlarms",[EnergySiteStatesticsController::class,"siteGenAlarms"]);
    Route::post("/downloadSitePowerAlarms",[EnergySiteStatesticsController::class,"downloadSitePowerAlarms"]);
    Route::post("/downloadSiteHighTempAlarms",[EnergySiteStatesticsController::class,"downloadSiteHighTempAlarms"]);
    Route::post("/downloadSiteGenAlarms",[EnergySiteStatesticsController::class,"downloadSiteGenAlarms"]);
    Route::post("/downloadZoneHTAlarms",[EnergyZoneStatesticsController::class,"downloadZoneHTAlarms"]);

});

Route::prefix('modifications')->middleware(['auth:sanctum',"role:admin|super-admin"])->group(function(){
    Route::get("/analysis",[ModificationsController::class,"analysis"])->name("analysis");
    Route::get("/index/{columnName}/{columnValue}",[ModificationsController::class,"index"])->name("index");
    Route::post("/update",[ModificationsController::class,"modificationUpdate"])->name("modification_update");
    Route::get("/siteModifications/{site_code}",[ModificationsController::class,"siteModifications"])->name("siteModifications");
    Route::get("/details/{id}",[ModificationsController::class,"modificationDetails"])->name("details");
    Route::post("/new",[ModificationsController::class,"newModification"])->name("new_modification");
    Route::post("/delete",[ModificationsController::class,"deleteModification"])->name("delete_modification");
    Route::post("/download",[ModificationsController::class,"download"])->name("download_modification");
});


Route::prefix('sites')->middleware(['auth:sanctum',"role:super-admin"])->group(function(){
    // Route::get('/newsitesinsert',[SitesController::class,"index"])->name("sites");
    Route::post('/create',[SuperAdminSitesController::class,"siteCreate"])->name("create_site");
    Route::post('/store',[SuperAdminSitesController::class,"store"])->name("store_sites");
    Route::get('/downloadAll',[SuperAdminSitesController::class,"export_all"])->name("export_all");
    Route::get('/cascades',[CascadesController::class,"exportAllCascades"])->name("all_cascades");
    Route::post('/cascades',[CascadesController::class,"importCascades"])->name("import_cascades");
    Route::post('/nodals',[NodalsController::class,"importNodals"])->name("import_nodals");
    Route::post('/updateCascades',[CascadesController::class,"updateCascades"])->name("updateCascades");
    Route::post('/update',[SuperAdminSitesController::class,"siteUpdate"])->name("siteUpdate");
    
});
Route::prefix('sites')->middleware(['auth:sanctum',])->group(function(){
    Route::get('/search/{search}',[NormalUsersSitesController::class,"search"])->name("search_sites");
    Route::get('/details/{siteCode}',[NormalUsersSitesController::class,"siteDetails"])->name("site_details");
});
Route::prefix('Nur')->middleware(['auth:sanctum',"role:super-admin"])->group(function(){
    // Route::get('/newsitesinsert',[SitesController::class,"index"])->name("sites");
    Route::get('/index',[NurIndexController::class,"index"])->name("Nur_index");
    Route::post('/2G',[NUR2GController::class,"store"])->name("store_2G");
    Route::post('/3G',[NUR3GController::class,"store"])->name("store_3G");
    Route::post('/4G',[NUR4GController::class,"store"])->name("store_4G");
  
   

});
Route::prefix('Nur')->middleware(['auth:sanctum',"role:admin|super-admin"])->group(function(){
    Route::post('/siteNUR',[ShowNURController::class,"SiteNUR"])->name("siteNUR");
    Route::get('/show/{week}/{year}',[ShowNURController::class,"show_nur"])->name("show_nur");
    Route::post('/downloadNUR2G',[DownloadNURController::class,"NUR2G"])->name("site2GNUR");
    Route::post('/downloadNUR3G',[DownloadNURController::class,"NUR3G"])->name("site3GNUR");
    Route::post('/downloadNUR4G',[DownloadNURController::class,"NUR4G"])->name("site4GNUR");
    Route::get('/vip/week/{zone}/{week}/{year}',[ShowNURController::class,"vipSitesWeeklyNUR"]);
    Route::get('/nodal/week/{zone}/{week}/{year}',[ShowNURController::class,"nodalSitesWeeklyNUR"]);
    Route::get('/cairo/weekly/MWNUR/{week}/{year}',[ShowNURController::class,"cairoMWweeklyNUR"]);
    Route::get('/cairo/weekly/GenNUR/{week}/{year}',[ShowNURController::class,"cairoGenweeklyNUR"]);
    Route::get('/cairo/weekly/PowerNUR/{week}/{year}',[ShowNURController::class,"cairoPowerWeeklyNUR"]);
    Route::get('/cairo/yearly/NUR_C/{year}',[ShowNURController::class,"cairoYearlyNUR_C"]);
    Route::get('/cairo/yearly/GenNUR/{year}',[ShowNURController::class,"cairoGenYearlyNUR"]);
    Route::get('/cairo/yearly/TXNUR/{year}',[ShowNURController::class,"cairoMWYearlyNUR"]);

});

Route::prefix("user")->group(function(){
Route::post("/register",[RegisterController::class,"register"]);
Route::post("/login",[LoginController::class,"login"]);
Route::post("/sendToken",[ResetPasswordController::class,"sendToken"]);
Route::post("/validateToken",[ResetPasswordController::class,"validateToken"]);
Route::post("/resetPassword",[ResetPasswordController::class,"resetPassword"]);
});
