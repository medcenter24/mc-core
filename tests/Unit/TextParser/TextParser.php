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

namespace Tests\Unit\TextParser;


use Tests\TestCase;

class TextParser extends TestCase
{
    /**
     * @var \DOMDocument
     */
    private $dom;

    public function setUp()
    {
        parent::setUp();
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR . 'structure.xml';
        $this->dom = new \DOMDocument();
        $this->dom->loadXML(file_get_contents($path), LIBXML_NOBLANKS | LIBXML_NOCDATA | LIBXML_NOEMPTYTAG | LIBXML_NSCLEAN | LIBXML_PARSEHUGE | LIBXML_PEDANTIC );
    }

    protected function getDom()
    {
        return $this->dom;
    }
}
