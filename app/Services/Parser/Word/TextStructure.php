<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
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
