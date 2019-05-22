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


use Illuminate\Support\Str;
use medcenter24\mcCore\App\Contract\Installer\InstallerParam;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Helpers\Arr;
use medcenter24\mcCore\App\Helpers\FileHelper;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvApiDebugParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvApiNameParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvApiPrefixParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvApiStrictParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvApiSubtypeParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvApiVersionParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvAppDebugParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvAppEnvParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvAppKeyParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvAppLogLevelParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvAppModeParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvAppUrlParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvBroadcastDriverParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvCacheDriverParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvCorsAllowOriginDirectorParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvCorsAllowOriginDoctorParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvCustomerNameParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvDbConnectionParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvDbDatabaseParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvDbHostParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvDbPasswordParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvDbPortParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvDbUserNameParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvDebugbarEnabledParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvDropboxBackupAppParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvDropboxBackupKeyParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvDropboxBackupRootParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvDropboxBackupSecretParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvDropboxBackupTokenParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvImageDriverParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvLogChannelParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvLogSlackWebhookUrlParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvMailDriverParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvMailEncryptionParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvMailHostParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvMailPasswordParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvMailPortParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvMailUsernameParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvPusherAppIdParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvPusherAppKeyParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvPusherAppSecretParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvQueueDriverParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvRedisHostParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvRedisPasswordParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvRedisPortParam;
use medcenter24\mcCore\App\Models\Installer\Params\Env\EnvSessionDriverParam;
use medcenter24\mcCore\App\Models\Installer\Params\Settings\AdminEmailParam;
use medcenter24\mcCore\App\Models\Installer\Params\Settings\AdminNameParam;
use medcenter24\mcCore\App\Models\Installer\Params\Settings\AdminPasswordParam;
use medcenter24\mcCore\App\Models\Installer\Params\Settings\DirectorDevHostParam;
use medcenter24\mcCore\App\Models\Installer\Params\Settings\DirectorDevProjectNameParam;
use medcenter24\mcCore\App\Models\Installer\Params\Settings\DirectorDoctorDevHostParam;
use medcenter24\mcCore\App\Models\Installer\Params\Settings\DirectorDoctorProdHostParam;
use medcenter24\mcCore\App\Models\Installer\Params\Settings\DirectorProdHostParam;
use medcenter24\mcCore\App\Models\Installer\Params\Settings\DirectorProdProjectNameParam;
use medcenter24\mcCore\App\Models\Installer\Params\Settings\DoctorDevHostParam;
use medcenter24\mcCore\App\Models\Installer\Params\Settings\DoctorProdHostParam;
use medcenter24\mcCore\App\Models\Installer\Params\Settings\SeedAuthorParam;
use medcenter24\mcCore\App\Models\Installer\Params\Settings\SeedIdentifierParam;
use medcenter24\mcCore\App\Models\Installer\Params\Settings\SeedVersionParam;
use medcenter24\mcCore\App\Models\Installer\Params\System\ConfigDirParam;
use medcenter24\mcCore\App\Models\Installer\Params\System\ConfigFilenameParam;
use medcenter24\mcCore\App\Models\Installer\Params\System\DataDirParam;
use medcenter24\mcCore\App\Models\Installer\Params\System\EnvFilenameParam;
use medcenter24\mcCore\App\Support\Core\ConfigurableInterface;

class JsonSeedReaderService
{

