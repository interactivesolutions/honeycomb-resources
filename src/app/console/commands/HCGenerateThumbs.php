<?php

namespace interactivesolutions\honeycombresources\app\console\commands;

use InteractiveSolutions\HoneycombCore\Console\HCCommand;
use interactivesolutions\honeycombresources\app\helpers\HCImageThumbs;
use interactivesolutions\honeycombresources\app\models\HCResources;
use interactivesolutions\honeycombresources\app\models\resources\HCThumbs;

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
    public function handle()
    {
        $id = $this->argument('id');
        $rule = $this->argument('rule');

        if ($id && $rule) {
            $this->generateThumbByRule($id, $rule);
        } else {
            if ($id) {
                $this->generateGlobalThumb($id);
            } else {

                if ($this->confirm('Are you sure you want to generate thumbs for all resources?')) {
                    $list = HCResources::all()->pluck('id');

                    foreach ($list as $id) {
                        $this->generateGlobalThumb($id);
                    }
                }
            }
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

        if (!file_exists(storage_path('app/') . $resource->path)) {
            return;
        }

        if (strpos($resource->mime_type, 'image') === false || strpos($resource->mime_type, 'svg') !== false) {
            return;
        }

        if (!$resource) {
            throw new \Exception('Resource not found: ' . $id);
        }

        $thumbRules = HCThumbs::where('global', 1)->get();
        $dest = implode('/', str_split(str_pad($resource->count, 9, '0', STR_PAD_LEFT), 3)) . '/';

        foreach ($thumbRules as $rule) {
            $destination = generateResourcePublicLocation($dest, $rule->width, $rule->height,
                    $rule->fit) . $resource->extension;
            createImage(storage_path('app/') . $resource->path, $destination, $rule->width, $rule->height, $rule->fit);
        }
    }

    /**
     * Generate thumb by given thumb settings id
     *
     * @param $resourceId
     * @param $thumbId
     */
    private function generateThumbByRule($resourceId, $thumbId)
    {
        $thumbs = new HCImageThumbs();

        $thumbs->generate($resourceId, $thumbId);
    }
}
