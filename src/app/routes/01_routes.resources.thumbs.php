<?php

Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth']], function ()
{
    Route::get('resources/thumbs', ['as' => 'admin.resources.thumbs', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_list'], 'uses' => 'resources\\HCThumbsController@adminView']);

    Route::group(['prefix' => 'api'], function ()
    {
        Route::get('resources/thumbs', ['as' => 'admin.api.resources.thumbs', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_list'], 'uses' => 'resources\\HCThumbsController@listData']);
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
