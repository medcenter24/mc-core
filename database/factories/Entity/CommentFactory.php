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
declare(strict_types = 1);

namespace Database\Factories\Entity;

use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\Comment;
use medcenter24\mcCore\App\Entity\User;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    #[ArrayShape([
        'created_by' => "\Closure",
        'text' => "array|string",
        'commentable_type' => "string",
        'commentable_id' => "\Closure"]
    )]
    public function definition(): array
    {
        return [
            'created_by' => function () {
                return User::factory()->create()->id;
            },
            'text' => $this->faker->paragraphs(3, true),
            'commentable_type' => Accident::class,
            'commentable_id' => function () {
                return Accident::factory()->create()->id;
            },
        ];
    }
}
