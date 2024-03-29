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
declare(strict_types=1);

namespace medcenter24\mcCore\Tests\Feature\Console;

use medcenter24\mcCore\App\Services\Core\EnvironmentService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use medcenter24\mcCore\Tests\SamplePath;
use medcenter24\mcCore\Tests\TestCase;

class SetupEnvironmentCommandTest extends TestCase
{
    use DatabaseMigrations;
    use SamplePath;

    /*
     * it makes work test_installing_env()
     * but brakes tests
     * public function tearDown(): void
    {
        $_ENV['APP_CONFIG_PATH'] = null;
        try {
            parent::tearDown();
        }catch (\BadMethodCallException $e) {}
    }*/

    public function test_installing_generate_config(): void
    {
        EnvironmentService::terminate();
        $_ENV['APP_CONFIG_PATH'] = 'exception';
        $this->artisan('setup:environment')
            ->expectsQuestion('Are you sure want to install application with these parameters?', 'no')
            ->assertExitCode(0);
    }

    public function test_exception_on_the_wrong_config(): void
    {
        EnvironmentService::terminate();
        $_ENV['APP_CONFIG_PATH'] = 'exception';
        EnvironmentService::init(realpath(__DIR__ . '/samples/default.conf.php'));
        $this->artisan('setup:environment')
            ->expectsOutput('Environment already configured')
            ->assertExitCode(0);
    }

    public function test_install_on_the_installed(): void
    {
        EnvironmentService::terminate();
        $_ENV['APP_CONFIG_PATH'] = 'exception';
        EnvironmentService::init($this->getTopAppSamplePath() . '/settings/default.conf.php');
        $this->artisan('setup:environment')
            ->expectsOutput('Environment already configured')
            ->assertExitCode(0);
    }

    /*
     * it breaks all tests
     * needs to be reviewed later
     */
    /*public function test_installing_env(): void
    {
        $_ENV['APP_CONFIG_PATH'] = null;
        EnvironmentService::terminate();
        $configDir = __DIR__ . '/samples/sandbox/config';
        $confFile = 'generis.conf.php';
        $_ENV['APP_CONFIG_PATH'] = $configDir . '/' . $confFile;
        $envFile = '.env';
        $dataDir = __DIR__ . '/samples/sandbox/data';

        // clean test dir before the test
        FileHelper::delete($configDir);
        FileHelper::delete($dataDir);

        FileHelper::createDir($configDir);
        FileHelper::createDir($dataDir);

        $this->artisan('setup:environment', [
            '--' . InstallerService::PROP_CONFIG_DIR => $configDir,
            '--' . InstallerService::PROP_CONFIG_FILE_NAME => $confFile,
            '--' . InstallerService::PROP_ENV_FILE_NAME => $envFile,
            '--' . InstallerService::PROP_DATA_DIR => $dataDir,
            '--' . EnvironmentService::PROP_API_DEBUG => true,
            '--' . EnvironmentService::PROP_DB_CONNECTION => 'sqlite',
            '--' . EnvironmentService::PROP_DB_DATABASE => ':memory:',
        ])
            ->expectsQuestion('Are you sure want to install application with these parameters?', 'yes')
            ->expectsQuestion('Do you want to migrate DB?', 'yes')
            ->expectsQuestion('Do you really wish to run this command?', 'yes')
            ->expectsQuestion('Do you want to seed DB?', 'yes')
            ->expectsQuestion('Do you really wish to run this command?', 'yes')
            ->expectsQuestion('Do you want to create Super Admin?', 'yes')
            ->expectsQuestion('`` is not correct E - Mail, fix it please', 'yes')
            ->expectsQuestion('Enter new password', 'yes')
            ->expectsQuestion('Enter user name', 'yes')
            ->expectsQuestion('Do you wish to create this user?', 'yes')
            ->run();

        self::assertDirectoryExists($configDir);
        self::assertDirectoryExists($dataDir);
        self::assertFileExists($_ENV['APP_CONFIG_PATH']);
        $checkConf = @include $_ENV['APP_CONFIG_PATH'];
        self::assertArrayHasKey(Environment::ENV_FILE, $checkConf);
        self::assertArrayHasKey(Environment::DATA_DIR, $checkConf);
        self::assertEquals($configDir . DIRECTORY_SEPARATOR . $envFile, $checkConf[Environment::ENV_FILE]);
        self::assertEquals($dataDir, $checkConf[Environment::DATA_DIR]);
        self::assertFileExists($configDir.DIRECTORY_SEPARATOR.$confFile);

        // clean dir when test completed
        FileHelper::delete($configDir);
        FileHelper::delete($dataDir);
    }*/
}
