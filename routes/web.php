<?php
$namespacePrefix = '\\ColorIjo\\MediaManager\\Http\\';
Route::group(['prefix' => 'media'], function () use ($namespacePrefix) {
    Route::post('upload', $namespacePrefix.'Controller@store');
    Route::get('all', $namespacePrefix.'Controller@list');
    Route::get('{unique}', $namespacePrefix.'Controller@show');
    Route::put('{unique}', $namespacePrefix.'Controller@update');
    Route::delete('delete', $namespacePrefix.'Controller@destroy');
    Route::get('d/file/{unique}/{size}', $namespacePrefix.'Controller@getImageLink');
    Route::post('d/file/download', $namespacePrefix.'Controller@getLinkDownload');
    // /media/file/0/d/'+unique
});
Route::get('download/{hash}/{unique}', $namespacePrefix.'Controller@download');
// Route::get('view/{hash}/{unique}/{size}', $namespacePrefix.'Controller@getImage');
