<?php

Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth']], function ()
{
    Route::get('resources', ['as' => 'admin.resources', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@adminView']);

    Route::group(['prefix' => 'api'], function ()
    {
        Route::get('resources', ['as' => 'admin.api.resources', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@listData']);
        Route::get('resources/search', ['as' => 'admin.api.resources.search', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@search']);
        Route::get('resources/{id}', ['as' => 'admin.api.resources.single', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@getSingleRecord']);
        Route::post('resources/{id}/duplicate', ['as' => 'admin.api.resources.duplicate', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@duplicate']);
        Route::post('resources/restore', ['as' => 'admin.api.resources.restore', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@restore']);
        Route::post('resources/merge', ['as' => 'admin.api.resources.merge', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@merge']);
        Route::post('resources', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_create'], 'uses' => 'HCResourcesController@create']);
        Route::put('resources/{id}', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@update']);
        Route::delete('resources/{id}', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_delete'], 'uses' => 'HCResourcesController@delete']);
        Route::delete('resources', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_delete'], 'uses' => 'HCResourcesController@delete']);
        Route::delete('resources/{id}/force', ['as' => 'admin.api.resources.force', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_force_delete'], 'uses' => 'HCResourcesController@forceDelete']);
        Route::delete('resources/force', ['as' => 'admin.api.resources.force.multi', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_force_delete'], 'uses' => 'HCResourcesController@forceDelete']);
    });
});
