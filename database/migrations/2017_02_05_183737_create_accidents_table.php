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
            $table->string('accident_ref_num')->default('')->index();
            $table->string('accident_title')->default('')->index();
            $table->unsignedInteger('accident_city_id')->default(0)->index();
            $table->string('accident_address')->default('');
            $table->text('accident_contacts')->default('');
            $table->unsignedInteger('assistant_id')->default(0)->index();
            $table->string('assistant_ref_num')->default('')->index();
            $table->morphs('caseable');
            $table->unsignedInteger('accident_statusable_id')->index();
            $table->text('symptoms');
            $table->timestamps();
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
