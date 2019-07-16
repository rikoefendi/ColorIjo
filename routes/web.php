<?php
$namespacePrefix = '\\ColorIjo\\MediaManager\\Http\\';
Route::group(['prefix' => 'media'], function () use ($namespacePrefix) {
    Route::post('upload', $namespacePrefix.'Controller@store');
    Route::get('all', $namespacePrefix.'Controller@list');
    Route::get('{unique}', $namespacePrefix.'Controller@show');
    Route::put('{unique}', $namespacePrefix.'Controller@update');
    Route::delete('{unique}', $namespacePrefix.'Controller@destroy');
    Route::get('d/file/{unique}', $namespacePrefix.'Controller@getImage');
});
