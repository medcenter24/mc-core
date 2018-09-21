<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\Services\FinanceService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceConditionsTable extends Migration
{

    public function up(FinanceService $service)
    {
        Schema::create('finance_conditions', function (Blueprint $table) use ($service) {
            $table->increments('id');
            $table->unsignedInteger('created_by')->default(0)->index();
            $table->string('title');
            $table->enum('type', $service->getTypes());
            $table->decimal('price');
            $table->enum('currency', $service->getCurrencies());
            $table->string('model');
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
        Schema::dropIfExists('finance_conditions');
    }
}
