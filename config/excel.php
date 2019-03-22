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

use Maatwebsite\Excel\Excel;

return [
    'exports'            => [

        /*
        |--------------------------------------------------------------------------
        | Chunk size
        |--------------------------------------------------------------------------
        |
        | When using FromQuery, the query is automatically chunked.
        | Here you can specify how big the chunk should be.
        |
        */
        'chunk_size' => 1000,

        /*
        |--------------------------------------------------------------------------
        | Temporary path
        |--------------------------------------------------------------------------
        |
        | When exporting files, we use a temporary file, before storing
        | or downloading. Here you can customize that path.
        |
        */
        'temp_path'  => sys_get_temp_dir(),

        /*
        |--------------------------------------------------------------------------
        | CSV Settings
        |--------------------------------------------------------------------------
        |
        | Configure e.g. delimiter, enclosure and line ending for CSV exports.
        |
        */
        'csv'        => [
            'delimiter'              => ',',
            'enclosure'              => '"',
            'line_ending'            => PHP_EOL,
            'use_bom'                => false,
            'include_separator_line' => false,
            'excel_compatibility'    => false,
        ],
    ],

    'imports' => [

        'heading_row' => [

            /*
            |--------------------------------------------------------------------------
            | Heading Row Formatter
            |--------------------------------------------------------------------------
            |
            | Configure the heading row formatter.
            | Available options: none|slug|custom
            |
            */
            'formatter' => 'slug',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Extension detector
    |--------------------------------------------------------------------------
    |
    | Configure here which writer type should be used when
    | the package needs to guess the correct type
    | based on the extension alone.
    |
    */
    'extension_detector' => [
        'xlsx'     => Excel::XLSX,
        'xlsm'     => Excel::XLSX,
        'xltx'     => Excel::XLSX,
        'xltm'     => Excel::XLSX,
        'xls'      => Excel::XLS,
        'xlt'      => Excel::XLS,
        'ods'      => Excel::ODS,
        'ots'      => Excel::ODS,
        'slk'      => Excel::SLK,
        'xml'      => Excel::XML,
        'gnumeric' => Excel::GNUMERIC,
        'htm'      => Excel::HTML,
        'html'     => Excel::HTML,
        'csv'      => Excel::CSV,
        'tsv'      => Excel::TSV,

        /*
        |--------------------------------------------------------------------------
        | PDF Extension
        |--------------------------------------------------------------------------
        |
        | Configure here which Pdf driver should be used by default.
        | Available options: Excel::MPDF | Excel::TCPDF | Excel::DOMPDF
        |
        */
        'pdf'      => Excel::MPDF, // Excel::DOMPDF,
    ],
];
