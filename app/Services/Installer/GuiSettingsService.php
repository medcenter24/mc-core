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

namespace medcenter24\mcCore\App\Services\Installer;


use medcenter24\mcCore\App\Helpers\FileHelper;

class GuiSettingsService
{
    private $guiDoctorDir;
    private $guiDirectorDir;

    public function storeConfig(array $params): void
    {
        $this->createFolders();
        $this->createFiles($params);
    }

    private function createFolders(): void
    {
        $settings = dirname(__DIR__, 4) . DIRECTORY_SEPARATOR . 'settings';
        if (FileHelper::isDirExists($settings)) {
            FileHelper::delete($settings);
        }

        FileHelper::createDirRecursive([$settings, 'guiDirector', 'environments']);
        $this->guiDirectorDir = $settings . '/guiDirector/environments/';
        FileHelper::createDirRecursive([$settings, 'guiDoctor', 'environments']);
        $this->guiDoctorDir = $settings . '/guiDoctor/environments/';
    }

    private function createFiles(array $params): void
    {
        FileHelper::writeFile($this->guiDirectorDir.'environment.prod.ts', "export const env = {\n\tproduction: true,\n\tapiHost: '"
            .$params[JsonSeedReaderService::PROP_DIRECTOR_PROD_HOST]."',\n\tprojectName: '"
            .$params[JsonSeedReaderService::PROP_DIRECTOR_PROD_PROJECT_NAME]."',\n\tdoctorLink: '"
            .$params[JsonSeedReaderService::PROP_DIRECTOR_DOCTOR_PROD_HOST]."',\n};\n");
        FileHelper::writeFile($this->guiDirectorDir.'environment.ts', "export const env = {\n\tproduction: false,\n\tapiHost: '"
            .$params[JsonSeedReaderService::PROP_DIRECTOR_DEV_HOST]."',\n\tprojectName: '"
            .$params[JsonSeedReaderService::PROP_DIRECTOR_DEV_PROJECT_NAME]."',\n\tdoctorLink: '"
            .$params[JsonSeedReaderService::PROP_DIRECTOR_DOCTOR_DEV_HOST]."',\n};\n");

        FileHelper::writeFile($this->guiDoctorDir . 'development.js', "module.exports = {\n\tmode: '\"development\"',\n\tapiHost: '\""
            .$params[JsonSeedReaderService::PROP_DOCTOR_DEV_HOST]."\"'\n};\n");
        FileHelper::writeFile($this->guiDoctorDir . 'production.js', "module.exports = {\n\tmode: '\"production\"',\n\tapiHost: '\""
            .$params[JsonSeedReaderService::PROP_DOCTOR_PROD_HOST]."\"'\n};\n");
    }
}
