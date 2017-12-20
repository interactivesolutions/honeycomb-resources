<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class ChangeSizeFieldTypeToBigIntInHcResourcesTable
 */
class ChangeSizeFieldTypeToBigIntInHcResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('hc_resources', function (Blueprint $table) {
            $table->bigInteger('size')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('hc_resources', function (Blueprint $table) {
            $table->integer('size')->change();
        });
    }
}
