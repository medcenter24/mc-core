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
            // if it is a second or more call
            $table->unsignedInteger('parent_id')->default(0)->index();
            $table->unsignedInteger('patient_id')->default(0)->index();
            // insurance or not insurance
            $table->unsignedInteger('accident_type_id')->default(0)->index();
            // current status
            $table->unsignedInteger('accident_status_id')->default(0)->index();
            // assistants data
            $table->unsignedInteger('assistant_id')->default(0)->index();
            $table->string('assistant_ref_num')->default('')->index(); // ref number of the assistant
            $table->unsignedInteger('assistant_invoice_id')->default(0)->index();
            $table->unsignedInteger('assistant_guarantee_id')->default(0)->index();
            // report for printing the case
            $table->unsignedInteger('form_report_id')->default(0)->index();
            $table->unsignedInteger('city_id')->default(0)->index(); // where from the patient
            // payments
            $table->unsignedInteger('caseable_payment_id')->default(0)->index(); // doctors or hospitals revenue
            $table->unsignedInteger('income_payment_id')->default(0)->index(); // company's income from the case
            $table->unsignedInteger('assistant_payment_id')->default(0)->index(); // what assistant paid to the company
            //
            $table->nullableMorphs('caseable', 'ids_caseable'); // doctor or hospital case
            $table->string('ref_num')->default('')->index(); // mydocs unique referral number
            $table->string('title')->default('')->index(); // title to handle this test (if needed)
            $table->string('address')->default(''); // patient address
            $table->timestamp('handling_time')->nullable()->index(); // when case was sent to the hospital or to the doctor
            $table->text('contacts'); // patient contacts
            $table->text('symptoms'); // patient symptoms
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
