<?php

//interactivesolutions/honeycomb-resources/src/app/routes/01_routes.resources.thumbs.php


Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth']], function ()
{
    Route::get('resources/thumbs', ['as' => 'admin.resources.thumbs', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_list'], 'uses' => 'resources\\HCThumbsController@adminView']);

    Route::group(['prefix' => 'api'], function ()
    {
        Route::get('resources/thumbs', ['as' => 'admin.api.resources.thumbs', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_list'], 'uses' => 'resources\\HCThumbsController@pageData']);
        Route::get('resources/thumbs/list', ['as' => 'admin.api.resources.thumbs.list', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_list'], 'uses' => 'resources\\HCThumbsController@list']);
        Route::get('resources/thumbs/search', ['as' => 'admin.api.resources.thumbs.search', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_list'], 'uses' => 'resources\\HCThumbsController@search']);
        Route::get('resources/thumbs/{id}', ['as' => 'admin.api.resources.thumbs.single', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_list'], 'uses' => 'resources\\HCThumbsController@getSingleRecord']);
        Route::post('resources/thumbs/{id}/duplicate', ['as' => 'admin.api.resources.thumbs.duplicate', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_update'], 'uses' => 'resources\\HCThumbsController@duplicate']);
        Route::post('resources/thumbs/restore', ['as' => 'admin.api.resources.thumbs.restore', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_update'], 'uses' => 'resources\\HCThumbsController@restore']);
        Route::post('resources/thumbs/merge', ['as' => 'admin.api.resources.thumbs.merge', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_update'], 'uses' => 'resources\\HCThumbsController@merge']);
        Route::post('resources/thumbs', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_create'], 'uses' => 'resources\\HCThumbsController@create']);
        Route::put('resources/thumbs/{id}', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_update'], 'uses' => 'resources\\HCThumbsController@update']);
        Route::delete('resources/thumbs/{id}', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_delete'], 'uses' => 'resources\\HCThumbsController@delete']);
        Route::delete('resources/thumbs', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_delete'], 'uses' => 'resources\\HCThumbsController@delete']);
        Route::delete('resources/thumbs/{id}/force', ['as' => 'admin.api.resources.thumbs.force', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_force_delete'], 'uses' => 'resources\\HCThumbsController@forceDelete']);
        Route::delete('resources/thumbs/force', ['as' => 'admin.api.resources.thumbs.force.multi', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_force_delete'], 'uses' => 'resources\\HCThumbsController@forceDelete']);
    });
});


//interactivesolutions/honeycomb-resources/src/app/routes/02_routes.resources.php


Route::group (['prefix' => 'admin', 'middleware' => ['web', 'auth']], function () {
    Route::get ('resources', ['as' => 'admin.resources', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@adminView']);

    Route::group (['prefix' => 'api'], function () {
        Route::get ('resources', ['as' => 'admin.api.resources', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@pageData']);
        Route::get ('resources/list', ['as' => 'admin.api.resources.list', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@list']);
        Route::get ('resources/search', ['as' => 'admin.api.resources.search', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@search']);
        Route::get ('resources/{id}', ['as' => 'admin.api.resources.single', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@getSingleRecord']);
        Route::post ('resources/{id}/duplicate', ['as' => 'admin.api.resources.duplicate', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@duplicate']);
        Route::post ('resources/restore', ['as' => 'admin.api.resources.restore', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@restore']);
        Route::post ('resources/merge', ['as' => 'admin.api.resources.merge', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@merge']);
        Route::post ('resources', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_create'], 'uses' => 'HCResourcesController@create']);
        Route::put ('resources/{id}', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@update']);
        Route::delete ('resources/{id}', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_delete'], 'uses' => 'HCResourcesController@delete']);
        Route::delete ('resources', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_delete'], 'uses' => 'HCResourcesController@delete']);
        Route::delete ('resources/{id}/force', ['as' => 'admin.api.resources.force', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_force_delete'], 'uses' => 'HCResourcesController@forceDelete']);
        Route::delete ('resources/force', ['as' => 'admin.api.resources.force.multi', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_force_delete'], 'uses' => 'HCResourcesController@forceDelete']);
    });
});

Route::get ('resources/o/{resourceName}', ['as' => 'resource.o.get', 'uses' => 'HCResourcesController@showResourceByName',]);
Route::get ('resources/s/{resourceSafeName}', ['as' => 'resource.s.get', 'uses' => 'HCResourcesController@showResourceBySafeName',]);
Route::get ('resources/{resourceId}/{width?}/{height?}/{fit?}', ['as' => 'resource.get', 'uses' => 'HCResourcesController@showResource',]);


