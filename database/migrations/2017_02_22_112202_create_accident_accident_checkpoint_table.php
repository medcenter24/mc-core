<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccidentAccidentCheckpointTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accident_accident_checkpoint', function (Blueprint $table) {
            $table->integer('accident_id', false, true);
            $table->integer('accident_checkpoint_id', false, true);

            $table->primary(['accident_id', 'accident_checkpoint_id'], 'ind_accident_checkpoint');
            $table->index('accident_checkpoint_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accident_accident_checkpoint');
    }
}
