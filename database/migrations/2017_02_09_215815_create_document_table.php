<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\Services\DocumentService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('created_by', false, true)->default(0)->index();
            $table->string('title')->default('')->index();
            $table->enum('type', DocumentService::TYPES)->default(DocumentService::TYPE_PASSPORT);
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
        Schema::dropIfExists('documents');
    }
}
