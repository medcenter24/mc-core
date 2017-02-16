<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiagnosticDoctorAccidentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diagnostic_doctor_accident', function (Blueprint $table) {
            $table->integer('doctor_accident_id', false, true);
            $table->integer('diagnostic_id', false, true);

            $table->primary(['doctor_accident_id', 'diagnostic_id'], 'ind_doctor_accident_diagnostic');
            $table->index('diagnostic_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('diagnostic_doctor_accident');
    }
}
