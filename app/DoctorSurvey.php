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

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use medcenter24\mcCore\App\Helpers\DoctorTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use medcenter24\mcCore\App\Services\DoctorServiceService;
use medcenter24\mcCore\App\Services\ServiceLocatorTrait;

class DoctorSurvey extends Model
{
    use SoftDeletes;
    use DoctorTrait;
    use ServiceLocatorTrait;

    protected $fillable = ['title', 'description', 'created_by', 'surveable_id', 'surveable_type'];
    protected $visible = ['title', 'description'];

    /**
     * @return MorphTo
     */
    public function surveable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isDoctor(): bool
    {
        return $this->getServiceLocator()->get(DoctorServiceService::class)->isDoctor($this->created_by);
    }
}
