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

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * All changes which would be on accident status should include commentary
 * so we will store it in the statuses history
 *
 * Class AccidentStatusHistory
 * @package App
 */
class AccidentStatusHistory extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'accident_status_id', 'historyable_id', 'historyable_type', 'commentary'];

    public function accidentStatus()
    {
        return $this->belongsTo(AccidentStatus::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
