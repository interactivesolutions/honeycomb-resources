<?php

Route::group (['prefix' => env('HC_ADMIN_URL'), 'middleware' => ['web', 'auth']], function ()
{
    Route::get ('resources', ['as' => 'admin.resources', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@adminIndex']);

    Route::group (['prefix' => 'api/resources'], function ()
    {
        Route::get ('/', ['as' => 'admin.api.resources', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@apiIndexPaginate']);
        Route::post ('/', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_create'], 'uses' => 'HCResourcesController@apiStore']);
        Route::delete ('/', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_delete'], 'uses' => 'HCResourcesController@apiDestroy']);

        Route::post ('restore', ['as' => 'admin.api.resources.restore', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@apiRestore']);
        Route::post ('merge', ['as' => 'admin.api.resources.merge', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_create', 'acl:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@apiMerge']);
        Route::delete ('force', ['as' => 'admin.api.resources.force.multi', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_force_delete'], 'uses' => 'HCResourcesController@apiForceDelete']);

        Route::group(['prefix' => '{id}'], function ()
        {
            Route::get ('/', ['as' => 'admin.api.resources.single', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@apiShow']);
            Route::put ('/', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@apiUpdate']);
            Route::delete ('/', ['middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_delete'], 'uses' => 'HCResourcesController@apiDestroy']);

            Route::get('regenerate', ['as' => 'admin.api.resources.regenerate', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@regenerateVideoPreview']);
            Route::post ('strict', ['as' => 'admin.api.resources.update.strict', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@apiUpdateStrict']);
            Route::post ('duplicate', ['as' => 'admin.api.resources.duplicate', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_create'], 'uses' => 'HCResourcesController@apiDuplicate']);
            Route::delete ('force', ['as' => 'admin.api.resources.force', 'middleware' => ['acl:interactivesolutions_honeycomb_resources_resources_force_delete'], 'uses' => 'HCResourcesController@apiForceDelete']);
        });

    });
});
