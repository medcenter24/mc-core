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
declare(strict_types = 1);

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use medcenter24\mcCore\App\Entity\Diagnostic;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use Illuminate\Database\Seeder;
use medcenter24\mcCore\App\Entity\Document;

class DoctorAccidentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DoctorAccident::truncate();

        DB::table('diagnosticables')
            ->where('diagnosticable_type', DoctorAccident::class)
            ->delete();

        // 10 doc accidents without docs
        DoctorAccident::factory()->count(10)->create();
        // 10 doc accidents with docs
        DoctorAccident::factory()->count(10)
            ->create()
            ->each(function ($doctorAccident) {
                for ($i=0; $i<2; $i++) {
                    $doctorAccident->documents()->save(Document::factory()->make());
                    $doctorAccident->diagnostics()->save(Diagnostic::factory()->make());
                }
            });
    }
}
