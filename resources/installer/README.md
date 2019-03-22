# Automation for the installation of the tao

You can use this files as a template to provide your own `.json` file to install project.

### Interactive step by step
```
sudo -u www-data php artisan setup:environment
```

### Develop
```
sudo -u www-data php artisan setup:environment --develop
```

### Production
```
sudo -u www-data php artisan setup:environment production
```

### Your JSON file
```
sudo -u www-data php artisan setup:environment --config='/var/www/myconfig.json'
```

### To save current project configuration:
```
sudo -u www-data php artisan setup:environment --save
```
