<?php

namespace interactivesolutions\honeycombscripts\commands;

use Image;
use interactivesolutions\honeycombcore\commands\HCCommand;
use interactivesolutions\honeycombresources\models\HCResources;
use interactivesolutions\honeycombresources\models\resources\HCThumbs;

class HCGenerateThumbs extends HCCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hc:generate-thumbs {id?} {rule?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates thumbs for all resources';

    /**
     * Execute the console command.
     * @return mixed
     * @throws \Exception
     */
    public function handle ()
    {
        $id = $this->argument('id');

        if ($id)
            $this->generateGlobalThumb($id);
        else
        {
            $list = HCResources::all()->pluck('id');

            foreach ($list as $id)
                $this->generateGlobalThumb($id);
        }
    }

    /**
     * Generating thumbnails for a image record
     *
     * @param $id
     * @throws \Exception
     */
    private function generateGlobalThumb($id)
    {
        $resource = HCResources::find($id);

        if (strpos($resource->mime_type, 'image' === false))
            return;

        if (!$resource)
            throw new \Exception('Resource not found: ' . $id);

        $thumbs_location = public_path('thumbs/' . $id) . '/';
        $this->createDirectory($thumbs_location);

        $thumbRules = HCThumbs::where('global', 1)->get();

        foreach ($thumbRules as $rule)
        {
            $img = Image::make(storage_path('app/') . $resource->path);

            if ($rule->fit)
                $img->fit($rule->width, $rule->height);
            else
                $img->resize($rule->width, $rule->height, function ($constraint) {
                    $constraint->aspectRatio();
                });

            $img->save($thumbs_location . $rule->width . '_' . $rule->height . $resource->extension, 100);
        }
    }
}
