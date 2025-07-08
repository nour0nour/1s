<?php
use \Illuminate\Support\Facades\Route;

Route::group(['prefix'=>config('visa.visa_route_prefix')],function(){
    Route::get('/','VisaController@index')->name('visa.search'); // Search
    Route::get('/{slug}','VisaController@detail')->name('visa.detail');// Detail
});

Route::group(['prefix'=>'user/'.config('visa.visa_route_prefix'),'middleware' => ['auth','verified']],function(){
    Route::get('/','ManageVisaController@managevisa')->name('visa.vendor.index');
    Route::get('/create','ManageVisaController@createvisa')->name('visa.vendor.create');
    Route::get('/edit/{id}','ManageVisaController@editvisa')->name('visa.vendor.edit');
    Route::get('/del/{id}','ManageVisaController@deletevisa')->name('visa.vendor.delete');
    Route::post('/store/{id}','ManageVisaController@store')->name('visa.vendor.store');
    Route::get('bulkEdit/{id}','ManageVisaController@bulkEditvisa')->name("visa.vendor.bulk_edit");
    Route::get('/booking-report/bulkEdit/{id}','ManageVisaController@bookingReportBulkEdit')->name("visa.vendor.booking_report.bulk_edit");
    Route::get('/recovery','ManageVisaController@recovery')->name('visa.vendor.recovery');
    Route::get('/restore/{id}','ManageVisaController@restore')->name('visa.vendor.restore');
});

Route::group(['prefix'=>'user/'.config('visa.visa_route_prefix')],function(){
    Route::group(['prefix'=>'availability'],function(){
        Route::get('/','AvailabilityController@index')->name('visa.vendor.availability.index');
        Route::get('/loadDates','AvailabilityController@loadDates')->name('visa.vendor.availability.loadDates');
        Route::post('/store','AvailabilityController@store')->name('visa.vendor.availability.store');
    });
});
