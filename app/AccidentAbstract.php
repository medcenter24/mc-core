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

namespace medcenter24\mcCore\App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccidentAbstract extends Model
{
    use SoftDeletes;

    public function accident(): MorphOne
    {
        return $this->morphOne(Accident::class, 'caseable');
    }

    /**
     * Assignment from the Doctor_Accident to the status action with comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function statusHistory(): MorphMany
    {
        return $this->morphMany(AccidentStatusHistory::class, 'historyable');
    }

    /**
     * Selected by doctor diagnostics
     */
    public function diagnostics(): MorphToMany
    {
        return $this->morphToMany(Diagnostic::class, 'diagnosticable');
    }

    /**
     * Each DoctorAccident is able to has own services, created by a doctor
     * but by default it could be defined by the director
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function services(): MorphToMany
    {
        return $this->morphToMany(DoctorService::class, 'doctor_serviceable');
    }

    /**
     * As same as serviceable()
     * each doctorAccident is able to has his own survey
     * but by default it could be defined by the director
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function surveys(): MorphToMany
    {
        return $this->morphToMany(DoctorSurvey::class, 'doctor_surveable');
    }

    /**
     * Photos of the documents from the patient
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function documents(): MorphToMany
    {
        return $this->morphToMany(Document::class, 'documentable');
    }


}
