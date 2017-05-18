<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiagnosticableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diagnosticable', function (Blueprint $table) {
            $table->unsignedInteger('diagnostic_id');
            $table->integer('diagnosticable_id', false, true);
            $table->integer('diagnosticable_type', false, true);

            $table->primary(['diagnosticable_id', 'diagnosticable_type'], 'ind_diagnosticable');
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
        Schema::dropIfExists('diagnosticable');
    }
}
