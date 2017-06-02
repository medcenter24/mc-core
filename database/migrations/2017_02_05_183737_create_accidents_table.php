<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accidents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('created_by')->default(0)->index();
            $table->unsignedInteger('parent_id')->default(0)->index();
            $table->unsignedInteger('patient_id')->default(0)->index();
            $table->unsignedInteger('accident_type_id')->default(0)->index();
            $table->unsignedInteger('accident_status_id')->default(0)->index();
            $table->unsignedInteger('assistant_id')->default(0)->index();
            $table->unsignedInteger('form_report_id')->default(0)->index();
            $table->string('assistant_ref_num')->default('')->index();
            $table->morphs('caseable');
            $table->string('ref_num')->default('')->index();
            $table->string('title')->default('')->index();
            $table->unsignedInteger('city_id')->default(0)->index();
            $table->string('address')->default('');
            $table->text('contacts')->default('');
            $table->text('symptoms');
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
        Schema::dropIfExists('accidents');
    }
}
