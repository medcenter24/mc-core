<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finance_conditions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('created_by')->default(0)->index();
            $table->string('title');
            $table->decimal('price');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('finance_conditions');
    }
}
