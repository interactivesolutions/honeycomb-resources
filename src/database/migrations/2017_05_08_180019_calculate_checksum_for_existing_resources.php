<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use interactivesolutions\honeycombresources\app\models\HCResources;

/**
 * Class CalculateChecksumForExistingResources
 */
class CalculateChecksumForExistingResources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        /** @var HCResources[] $list */
        $list = HCResources::get();

        foreach ($list as $resource) {
            if ($resource->size <= env('MAX_CHECKSUM_SIZE', 102400000)) {
                $resource->update([
                    'checksum' => hash_file('sha256', storage_path('app/' . $resource->path)),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        /** @var HCResources[] $list */
        $list = HCResources::get();

        foreach ($list as $resource) {
            $resource->update(['checksum' => '']);
        }
    }
}
