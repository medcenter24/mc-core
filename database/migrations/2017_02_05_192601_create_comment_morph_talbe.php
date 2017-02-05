<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentMorphTalbe extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comment_morph', function (Blueprint $table) {
            $table->integer('comment_id');
            $table->morphs('bind');

            $table->primary(['comment_id', 'bind_type', 'bind_id']);
            $table->index(['bind_type', 'bind_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comment_morph');
    }
}
