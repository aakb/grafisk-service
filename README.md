# Grafisk service bestilling

## Installation

```
composer install
cd web
drush config-import
```

Add these line to `settings.php`:

```
$settings['file_private_path'] = 'sites/default/private-files';
$config_directories[CONFIG_SYNC_DIRECTORY] = '../config/sync';
```

Go to `/admin/grafisk_service_order/messsages` and configure email settings.


## APIs
Uses harvest API. See /vagrant/grafisk_service_bestilling/htdocs/web/modules/custom/grafisk_service_order/README.md
