<?php

Route::group(['prefix' => 'api', 'middleware' => ['web', 'auth-apps']], function ()
{
    Route::get('resources/thumbs', ['as' => 'api.v1.resources.thumbs', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_list'], 'uses' => 'resources\\HCThumbsController@adminView']);

    Route::group(['prefix' => 'v1/resources/thumbs'], function ()
    {
        Route::get('/', ['as' => 'api.v1.api.resources.thumbs', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_list'], 'uses' => 'resources\\HCThumbsController@listPage']);
        Route::get('list/{timestamp}', ['as' => 'api.v1.api.resources.thumbs.list.update', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_list'], 'uses' => 'resources\\HCThumbsController@listUpdate']);
        Route::get('list', ['as' => 'api.v1.api.resources.thumbs.list', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_list'], 'uses' => 'resources\\HCThumbsController@list']);
        Route::get('search', ['as' => 'api.v1.api.resources.thumbs.search', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_list'], 'uses' => 'resources\\HCThumbsController@listSearch']);
        Route::get('{id}', ['as' => 'api.v1.api.resources.thumbs.single', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_list'], 'uses' => 'resources\\HCThumbsController@getSingleRecord']);

        Route::post('{id}/duplicate', ['as' => 'api.v1.api.resources.thumbs.duplicate', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_update'], 'uses' => 'resources\\HCThumbsController@duplicate']);
        Route::post('restore', ['as' => 'api.v1.api.resources.thumbs.restore', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_update'], 'uses' => 'resources\\HCThumbsController@restore']);
        Route::post('merge', ['as' => 'api.v1.api.resources.thumbs.merge', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_update'], 'uses' => 'resources\\HCThumbsController@merge']);
        Route::post('/', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_create'], 'uses' => 'resources\\HCThumbsController@create']);

        Route::put('{id}', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_update'], 'uses' => 'resources\\HCThumbsController@update']);

        Route::delete('{id}', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_delete'], 'uses' => 'resources\\HCThumbsController@delete']);
        Route::delete('/', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_delete'], 'uses' => 'resources\\HCThumbsController@delete']);
        Route::delete('{id}/force', ['as' => 'api.v1.api.resources.thumbs.force', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_force_delete'], 'uses' => 'resources\\HCThumbsController@forceDelete']);
        Route::delete('force', ['as' => 'api.v1.api.resources.thumbs.force.multi', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_force_delete'], 'uses' => 'resources\\HCThumbsController@forceDelete']);
    });
});
