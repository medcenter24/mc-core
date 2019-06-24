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
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

namespace medcenter24\mcCore\App\Services\Core;


use Illuminate\Database\Eloquent\Relations\Relation;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class ModelService
{
    public const MODEL_NAMESPACE = '\medcenter24\mcCore\App\\';

    /**
     * List of the Models with parameters
     * @return array
     * @throws ReflectionException
     */
    public function getModels(): array
    {
        $models = [];
        foreach(glob(app_path().'/*.php') as $modelFile) {
            $className = mb_substr($modelFile, mb_strrpos($modelFile, '/')+1);
            $className = trim($className, '.php');
            $classNameNamespace = self::MODEL_NAMESPACE.$className;
            $class = new ReflectionClass($classNameNamespace);
            if ($class->isAbstract()) {
                continue;
            }
            $visible = $class->getProperty('visible');
            $visible->setAccessible(true);
            $obj = new $classNameNamespace;
            $models[] = [
                'id' => $className,
                'path' => $modelFile,
                'class' => $classNameNamespace,
                'params' => $visible->getValue($obj)
            ];
        }
        return $models;
    }

    /**
     * Returns all existing relations, described in the model
     * @param string $modelName
     * @return array
     * @throws ReflectionException
     */
    public function getRelations(string $modelName): array
    {
        $relations = [];
        $classNameNamespace = self::MODEL_NAMESPACE.$modelName;
        $class = new ReflectionClass($classNameNamespace);
        if (!$class->isAbstract()) {
            $methods = $class->getMethods();
            $obj = new $classNameNamespace;
            /** @var ReflectionMethod $method */
            foreach ($methods as $method) {
                if ($method->hasReturnType()) {
                    $type = $method->getReturnType();
                    if (is_subclass_of($type->getName(), Relation::class, true)) {
                        // var_dump($method, $method->invoke($obj),1);die;
                        // var_dump($method, $obj->createdBy()->getRelated());die;
                        /** @var Relation $relation */
                        $relation = $method->invoke($obj);
                        $relations[] = [
                            'relation' => $type->getName(),
                            'name' => $method->getName(),
                            'relatedTo' => get_class($relation->getRelated()),
                            'foreign_key' => $relation->getQualifiedParentKeyName(),
                        ];
                    }
                }
            }
        }
        return $relations;
    }
}
