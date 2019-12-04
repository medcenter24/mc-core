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

namespace medcenter24\mcCore\App\Services;


use DOMNode;
use DOMText;
use medcenter24\mcCore\App\Support\Core\Configurable;

class DomDocumentService extends Configurable
{
    public const CONFIG_WITHOUT_ATTRIBUTES = 'no_attributes';
    public const STRIP_STRING = 'strip_string';

    /**
     * Convert dom to array
     *
     * @param DOMNode $root
     * @return array|bool|string
     */
    public function toArray(DOMNode $root)
    {
        $result = [];
        if (!$this->getOption(self::CONFIG_WITHOUT_ATTRIBUTES) && $root->hasAttributes())
        {
            foreach ($root->attributes as $attr) {
                if ($attr && $attr instanceof \DOMAttr) {
                    $result[$attr->nodeName] = $attr->nodeValue;
                }
            }
        }

        if ($root->hasChildNodes()) {
            $children = $root->childNodes;
            if ($children->length === 1) {
                $child = $children->item(0);

                if ($child && $child->nodeType === XML_TEXT_NODE) {
                    $result['_value'] = $this->getString($child->nodeValue);

                    if (count($result) === 1) {
                        return $result['_value'];
                    }

                    return $result;
                }
            }

            $group = [];
            foreach ($children as $child) {

                if (!$child) {
                    continue;
                }

                $resultChild = $this->toArray($child);
                if ((!is_string($resultChild) || $resultChild === '')
                    && !$resultChild && $this->getOption(self::STRIP_STRING)) {

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
        } elseif ($root instanceof DOMText) {
            return $this->getString($root->wholeText);
        }

        return $result;
    }

    private function getString($string = '')
    {
        if ($this->getOption(self::STRIP_STRING)) {
            $string = trim($string);
            if ($string == '') {
                $string = false;
            }
        }

        return $string;
    }
}
