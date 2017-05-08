<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChecksumFieldToResourceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::table ('hc_resources', function (Blueprint $table) {
            $table->string ('checksum', 64)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down ()
    {
        Schema::table ('hc_resources', function (Blueprint $table) {
            $table->dropColumn ('checksum');
        });
    }
}
