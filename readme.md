[![Build Status](https://travis-ci.org/your/repo.svg?branch=master)](https://travis-ci.com/medcenter24/mc-core)

# CRM My Doctors

CRM system that provide opportunity to store, aggregate and automatize all business
processes.

The system is intended for companies which work with assistants and are engaged in
providing medical insurance services.

## Technical documentation
Description of the features and tools for the application.

### ONLY_API
- Parameter `ONLY_API` passed as a server parameter will be interpreted as configuration is used as a API only.
 It means that with that parameter all requests will be proceed by the `router/api.php` 
  
### Telegram settings
To generate new cert: `openssl req -x509 -newkey rsa:4096 -keyout key.pem -out cert.pem -days 3650 -nodes`

# Installation

We created artisan command to make it simple: `artisan setup:environment`

## Quick local installation

But you need to take into an account that to use the local Environment you need to do `composer install` in the mcCore folder 
```
php artisan setup:environment \
    --API_DEBUG=true \
    --APP_DEBUG=true \
    --APP_ENV=local \
    --APP_LOG_LEVEL=debug \
    --DB_CONNECTION=sqlite \
    --DB_DATABASE=/private/var/www/sandbox/projects/medcenter24/develop/mcCore/database/db.sqlite \
    --DEBUGBAR_ENABLED=true
```