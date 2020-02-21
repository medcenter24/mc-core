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
use Illuminate\Database\Eloquent\SoftDeletes;
use medcenter24\mcCore\App\Services\AccidentStatusesService;

/**
 * Statuses of the Accident
 *
 * Class AccidentStatus
 * @package App
 */
class AccidentStatus extends Model
{
    use SoftDeletes;

    protected $fillable = AccidentStatusesService::FILLABLE;
    protected $visible  = AccidentStatusesService::VISIBLE;

    public function accidents()
    {
        return $this->morphedByMany(Accident::class, 'accident_statusable');
    }

    public function history()
    {
        return $this->morphTo();
    }
}
