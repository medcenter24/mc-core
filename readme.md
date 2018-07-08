#CRM My Doctors

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

# v2.0 Changes:
`php artisan storage:link`