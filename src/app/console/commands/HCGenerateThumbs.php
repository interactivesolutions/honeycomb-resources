<?php

namespace interactivesolutions\honeycombscripts\commands;

use Intervention\Image\ImageManagerStatic as Image;
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
        $id = $this->argument ('id');

        if ($id)
            $this->generateGlobalThumb ($id);
        else {
            $list = HCResources::all ()->pluck ('id');

            foreach ($list as $id)
                $this->generateGlobalThumb ($id);
        }
    }

    /**
     * Generating thumbnails for a image record
     *
     * @param $id
     * @throws \Exception
     */
    private function generateGlobalThumb ($id)
    {
        $resource = HCResources::find ($id);

        if (strpos ($resource->mime_type, 'image' === false))
            return;

        if (!$resource)
            throw new \Exception('Resource not found: ' . $id);

        $thumbRules = HCThumbs::where ('global', 1)->get ();

        foreach ($thumbRules as $rule) {
            $destination = generateResourcePublicLocation ($id, $rule->width, $rule->height, $rule->fit) . $resource->extension;
            createImage (storage_path ('app/') . $resource->path, $destination, $rule->width, $rule->height, $rule->fit);
        }
    }
}
