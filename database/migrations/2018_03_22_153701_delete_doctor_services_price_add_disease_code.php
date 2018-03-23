<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteDoctorServicesPriceAddDiseaseCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doctor_services', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->string('disease_code')->default('');
            $table->index('disease_code', 'idx_disease_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('doctor_services', function (Blueprint $table) {
            $table->dropIndex('idx_disease_code');
            $table->dropColumn('disease_code');
            $table->decimal('price', 8, 2)->default(0);
        });
    }
}
