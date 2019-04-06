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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Invoice can be sent to the Assistant to paid for the guarantee patient
 *
 * Class Invoice
 * @package App
 */
class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'payment_id', 'type', 'created_by', 'status'];
    protected $visible = ['created_by', 'title', 'payment_id', 'type', 'status'];

    /**
     * File uploader
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function uploads(): MorphMany
    {
        return $this->morphMany(Upload::class, 'uploadable');
    }

    /**
     * Invoice body stored as a FormReport element
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function forms(): MorphToMany
    {
        return $this->morphToMany(Form::class, 'formable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
