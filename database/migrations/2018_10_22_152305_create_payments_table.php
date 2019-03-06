<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * With this table I can do account statement
 * Class CreatePaymentsTable
 */
class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('created_by')->default(0)->index();
            $table->decimal('value', 8, 2);
            $table->unsignedInteger('currency_id')->default(0)->index();
            $table->tinyInteger('fixed'); // the flag that value don't being changeable automatically, hand mode only
            $table->text('description'); // additional information in the json format (who paid, which documents were used, etc)
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
        Schema::dropIfExists('payments');
    }
}
