<?php

Route::group (['prefix' => 'api', 'middleware' => ['auth-apps']], function ()
{
    Route::group (['prefix' => 'v1/resources'], function ()
    {
        Route::get ('/', ['as' => 'api.v1.resources', 'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@apiIndexPaginate']);
        Route::post ('/', ['middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_create'], 'uses' => 'HCResourcesController@apiStore']);
        Route::delete ('/', ['middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_delete'], 'uses' => 'HCResourcesController@apiDestroy']);

        Route::group(['prefix' => 'list'], function ()
        {
            Route::get ('/', ['as' => 'api.v1.resources.list', 'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@apiIndex']);
            Route::get ('{timestamp}', ['as' => 'api.v1.resources.list.update', 'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@apiIndexSync']);
        });

        Route::post ('restore', ['as' => 'api.v1.resources.restore', 'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@apiRestore']);
        Route::post ('merge', ['as' => 'api.v1.resources.merge', 'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@apiMerge']);
        Route::delete ('force', ['as' => 'api.v1.resources.force.multi', 'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_force_delete'], 'uses' => 'HCResourcesController@apiForceDelete']);

        Route::group(['prefix' => '{id}'], function ()
        {
            Route::get ('/', ['as' => 'api.v1.resources.single', 'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_list'], 'uses' => 'HCResourcesController@apiShow']);
            Route::put ('/', ['middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@apiUpdate']);
            Route::delete ('/', ['middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_delete'], 'uses' => 'HCResourcesController@apiDestroy']);

            Route::post ('strict', ['as' => 'api.v1.resources.update.strict', 'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_update'], 'uses' => 'HCResourcesController@apiUpdateStrict']);
            Route::post ('duplicate', ['as' => 'api.v1.resources.duplicate', 'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_create'], 'uses' => 'HCResourcesController@apiDuplicate']);
            Route::delete ('force', ['as' => 'api.v1.resources.force', 'middleware' => ['acl-apps:interactivesolutions_honeycomb_resources_resources_force_delete'], 'uses' => 'HCResourcesController@apiForceDelete']);
        });
    });
});
