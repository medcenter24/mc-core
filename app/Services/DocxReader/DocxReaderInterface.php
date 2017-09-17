<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace app\Services\DocxReader;


/**
 * Interface provided opportunity to read docx files
 *
 * Interface DocxReaderInterface
 * @package app\Services\DocxReader
 */
interface DocxReaderInterface
{
    /**
     * Load file for reading
     * @return mixed
     */
    public function load();

    /**
     * Object with parsed body
     * @return mixed
     */
    public function getDom();

    /**
     * Get all text from the document
     * @return string
     */
    public function getText();

    /**
     * File path
     * @return string
     */
    public function getFilePath();
}
