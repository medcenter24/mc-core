<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderFieldToFinanceConditionsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if ( ! Schema::hasColumn( 'finance_conditions', 'order' ) ) {
            Schema::table( 'finance_conditions', function ( Blueprint $table ) {
                $table->unsignedInteger( 'order' )->default(0)->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'finance_conditions', function ( Blueprint $table ) {
            $table->dropColumn( 'order' );
        } );
    }
}
