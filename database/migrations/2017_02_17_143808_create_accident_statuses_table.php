<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccidentStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accident_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 50)->default('');
            $table->string('type', 50)->default('')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['title', 'type'], 'ik_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accident_statuses');
    }
}
