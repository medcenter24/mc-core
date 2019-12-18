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

namespace medcenter24\mcCore\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use medcenter24\mcCore\App\Accident;
use medcenter24\mcCore\App\AccidentCheckpoint;
use medcenter24\mcCore\App\AccidentStatus;
use medcenter24\mcCore\App\AccidentStatusHistory;
use medcenter24\mcCore\App\AccidentType;
use medcenter24\mcCore\App\Assistant;
use medcenter24\mcCore\App\City;
use medcenter24\mcCore\App\Country;
use medcenter24\mcCore\App\DatePeriod;
use medcenter24\mcCore\App\Diagnostic;
use medcenter24\mcCore\App\Doctor;
use medcenter24\mcCore\App\DoctorAccident;
use medcenter24\mcCore\App\DoctorService;
use medcenter24\mcCore\App\DoctorSurvey;
use medcenter24\mcCore\App\Document;
use medcenter24\mcCore\App\FinanceCondition;
use medcenter24\mcCore\App\FinanceCurrency;
use medcenter24\mcCore\App\FinanceStorage;
use medcenter24\mcCore\App\Form;
use medcenter24\mcCore\App\FormReport;
use medcenter24\mcCore\App\Hospital;
use medcenter24\mcCore\App\HospitalAccident;
use medcenter24\mcCore\App\Invoice;
use medcenter24\mcCore\App\Patient;
use medcenter24\mcCore\App\Payment;
use medcenter24\mcCore\App\Region;
use medcenter24\mcCore\App\Scenario;
use medcenter24\mcCore\App\Services\DocumentService;

class CleanStorageDev extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:cleaner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete stored data';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!app()->environment('production')) {
            Accident::truncate();
            AccidentStatus::truncate();
            Scenario::truncate();
            AccidentType::truncate();
            FinanceCurrency::truncate();
            City::truncate();
            Region::truncate();
            Country::truncate();
            Diagnostic::truncate();
            Document::truncate();
            $this->deleteDocuments();
            Doctor::truncate();
            DoctorAccident::truncate();
            Assistant::truncate();
            AccidentCheckpoint::truncate();
            Form::truncate();
            FormReport::truncate();
            Patient::truncate();
            DoctorService::truncate();
            AccidentStatusHistory::truncate();
            Hospital::truncate();
            HospitalAccident::truncate();
            Invoice::truncate();
            DatePeriod::truncate();
            FinanceCondition::truncate();
            FinanceStorage::truncate();
            DoctorSurvey::truncate();
            Payment::truncate();
            DB::table('documentables')->truncate();
            DB::table('diagnosticables')->truncate();
            DB::table('doctor_serviceables')->truncate();
            DB::table('doctor_surveables')->truncate();
            DB::table('import_logs')->truncate();
            DB::table('media')->truncate();
        }
    }

    private function deleteDocuments(): void
    {
        $disc = Storage::disk(DocumentService::DISC_IMPORTS);
        $directories = $disc->allDirectories();
        foreach ($directories as $dir) {
            $disc->deleteDirectory($dir);
        }
    }
}
