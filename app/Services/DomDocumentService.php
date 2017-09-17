<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services;


use App\Support\Core\Configurable;

class DomDocumentService extends Configurable
{

    const CONFIG_WITHOUT_ATTRIBUTES = 'no_attributes';
    const STRIP_STRING = 'strip_string';

    /**
     * Convert dom to array
     *
     * @param \DOMNode $root
     * @return array|bool|string
     */
    public function toArray(\DOMNode $root)
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
            if ($children->length == 1) {
                $child = $children->item(0);

                if ($child->nodeType == XML_TEXT_NODE) {
                    $result['_value'] = $this->getString($child->nodeValue);

                    if (count($result) == 1) {
                        return $result['_value'];
                    } else {
                        return $result;
                    }
                }
            }

            $group = [];
            foreach ($children as $child) {

                $resultChild = $this->toArray($child);
                if ($this->getOption(self::STRIP_STRING)
                    && !$resultChild && (!is_string($resultChild) || !mb_strlen($resultChild))) {

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
            return $this->getString($root->wholeText);
        }

        return $result;
    }

    private function getString($string = '')
    {
        if ($this->getOption(self::STRIP_STRING)) {
            $string = trim($string);
            if (!mb_strlen($string)) {
                $string = false;
            }
        }

        return $string;
    }
}
