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

namespace medcenter24/mcCore;


use medcenter24\mcCore\App\Helpers\DoctorTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Kinds of the diagnostics that should be done by the Doctor through the Accident
 *
 * Class Diagnostic
 * @package App
 */
class Diagnostic extends Model
{
    use SoftDeletes;
    use DoctorTrait;

    protected $fillable = ['title', 'description', 'diagnostic_category_id', 'disease_code', 'created_by'];
    protected $visible = ['title', 'description', 'diagnostic_category_id', 'disease_code'];

    public function category()
    {
        return $this->belongsTo(DiagnosticCategory::class);
    }

    public function diagnosticDoctorAccidents()
    {
        return $this->morphedByMany(DoctorAccident::class, 'diagnosticable');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
