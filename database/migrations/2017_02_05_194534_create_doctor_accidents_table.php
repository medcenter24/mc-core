<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\AccidentDoctor;
use App\DoctorAccident;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDoctorAccidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doctor_accidents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('doctor_id')->default(0)->index();
            $table->unsignedInteger('city_id')->default(0)->index();
            $table->text('recommendation')->default('');
            $table->text('investigation')->default('');
            $table->timestamp('visit_time')->nullable();
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
        Schema::dropIfExists('doctor_accidents');
    }
}
