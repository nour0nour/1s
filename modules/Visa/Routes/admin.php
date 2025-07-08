<?php
use \Illuminate\Support\Facades\Route;
Route::get('/','VisaController@index')->name('visa.admin.index');
Route::get('/create','VisaController@create')->name('visa.admin.create');
Route::get('/edit/{id}','VisaController@edit')->name('visa.admin.edit');
Route::post('/store/{id}','VisaController@store')->name('visa.admin.store');
Route::post('/bulkEdit','VisaController@bulkEdit')->name('visa.admin.bulkEdit');
Route::get('/recovery','VisaController@recovery')->name('visa.admin.recovery');
Route::get('/getForSelect2','VisaController@getForSelect2')->name('visa.admin.getForSelect2');

Route::group(['prefix'=>'attribute'],function (){
    Route::get('/','AttributeController@index')->name('visa.admin.attribute.index');
    Route::get('/edit/{id}','AttributeController@edit')->name('visa.admin.attribute.edit');
    Route::post('/store/{id}','AttributeController@store')->name('visa.admin.attribute.store');
    Route::post('/editAttrBulk','AttributeController@editAttrBulk')->name('visa.admin.attribute.editAttrBulk');

    Route::get('/terms/{id}','AttributeController@terms')->name('visa.admin.attribute.term.index');
    Route::get('/term_edit/{id}','AttributeController@term_edit')->name('visa.admin.attribute.term.edit');
    Route::post('/term_store','AttributeController@term_store')->name('visa.admin.attribute.term.store');
    Route::post('/editTermBulk','AttributeController@editTermBulk')->name('visa.admin.attribute.term.editTermBulk');

    Route::get('/getForSelect2','AttributeController@getForSelect2')->name('visa.admin.attribute.term.getForSelect2');
});

Route::group(['prefix'=>'availability'],function(){
    Route::get('/','AvailabilityController@index')->name('visa.admin.availability.index');
    Route::get('/loadDates','AvailabilityController@loadDates')->name('visa.admin.availability.loadDates');
    Route::post('/store','AvailabilityController@store')->name('visa.admin.availability.store');
});
