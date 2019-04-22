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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A lot of checkpoints should be done through Accident
 * so let keep list of them to cross finished steps
 *
 * Class AccidentCheckpoint
 * @package App
 */
class AccidentCheckpoint extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'description'];
    protected $visible = ['id', 'title', 'description'];

    protected function accidents()
    {
        return $this->belongsTo(Accident::class);
    }
}
