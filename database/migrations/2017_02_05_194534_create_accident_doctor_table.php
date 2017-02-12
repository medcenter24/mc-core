<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\AccidentDoctor;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccidentDoctorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accident_doctors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('doctor_id', false, true)->index();
            $table->integer('city_id', false, true)->index();
            $table->enum('status', [
                AccidentDoctor::STATUS_NEW,
                AccidentDoctor::STATUS_IN_PROGRESS,
                AccidentDoctor::STATUS_SIGNED,
                AccidentDoctor::STATUS_SENT,
                AccidentDoctor::STATUS_PAID,
                AccidentDoctor::STATUS_CLOSED,
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
        Schema::dropIfExists('accident_doctors');
    }
}
