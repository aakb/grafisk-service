# Grafisk service bestilling

## Installation

```
composer install --no-dev
cd web/sites/default
```

Insert database configuration into `settings.local.php`, e.g.

```php
<?php

$databases['default']['default'] = [
  'database' => 'grafiskeopgaver',
  'username' => 'grafiskeopgaver',
  'password' => 'grafiskeopgaver',
  'prefix' => '',
  'host' => 'localhost',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
];

$settings['file_private_path'] = 'sites/default/private-files';
$config_directories[CONFIG_SYNC_DIRECTORY] = '../config/sync';
```

Install the site:

```sh
../../../vendor/bin/drush site-install minimal --config-dir=$PWD/../../../config/sync/
```

Go to `/admin/grafisk_service_order/messsages` and configure email settings.

Set up a cron job:

```
*/1 * * * * /usr/local/bin/drush --root="/home/www/grafisk_service_bestilling/htdocs/web" --uri="http://grafisk_service_bestilling.dev" cron > /dev/null 2>&1
```

Change paths to match your actual setup.

### Updating

```
composer install --no-dev
cd web/sites/default
../../../vendor/bin/drush --yes config-import
../../../vendor/bin/drush --yes updatedb
../../../vendor/bin/drush --yes locale-update
../../../vendor/bin/drush --yes cache-rebuild
```

## APIs

Uses harvest API. See /vagrant/grafisk_service_bestilling/htdocs/web/modules/custom/grafisk_service_order/README.md
