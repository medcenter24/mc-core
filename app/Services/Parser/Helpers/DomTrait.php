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

namespace App\Services\Parser\Helpers;


trait DomTrait
{
    public function domToArray(\DOMNode $root, $withAttributes = true, $stripText = false)
    {
        $result = [];

        if ($withAttributes && $root->hasAttributes())
        {
            foreach ($root->attributes as $attr) {
                if ($attr && $attr instanceof \DOMAttr) {
                    $result[$attr->nodeName] = $attr->nodeValue;
                }
            }
        }

        if ($root->hasChildNodes()) {
            $children = $root->childNodes;
            if ($children->length == 1) {
                $child = $children->item(0);

                if ($child->nodeType == XML_TEXT_NODE) {
                    $result['_value'] = self::getText($child->nodeValue, $stripText);

                    if (count($result) == 1) {
                        return $result['_value'];
                    } else {
                        return $result;
                    }
                }
            }

            $group = [];
            foreach ($children as $child) {

                $resultChild = self::domToArray($child, $withAttributes, $stripText);
                if (!$resultChild && $stripText) {
                    continue;
                }

                if (!isset($group[$child->nodeName]) && !isset($result[$child->nodeName])) {
                    $result[$child->nodeName] = $resultChild;
                } else {
                    if (!isset($group[$child->nodeName])) {
                        $tmp = $result[$child->nodeName];
                        unset($result[$child->nodeName]);
                        $result[$child->nodeName . ':0'] = [$tmp];
                        $group[$child->nodeName] = 0;
                    }

                    $result[$child->nodeName . ':' . ++$group[$child->nodeName]][] = $resultChild;
                }
            }
        } elseif ($root instanceof \DOMText) {
            return self::getText($root->wholeText, $stripText);
        }

        return $result;
    }

    private static function getText($string, $stripText=false)
    {
        if ($stripText) {
            $string = trim($string);
            if (!mb_strlen($string)) {
                $string = false;
            }
        }

        return $string;
    }

}
