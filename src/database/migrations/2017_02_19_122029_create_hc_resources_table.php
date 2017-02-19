<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHcResourcesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create ('hc_resources', function (Blueprint $table) {
            $table->integer ('count', true);
            $table->string ('id', 36)->unique ('id');
            $table->timestamps ();
            $table->softDeletes ();
            $table->string ('original_name');
            $table->string ('safe_name');
            $table->integer ('size')->nullable ();
            $table->string ('path', 19)->nullable ();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down ()
    {
        Schema::drop ('hc_resources');
    }

}
