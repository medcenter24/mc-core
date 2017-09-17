<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\DocxReader;
use app\Helpers\MediaLibrary\DocPathGenerator;
use DOMDocument;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\Media;
use ZipArchive;

/**
 * Very simple driver for reading of the docx files
 * external sources don't needed, all you need that php
 *
 * Class SimpleDocxReaderService
 * @package app\Services\DocxReader
 */
class SimpleDocxReaderService implements DocxReaderInterface
{
    /**
     * Current file
     * @var string
     */
    private $filePath = '';

    /**
     * Parsed body
     * @var DOMDocument
     */
    private $dom;

    /**
     * Load file to read service
     *
     * @param string $path
     * @return $this
     */
    public function load($path = '')
    {
        $this->filePath = $path;
        return $this;
    }

    /**
     * File path
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * dom as it is
     * @return DOMDocument
     */
    public function getDom()
    {
        $this->dom = $this->docx2text($this->filePath);
        return $this->dom;
    }

    /**
     * Get only text from the document
     * @return string
     */
    public function getText()
    {
        return strip_tags($this->getDom()->saveXML());
    }

    public function getImages()
    {
        return $this->getZippedMedia($this->filePath);
    }

    /**
     * load file
     *
     * @param $filename
     * @return DOMDocument|string
     */
    private function docx2text($filename) {
        return $this->readZippedXML($filename, "word/document.xml");
    }

    /**
     * Get dom of the document
     *
     * @param $archiveFile
     * @param $dataFile
     * @return DOMDocument|string
     */
    private function readZippedXML($archiveFile, $dataFile) {
        Log::info('Open file to read', ['file' => $archiveFile]);
        // Create new ZIP archive
        $zip = new ZipArchive;

        // Open received archive file
        if (true === ($zipErr = $zip->open($archiveFile))) {
            Log::info('Zip was opened', ['file' => $archiveFile]);
            // If done, search for the data file in the archive
            if (($index = $zip->locateName($dataFile)) !== false) {
                Log::info('Index was found', ['file' => $archiveFile]);
                // If found, read it to the string
                $data = $zip->getFromIndex($index);
                // Close archive file
                $zip->close();
                // Load XML from a string
                // Skip errors and warnings
                $xml = new DOMDocument();
                $xml->loadXML($data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                Log::info('XML was loaded', ['file' => $archiveFile]);
                return $xml;
            }
            $zip->close();
        }

        Log::error('File can not be read', ['file' => $archiveFile, 'err' => $zipErr]);
        // In case of failure return empty string
        return false;
    }

    /**
     * Get media from the document
     *
     * @param $archiveFile
     * @return array of medias
     */
    private function getZippedMedia($archiveFile) {
        $files = [];

        Log::info('Open file to read', ['file' => $archiveFile]);
        // Create new ZIP archive
        $zip = new ZipArchive;

        // Open received archive file
        if (true === $zip->open($archiveFile)) {
            Log::info('Zip was opened', ['file' => $archiveFile]);
            // If done, search for the data file in the archive
            // loop through all the files in the archive
            for ( $i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->statIndex($i);
                // is it an image
                if ( $entry['size'] > 0 && preg_match('#\.(jpg|gif|png|jpeg|bmp)$#i', $entry['name'] )) {
                    $file = $zip->getFromIndex($i);
                    if( $file ){
                        $ext = pathinfo(( basename( $entry['name'] ) . PHP_EOL ), PATHINFO_EXTENSION);
                        $files[] = [
                            'name'  => $entry['name'],
                            'ext'   => $ext,
                            'imageContent' => $file,
                        ];
                    }
                }
            }
            $zip->close();
        } else {
            Log::error('File can not be read', ['file' => $archiveFile]);
        }

        // In case of failure return empty string
        return $files;
    }
}
