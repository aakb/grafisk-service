# Grafisk service bestilling

## Installation

```
composer install --no-dev
cd web/sites/default
drush --yes config-import
drush locale-update
drush cache-rebuild
```

Add these line to `settings.php`:

```
$settings['file_private_path'] = 'sites/default/private-files';
$config_directories[CONFIG_SYNC_DIRECTORY] = '../config/sync';
```

Go to `/admin/grafisk_service_order/messsages` and configure email settings.

Set up a cron job:

```
*/1 * * * * /usr/local/bin/drush --root="/home/www/grafisk_service_bestilling/htdocs/web" --uri="http://grafisk_service_bestilling.dev" cron > /dev/null 2>&1
```

Change paths to match your actual setup.

## APIs
Uses harvest API. See /vagrant/grafisk_service_bestilling/htdocs/web/modules/custom/grafisk_service_order/README.md
