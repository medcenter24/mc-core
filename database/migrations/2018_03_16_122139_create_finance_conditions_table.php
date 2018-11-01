<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\Services\FinanceConditionService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceConditionsTable extends Migration
{

    public function up()
    {
        Schema::create('finance_conditions', function (Blueprint $table) {
            $service = new FinanceConditionService();

            $table->increments('id');
            $table->unsignedInteger('created_by')->default(0)->index();
            $table->string('title');
            $table->enum('type', $service->getTypes());
            $table->decimal('value');
            $table->unsignedInteger('currency_id')->index();
            $table->string('currency_mode'); // percent, currency
            $table->string('model'); // accident, doctor, hospital
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
