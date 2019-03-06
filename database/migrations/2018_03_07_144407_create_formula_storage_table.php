<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormulaStorageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('formula_storage', function (Blueprint $table) {
            // I need this id to determine order for this formula
            $table->increments('id');
            $table->unsignedInteger('formula_id');
            // all Operations and closeNestedFormula as a const value
            $table->string('operation_class');
            // all models and needs to be add const to determine fixed value also it could be a nested formula
            $table->string('variable_class')->default('');
            $table->string('variable_id')->default(''); // identifier for the model or value of the fixed value
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
        Schema::dropIfExists('formula_storage');
    }
}
