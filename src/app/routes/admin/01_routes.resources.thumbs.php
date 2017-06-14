<?php

Route::group(['prefix' => config('hc.admin_url'), 'middleware' => ['web', 'auth']], function ()
{
    Route::get('resources/thumbs', ['as' => 'admin.resources.thumbs', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_list'], 'uses' => 'resources\\HCThumbsController@adminIndex']);

    Route::group(['prefix' => 'api/resources/thumbs'], function ()
    {
        Route::get('/', ['as' => 'admin.api.resources.thumbs', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_list'], 'uses' => 'resources\\HCThumbsController@apiIndexPaginate']);
        Route::post('/', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_create'], 'uses' => 'resources\\HCThumbsController@apiStore']);
        Route::delete('/', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_delete'], 'uses' => 'resources\\HCThumbsController@apiDestroy']);

        Route::get('list', ['as' => 'admin.api.resources.thumbs.list', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_list'], 'uses' => 'resources\\HCThumbsController@apiIndex']);
        Route::post('restore', ['as' => 'admin.api.resources.thumbs.restore', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_update'], 'uses' => 'resources\\HCThumbsController@apiRestore']);
        Route::post('merge', ['as' => 'admin.api.resources.thumbs.merge', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_create', 'acl:interactivesolutions_honeycomb_resources_resources_thumbs_update'], 'uses' => 'resources\\HCThumbsController@apiMerge']);
        Route::delete('force', ['as' => 'admin.api.resources.thumbs.force.multi', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_force_delete'], 'uses' => 'resources\\HCThumbsController@apiForceDelete']);

        Route::group(['prefix' => '{id}'], function ()
        {
            Route::get('/', ['as' => 'admin.api.resources.thumbs.single', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_list'], 'uses' => 'resources\\HCThumbsController@apiShow']);
            Route::put('/', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_update'], 'uses' => 'resources\\HCThumbsController@apiUpdate']);
            Route::delete('/', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_delete'], 'uses' => 'resources\\HCThumbsController@apiDestroy']);

            Route::post('strict', ['as' => 'admin.api.resources.thumbs.update.strict', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_update'], 'uses' => 'resources\\HCThumbsController@apiUpdateStrict']);
            Route::post('duplicate', ['as' => 'admin.api.resources.thumbs.duplicate', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_create'], 'uses' => 'resources\\HCThumbsController@apiDuplicate']);
            Route::delete('force', ['as' => 'admin.api.resources.thumbs.force', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_thumbs_force_delete'], 'uses' => 'resources\\HCThumbsController@apiForceDelete']);
        });
    });
});

