<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
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
