<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateHospitalAccidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_accidents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('hospital_id')->default(0)->index();
            // link to the form
            $table->unsignedInteger('hospital_guarantee_id')->default(0)->index();
            $table->unsignedInteger('hospital_invoice_id')->default(0)->index();
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
        Schema::dropIfExists('hospital_accidents');
    }
}
