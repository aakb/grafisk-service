Grafisk service order
=====================

After installing this module, settings like these must be added to settings.php:

```
$config['grafisk_service_order.settings']['harvest']['api']['username'] = 'harvest@example.com';
$config['grafisk_service_order.settings']['harvest']['api']['password'] = 'changethis';
$config['grafisk_service_order.settings']['harvest']['api']['account'] = 'example';
```

A cron jobs takes care of creating orders in Harvest.
