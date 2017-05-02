<?php

Route::group (['prefix' => 'api', 'middleware' => ['auth-apps']], function () {
    Route::get ('resources', ['as' => 'api.v1.resources', 'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@adminView']);

    Route::group (['prefix' => 'v1/resources'], function () {
        Route::get ('/', ['as' => 'api.v1.api.resources', 'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@listPage']);
        Route::get ('list', ['as' => 'api.v1.api.resources.list', 'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@list']);
        Route::get ('list/{timestamp}', ['as' => 'api.v1.api.resources.list.update', 'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@listUpdate']);
        Route::get ('search', ['as' => 'api.v1.api.resources.search', 'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@listSearch']);
        Route::get ('{id}', ['as' => 'api.v1.api.resources.single', 'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@getSingleRecord']);

        Route::post ('{id}/duplicate', ['as' => 'api.v1.api.resources.duplicate', 'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@duplicate']);
        Route::post ('restore', ['as' => 'api.v1.api.resources.restore', 'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@restore']);
        Route::post ('merge', ['as' => 'api.v1.api.resources.merge', 'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@merge']);
        Route::post ('/', ['middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_create'], 'uses' => 'HCResourcesController@create']);

        Route::put ('{id}', ['middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@update']);

        Route::delete ('{id}', ['middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_delete'], 'uses' => 'HCResourcesController@delete']);
        Route::delete ('resources', ['middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_delete'], 'uses' => 'HCResourcesController@delete']);
        Route::delete ('{id}/force', ['as' => 'api.v1.api.resources.force', 'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_force_delete'], 'uses' => 'HCResourcesController@forceDelete']);
        Route::delete ('force', ['as' => 'api.v1.api.resources.force.multi', 'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_force_delete'], 'uses' => 'HCResourcesController@forceDelete']);
    });
});

Route::get ('resources/o/{resourceName}', ['as' => 'resource.o.get', 'uses' => 'HCResourcesController@showResourceByName',]);
Route::get ('resources/s/{resourceSafeName}', ['as' => 'resource.s.get', 'uses' => 'HCResourcesController@showResourceBySafeName',]);
Route::get ('resources/{resourceId}/{width?}/{height?}/{fit?}', ['as' => 'resource.get', 'uses' => 'HCResourcesController@showResource',]);

