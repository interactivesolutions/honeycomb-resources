<?php

Route::group(['prefix' => 'api', 'middleware' => ['auth-apps']], function() {
    Route::group(['prefix' => 'v1/resources/thumbs'], function() {
        Route::get('/', [
            'as' => 'api.v1.resources.thumbs',
            'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_thumbs_list'],
            'uses' => 'resources\\HCThumbsController@apiIndexPaginate',
        ]);
        Route::post('/', [
            'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_thumbs_create'],
            'uses' => 'resources\\HCThumbsController@apiStore',
        ]);
        Route::delete('/', [
            'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_thumbs_delete'],
            'uses' => 'resources\\HCThumbsController@apiDestroy',
        ]);

        Route::group(['prefix' => 'list'], function() {
            Route::get('/', [
                'as' => 'api.v1.resources.thumbs.list',
                'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_thumbs_list'],
                'uses' => 'resources\\HCThumbsController@apiIndex',
            ]);
            Route::get('{timestamp}', [
                'as' => 'api.v1.resources.thumbs.list.update',
                'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_thumbs_list'],
                'uses' => 'resources\\HCThumbsController@apiIndexSync',
            ]);
        });

        Route::post('restore', [
            'as' => 'api.v1.resources.thumbs.restore',
            'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_thumbs_update'],
            'uses' => 'resources\\HCThumbsController@apiRestore',
        ]);
        Route::post('merge', [
            'as' => 'api.v1.resources.thumbs.merge',
            'middleware' => [
                'acl-apps:interactivesolutions_honeycomb_resources_resources_thumbs_create',
                'acl-apps:interactivesolutions_honeycomb_resources_resources_thumbs_update',
            ],
            'uses' => 'resources\\HCThumbsController@apiMerge',
        ]);
        Route::delete('force', [
            'as' => 'api.v1.resources.thumbs.force.multi',
            'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_thumbs_force_delete'],
            'uses' => 'resources\\HCThumbsController@apiForceDelete',
        ]);

        Route::group(['prefix' => '{id}'], function() {
            Route::get('/', [
                'as' => 'api.v1.resources.thumbs.single',
                'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_thumbs_list'],
                'uses' => 'resources\\HCThumbsController@apiShow',
            ]);
            Route::put('/', [
                'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_thumbs_update'],
                'uses' => 'resources\\HCThumbsController@apiUpdate',
            ]);
            Route::delete('/', [
                'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_thumbs_delete'],
                'uses' => 'resources\\HCThumbsController@apiDestroy',
            ]);

            Route::post('strict', [
                'as' => 'api.v1.resources.thumbs.update.strict',
                'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_thumbs_update'],
                'uses' => 'resources\\HCThumbsController@apiUpdateStrict',
            ]);
            Route::post('duplicate', [
                'as' => 'api.v1.resources.thumbs.duplicate',
                'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_thumbs_create'],
                'uses' => 'resources\\HCThumbsController@apiDuplicate',
            ]);
            Route::delete('force', [
                'as' => 'api.v1.resources.thumbs.force',
                'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_thumbs_force_delete'],
                'uses' => 'resources\\HCThumbsController@apiForceDelete',
            ]);
        });
    });
});


