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

namespace Tests\Feature\Console;


use App\Contract\General\Environment;
use App\Helpers\FileHelper;
use App\Services\EnvironmentService;
use App\Services\Installer\InstallerService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class SetupEnvironmentCommandTest extends TestCase
{
    use DatabaseMigrations;

    public function test_installing_generate_config(): void
    {
        $this->artisan('setup:environment')
            ->expectsQuestion('Are you sure want to install application with these parameters?', 'no')
            ->assertExitCode(0);
    }

    public function test_install_on_the_installed(): void
    {
        EnvironmentService::init(realpath(__DIR__ . '/samples/default.conf.php'));
        $this->artisan('setup:environment')
            ->expectsOutput('Environment already configured')
            ->assertExitCode(0);
    }

    public function test_installing_env(): void
    {
        $configDir = __DIR__ . '/samples/sandbox/config';
        $confFile = 'generis.conf.php';
        $envFile = '.env';
        $dataDir = __DIR__ . '/samples/sandbox/data';
        $this->artisan('setup:environment', [
            '--' . InstallerService::PROP_CONFIG_DIR => $configDir,
            '--' . InstallerService::PROP_CONFIG_FILE_NAME => $confFile,
            '--' . InstallerService::PROP_ENV_FILE_NAME => $envFile,
            '--' . InstallerService::PROP_DATA_DIR => $dataDir,
        ])
            ->expectsQuestion('Are you sure want to install application with these parameters?', 'yes')
            ->assertExitCode(0);

        self::assertDirectoryExists($configDir);
        self::assertDirectoryExists($dataDir);
        self::assertFileExists($configDir.DIRECTORY_SEPARATOR.$confFile);
        $checkConf = include $configDir . DIRECTORY_SEPARATOR . $confFile;
        self::assertArrayHasKey(Environment::ENV_FILE, $checkConf);
        self::assertArrayHasKey(Environment::DATA_DIR, $checkConf);
        self::assertEquals($configDir . DIRECTORY_SEPARATOR . $envFile, $checkConf[Environment::ENV_FILE]);
        self::assertEquals($dataDir, $checkConf[Environment::DATA_DIR]);
        self::assertFileExists($configDir.DIRECTORY_SEPARATOR.$confFile);

        FileHelper::delete($configDir);
        FileHelper::delete($dataDir);
    }
}
