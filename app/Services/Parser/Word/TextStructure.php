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

namespace app\Services\Parser\Word;

/**
 * To sample comparation world files
 * and getting text from them I need to build simple tree to the text nodes
 *
 * Class TextStructure
 * @package app\Services\Parser\Word
 */
class TextStructure
{
    /**
     * Load file to parse
     * @param string $path - path to the xml file with text
     */
    public function load($path = '')
    {
        $dom = new \DOMDocument();
    }

    /**
     * Getting only text nodes from the document
     * also will be deleted everything without text
     * and text should be moved to the up of the nodes without text
     * I mean, examle:
     * <noded1>
     * <table><thead></thead><tr><td><w:t>Text</w:t></td></table>
     * </node1>
     * <node2>
     *  <w:t>Text</w:t>
     * </node2>
     *
     * Will be parsed to:
     * <node1>Text</node1>
     * <node2>Text</node2>
     *
     * In that way I can get full picture without garbadge
     */
    public function stripText()
    {

    }
}