    public const PROP_SEED_IDENTIFIER = 'seed-id';
    public const PROP_SEED_AUTHOR = 'seed-author';
    public const PROP_SEED_VERSION = 'seed-version';
    public const PROP_DIRECTOR_DEV_HOST = 'director-dev-host';
    public const PROP_DOCTOR_DEV_HOST = 'doctor-dev-host';
    public const PROP_DOCTOR_PROD_HOST = 'doctor-prod-host';
    public const PROP_DIRECTOR_PROD_HOST = 'director-prod-host';
    public const PROP_DIRECTOR_DEV_PROJECT_NAME = 'director-dev-project-name';
    public const PROP_DIRECTOR_PROD_PROJECT_NAME = 'director-prod-project-name';
    public const PROP_DIRECTOR_DOCTOR_DEV_HOST = 'director-doctor-dev-host';
    public const PROP_DIRECTOR_DOCTOR_PROD_HOST = 'director-doctor-prod-host';

    /**
     * Data from the json
     * @var array
     */
    private $data = [];

    /**
     * @param string $path
     * @return array
     * @throws InconsistentDataException
     */
    public function read(string $path): array
    {
        $this->data = [];
        if (file_exists($path) && FileHelper::isReadable($path)) {
            $json = FileHelper::getContent($path);
            $data = json_decode($json, true);
            // replace ../ from the json file according to the json file location
            $data = $this->fillPaths($data, dirname($path));
            $this->checkAndCollect($data, $this->getJsonFileMap());
        }  else {
            throw new InconsistentDataException('Json file not found');
        }

        return $this->data;
    }

    /**
     * @param string $name
     * @return InstallerParam
     * @throws InconsistentDataException
     */
    public function get(string $name): InstallerParam
    {
        /** @var InstallerParam $param */
        foreach ($this->data as $param) {
            if ($param->getParamName() === $name) {
                return $param;
            }
        }

        throw new InconsistentDataException('Parameter with name ' . $name . ' not found');
    }

    private function fillPaths(array $data, $path): array
    {
        if (Arr::keysExists($data, ['configurations', 'global', 'config-path']) && Str::startsWith($data['configurations']['global']['config-path'], '..')) {
            $data['configurations']['global']['config-path'] = $path . '/' . $data['configurations']['global']['config-path'];
        }
        if (Arr::keysExists($data, ['configurations', 'global', 'data-path']) && Str::startsWith($data['configurations']['global']['data-path'], '..')) {
            $data['configurations']['global']['data-path'] = $path . '/' . $data['configurations']['global']['data-path'];
        }
        // rewrite sqlite storage path
        if (Arr::keysExists($data, ['configurations', 'core', 'db_database']) && Str::startsWith($data['configurations']['core']['db_database'], '..')) {
            $data['configurations']['core']['db_database'] = $path . '/' . $data['configurations']['core']['db_database'];
        }
        return $data;
    }

    /**
     * @param array|string $data
     * @param array|InstallerParam $map
     * @throws InconsistentDataException
     */
    private function checkAndCollect($data, $map): void
    {
        /**
         * @var string $key
         * @var string|InstallerParam|ConfigurableInterface $val
         */
        foreach ($map as $key => $val) {
            if ($val instanceof InstallerParam) {
                $val->setValue($data[$key]);
                if (!$val->isValid()) {
                    throw new InconsistentDataException('Incorrect parameter ' . $val->getParamName() . ' in ' . $key);
                }
                $this->data[] = $val;
            } elseif(!is_array($data) || !array_key_exists($key, $data)) {
                throw new InconsistentDataException(' Incorrect value in ' . $key);
            } else {
                $this->checkAndCollect($data[$key], $val);
            }
        }
    }

