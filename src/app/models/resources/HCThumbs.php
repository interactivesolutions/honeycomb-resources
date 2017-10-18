<?php

namespace interactivesolutions\honeycombresources\app\models\resources;


use InteractiveSolutions\HoneycombCore\Models\HCUuidModel;

class HCThumbs extends HCUuidModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'hc_resources_thumbs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'global', 'width', 'height', 'fit'];

}
