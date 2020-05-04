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

namespace medcenter24\mcCore\App\Contract\General;


interface Environment
{
    public const ENV_FILE = 'env';
    public const DATA_DIR = 'data';

    public const PROP_CUSTOMER_NAME = 'CUSTOMER_NAME';
    public const PROP_APP_ENV = 'APP_ENV';
    public const PROP_APP_KEY = 'APP_KEY';
    public const PROP_APP_DEBUG = 'APP_DEBUG';
    public const PROP_APP_LOG_LEVEL = 'APP_LOG_LEVEL';
    public const PROP_APP_URL = 'APP_URL';
    // todo to move it from the nginx config
    public const PROP_APP_MODE = 'APP_MODE';
    public const PROP_DB_CONNECTION = 'DB_CONNECTION';
    public const PROP_DB_DATABASE = 'DB_DATABASE';
    public const PROP_DB_HOST = 'DB_HOST';
    public const PROP_DB_PORT = 'DB_PORT';
    public const PROP_DB_USERNAME = 'DB_USERNAME';
    public const PROP_DB_PASSWORD = 'DB_PASSWORD';
    public const PROP_BROADCAST_DRIVER = 'BROADCAST_DRIVER';
    public const PROP_CACHE_DRIVER = 'CACHE_DRIVER';
    public const PROP_SESSION_DRIVER = 'SESSION_DRIVER';
    public const PROP_QUEUE_DRIVER = 'QUEUE_DRIVER';
    public const PROP_REDIS_HOST = 'REDIS_HOST';
    public const PROP_REDIS_PASSWORD = 'REDIS_PASSWORD';
    public const PROP_REDIS_PORT = 'REDIS_PORT';
    public const PROP_MAIL_DRIVER = 'MAIL_DRIVER';
    public const PROP_MAIL_HOST = 'MAIL_HOST';
    public const PROP_MAIL_PORT = 'MAIL_PORT';
    public const PROP_MAIL_USERNAME = 'MAIL_USERNAME';
    public const PROP_MAIL_PASSWORD = 'MAIL_PASSWORD';
    public const PROP_MAIL_ENCRYPTION = 'MAIL_ENCRYPTION';
    public const PROP_PUSHER_APP_ID = 'PUSHER_APP_ID';
    public const PROP_PUSHER_APP_KEY = 'PUSHER_APP_KEY';
    public const PROP_PUSHER_APP_SECRET = 'PUSHER_APP_SECRET';
    public const PROP_API_SUBTYPE = 'API_SUBTYPE';
    public const PROP_API_PREFIX = 'API_PREFIX';
    public const PROP_API_VERSION = 'API_VERSION';
    public const PROP_API_NAME = 'API_NAME';
    public const PROP_API_STRICT = 'API_STRICT';
    public const PROP_API_DEBUG = 'API_DEBUG';
    public const PROP_CORS_ALLOW_ORIGIN_DIRECTOR = 'CORS_ALLOW_ORIGIN_DIRECTOR';
    public const PROP_CORS_ALLOW_ORIGIN_DOCTOR = 'CORS_ALLOW_ORIGIN_DOCTOR';
    public const PROP_IMAGE_DRIVER = 'IMAGE_DRIVER';
    public const PROP_DROPBOX_BACKUP_TOKEN = 'DROPBOX_BACKUP_TOKEN';
    public const PROP_DROPBOX_BACKUP_KEY = 'DROPBOX_BACKUP_KEY';
    public const PROP_DROPBOX_BACKUP_SECRET = 'DROPBOX_BACKUP_SECRET';
    public const PROP_DROPBOX_BACKUP_APP = 'DROPBOX_BACKUP_APP';
    public const PROP_DROPBOX_BACKUP_ROOT = 'DROPBOX_BACKUP_ROOT';
    public const PROP_DEBUGBAR_ENABLED = 'DEBUGBAR_ENABLED';
    public const PROP_LOG_CHANNEL = 'LOG_CHANNEL';
    public const PROP_LOG_SLACK_WEBHOOK_URL = 'LOG_SLACK_WEBHOOK_URL';
    public const PROP_JWT_BLACKLIST_GRACE_PERIOD = 'JWT_BLACKLIST_GRACE_PERIOD';
}
