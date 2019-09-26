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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;

class Doctor extends Model implements HasMedia
{
    use SoftDeletes;
    use HasMediaTrait;

    protected $fillable = ['name', 'description', 'ref_key', 'gender', 'medical_board_num', 'user_id'];
    protected $visible  = ['id', 'name', 'description', 'ref_key', 'gender', 'medical_board_num', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * City where doctor is (leave)
     * @return BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Cities which are covered by this doctor
     * @return MorphToMany
     */
    public function cities(): MorphToMany
    {
        return $this->morphToMany(City::class,'cityable');
    }
}
