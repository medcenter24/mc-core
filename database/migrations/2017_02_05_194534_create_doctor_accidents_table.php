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
            $table->integer('doctor_id', false, true)->default(0)->index();
            $table->integer('city_id', false, true)->default(0)->index();
            $table->enum('status', [
                DoctorAccident::STATUS_NEW,
                DoctorAccident::STATUS_IN_PROGRESS,
                DoctorAccident::STATUS_SIGNED,
                DoctorAccident::STATUS_SENT,
                DoctorAccident::STATUS_PAID,
                DoctorAccident::STATUS_CLOSED,
            ])->index();
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
