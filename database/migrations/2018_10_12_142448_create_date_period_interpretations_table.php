<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDatePeriodInterpretationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('date_period_interpretations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('date_period_id')->index();
            $table->unsignedTinyInteger('day_of_week')->index();
            $table->time('from')->index(); // utc date (time())
            $table->time('to')->index(); // utc date (time())
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('date_period_interpretations');
    }
}
