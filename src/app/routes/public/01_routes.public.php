<?php

Route::group (['prefix' => 'resources'], function (){
    Route::get ('stream/{id}', ['as' => 'resource.stream', 'uses' => 'HCResourcesController@streamResource',]);
    Route::get ('o/{resourceName}', ['as' => 'resource.o.get', 'uses' => 'HCResourcesController@showResourceByName',]);
    Route::get ('s/{resourceSafeName}', ['as' => 'resource.s.get', 'uses' => 'HCResourcesController@showResourceBySafeName',]);
    Route::get ('{resourceId}/{width?}/{height?}/{fit?}', ['as' => 'resource.get', 'uses' => 'HCResourcesController@showResource',]);
});