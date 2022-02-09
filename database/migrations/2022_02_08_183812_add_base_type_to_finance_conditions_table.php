<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use medcenter24\mcCore\App\Services\Entity\FinanceConditionService;

class AddBaseTypeToFinanceConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'finance_conditions', function ( Blueprint $table ) {

            // create new col
            $table->string( 'type2', 10 )
                ->default(FinanceConditionService::PARAM_TYPE_BASE)
                ->index();
        });

        // migrate values
        $this->migrateData();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'finance_conditions', function ( Blueprint $table ) {
            $table->enum('type2', [
                FinanceConditionService::PARAM_TYPE_SUBTRACT,
                FinanceConditionService::PARAM_TYPE_ADD,
                FinanceConditionService::PARAM_TYPE_BASE,
            ])->default(FinanceConditionService::PARAM_TYPE_ADD);
        });

        // migrate values
        $this->migrateData();
    }

    private function migrateData(): void
    {
        $oldData = DB::table('finance_conditions')->get();
        foreach ($oldData as $row) {
            DB::table('finance_conditions')
                ->where('id', $row->id)
                ->update([
                    'type2' => $row->type,
                ]);
        }

        Schema::table( 'finance_conditions', function ( Blueprint $table ) {
            // drop old
            $table->dropColumn( 'type' );
        });

        Schema::table( 'finance_conditions', function ( Blueprint $table ) {
            // rename
            $table->renameColumn('type2', 'type');
        });
    }
}