    private function getJsonFileMap(): array
    {
        return [
            'seed' => [
                'identifier' => new SeedIdentifierParam(),
                'author' => new SeedAuthorParam(),
                'version' => new SeedVersionParam(),
            ],
            'admin' => [
                'email' => new AdminEmailParam(),
                'password' => new AdminPasswordParam(),
                'name' => new AdminNameParam(),
            ],
            'configurations' => [
                'global' => [
                    'config-path' => new ConfigDirParam(),
                    'config-filename' => new ConfigFilenameParam(),
                    'env-filename' => new EnvFilenameParam(),
                    'data-path' => new DataDirParam,
                ],
                'core' => [
                    'api_debug' => new EnvApiDebugParam(),
                    'app_debug' => new EnvAppDebugParam(),
                    'api_name' => new EnvApiNameParam(),
                    'api_prefix' => new EnvApiPrefixParam(),
                    'api_strict' => new EnvApiStrictParam(),
                    'api_subtype' => new EnvApiSubtypeParam(),
                    'api_version' => new EnvApiVersionParam(),
                    'app_env' => new EnvAppEnvParam(),
                    'app_key' => new EnvAppKeyParam(),
                    'app_log_level' => new EnvAppLogLevelParam(),
                    'app_mode' => new EnvAppModeParam(),
                    'app_url' => new EnvAppUrlParam(),
                    'broadcast_driver' => new EnvBroadcastDriverParam(),
                    'cache_driver' => new EnvCacheDriverParam(),
                    'cors_allow_origin_director' => new EnvCorsAllowOriginDirectorParam(),
                    'cors_allow_origin_doctor' => new EnvCorsAllowOriginDoctorParam(),
                    'customer_name' => new EnvCustomerNameParam(),
                    'db_connection' => new EnvDbConnectionParam(),
                    'db_database' => new EnvDbDatabaseParam(),
                    'db_host' => new EnvDbHostParam(),
                    'db_port' => new EnvDbPortParam(),
                    'db_password' => new EnvDbPasswordParam(),
                    'db_username' => new EnvDbUserNameParam(),
                    'debugbar_enabled' => new EnvDebugbarEnabledParam(),
                    'dropbox_backup_app' => new EnvDropboxBackupAppParam(),
                    'dropbox_backup_key' => new EnvDropboxBackupKeyParam(),
                    'dropbox_backup_root' => new EnvDropboxBackupRootParam(),
                    'dropbox_backup_secret' => new EnvDropboxBackupSecretParam(),
                    'dropbox_backup_token' => new EnvDropboxBackupTokenParam(),
                    'image_driver' => new EnvImageDriverParam(),
                    'mail_driver' => new EnvMailDriverParam(),
                    'mail_encryption' => new EnvMailEncryptionParam(),
                    'mail_host' => new EnvMailHostParam(),
                    'mail_password' => new EnvMailPasswordParam(),
                    'mail_port' => new EnvMailPortParam(),
                    'mail_username' => new EnvMailUsernameParam(),
                    'pusher_app_id' => new EnvPusherAppIdParam(),
                    'pusher_app_key' => new EnvPusherAppKeyParam(),
                    'pusher_app_secret' => new EnvPusherAppSecretParam(),
                    'queue_driver' => new EnvQueueDriverParam(),
                    'redis_host' => new EnvRedisHostParam(),
                    'redis_password' => new EnvRedisPasswordParam(),
                    'redis_port' => new EnvRedisPortParam(),
                    'session_driver' => new EnvSessionDriverParam(),
                    'log_channel' => new EnvLogChannelParam(),
                    'slack_webhook_url' => new EnvLogSlackWebhookUrlParam(),
                ],
                'director-gui' => [
                    // static paths ../settings/guiDirector/environments/[environment.prod.ts|environment.ts]
                    'development' => [
                        'apiHost' => new DirectorDevHostParam(),
                        'projectName' => new DirectorDevProjectNameParam(),
                        'doctorLink' => new DirectorDoctorDevHostParam(),
                    ],
                    'production' => [
                        'apiHost' => new DirectorProdHostParam(),
                        'projectName' => new DirectorProdProjectNameParam(),
                        'doctorLink' => new DirectorDoctorProdHostParam(),
                    ],
                ],
                'doctor-gui' => [
                    // static paths ../settings/guiDoctor/environments/[development.js|productions.js]
                    'development' => [
                        'apiHost' => new DoctorDevHostParam(),
                    ],
                    'production' => [
                        'apiHost' => new DoctorProdHostParam(),
                    ],
                ],
            ]
        ];
    }
}
