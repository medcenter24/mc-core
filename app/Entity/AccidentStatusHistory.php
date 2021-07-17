<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusHistoryService;

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
    use HasFactory;

    protected $fillable = AccidentStatusHistoryService::FILLABLE;

    /**
     * @return BelongsTo
     */
    public function accidentStatus(): BelongsTo
    {
        return $this->belongsTo(AccidentStatus::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
