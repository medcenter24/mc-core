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

use medcenter24\mcCore\App\Services\Entity\DocumentService;
use medcenter24\mcCore\App\Services\Entity\FormService;
use medcenter24\mcCore\App\Services\File\TmpFileService;
use medcenter24\mcCore\App\Services\LogoService;
use medcenter24\mcCore\App\Services\UploaderService;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => 's3',

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'throw' => false,
        ],

        // @todo hack
        // @todo rewrite extension installer or module that connect filesystems to do it from the CaseImporterService::DISC_IMPORTS
        'imports' => [
            'driver' => 'local',
            'root' => storage_path( 'imports'),
            'throw' => false,
        ],

        DocumentService::DISC_IMPORTS => [
            'driver' => 'local',
            'root' => storage_path(DocumentService::DISC_IMPORTS),
            'throw' => false,
        ],

        LogoService::DISC => [
            'driver' => 'local',
            'root' => storage_path(LogoService::FOLDER),
            'throw' => false,
        ],

        FormService::PDF_DISK => [
            'driver' => 'local',
            'root' => storage_path(FormService::PDF_FOLDER),
            'throw' => false,
        ],

        UploaderService::CONF_DEFAULT => [
            'driver' => 'local',
            'root' => storage_path(UploaderService::CONF_DEFAULT),
            'throw' => false,
        ],

        TmpFileService::DISC => [
            'driver' => 'local',
            'root' => storage_path(TmpFileService::FOLDER),
            'throw' => false,
        ]

        /*
         * @deprecated We can't store in the public path files! everything should be stored in the storage folder
         * 'media' => [
            'driver' => 'local',
            'root' => public_path( 'media'),
        ]*/

    ],
];
