<?php

Route::group (['prefix' => env('HC_ADMIN_URL'), 'middleware' => ['web', 'auth']], function () {
    Route::get ('resources', ['as' => 'admin.resources', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@adminView']);

    Route::group (['prefix' => 'api/resources'], function () {
        Route::get ('/', ['as' => 'admin.api.resources', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@listPage']);
        Route::get ('list', ['as' => 'admin.api.resources.list', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@list']);
        Route::get ('list/{timestamp}', ['as' => 'admin.api.resources.list.update', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@listUpdate']);
        Route::get ('search', ['as' => 'admin.api.resources.search', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@listSearch']);
        Route::get ('{id}', ['as' => 'admin.api.resources.single', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@getSingleRecord']);

        Route::post ('{id}/duplicate', ['as' => 'admin.api.resources.duplicate', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@duplicate']);
        Route::post ('restore', ['as' => 'admin.api.resources.restore', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@restore']);
        Route::post ('merge', ['as' => 'admin.api.resources.merge', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@merge']);
        Route::post ('/', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_create'], 'uses' => 'HCResourcesController@create']);

        Route::put ('{id}', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@update']);

        Route::delete ('{id}', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_delete'], 'uses' => 'HCResourcesController@delete']);
        Route::delete ('resources', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_delete'], 'uses' => 'HCResourcesController@delete']);
        Route::delete ('{id}/force', ['as' => 'admin.api.resources.force', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_force_delete'], 'uses' => 'HCResourcesController@forceDelete']);
        Route::delete ('force', ['as' => 'admin.api.resources.force.multi', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_force_delete'], 'uses' => 'HCResourcesController@forceDelete']);
    });
});

Route::get ('resources/o/{resourceName}', ['as' => 'resource.o.get', 'uses' => 'HCResourcesController@showResourceByName',]);
Route::get ('resources/s/{resourceSafeName}', ['as' => 'resource.s.get', 'uses' => 'HCResourcesController@showResourceBySafeName',]);
Route::get ('resources/{resourceId}/{width?}/{height?}/{fit?}', ['as' => 'resource.get', 'uses' => 'HCResourcesController@showResource',]);

