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
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\AccidentCheckpoint;
use medcenter24\mcCore\App\Entity\AccidentStatus;
use medcenter24\mcCore\App\Entity\AccidentStatusHistory;
use medcenter24\mcCore\App\Entity\AccidentType;
use medcenter24\mcCore\App\Entity\Assistant;
use medcenter24\mcCore\App\Entity\City;
use medcenter24\mcCore\App\Entity\Country;
use medcenter24\mcCore\App\Entity\DatePeriod;
use medcenter24\mcCore\App\Entity\Diagnostic;
use medcenter24\mcCore\App\Entity\Doctor;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\Service;
use medcenter24\mcCore\App\Entity\Survey;
use medcenter24\mcCore\App\Entity\Document;
use medcenter24\mcCore\App\Entity\FinanceCondition;
use medcenter24\mcCore\App\Entity\FinanceCurrency;
use medcenter24\mcCore\App\Entity\FinanceStorage;
use medcenter24\mcCore\App\Entity\Form;
use medcenter24\mcCore\App\Entity\FormReport;
use medcenter24\mcCore\App\Entity\Hospital;
use medcenter24\mcCore\App\Entity\HospitalAccident;
use medcenter24\mcCore\App\Entity\Invoice;
use medcenter24\mcCore\App\Entity\Patient;
use medcenter24\mcCore\App\Entity\Payment;
use medcenter24\mcCore\App\Entity\Region;
use medcenter24\mcCore\App\Entity\Scenario;
use medcenter24\mcCore\App\Services\Entity\DocumentService;

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
            Service::truncate();
            AccidentStatusHistory::truncate();
            Hospital::truncate();
            HospitalAccident::truncate();
            Invoice::truncate();
            DatePeriod::truncate();
            FinanceCondition::truncate();
            FinanceStorage::truncate();
            Survey::truncate();
            Payment::truncate();
            DB::table('documentables')->truncate();
            DB::table('diagnosticables')->truncate();
            DB::table('serviceables')->truncate();
            DB::table('surveables')->truncate();
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
