<?php
/**
 * Add development service settings. 
 */
if (file_exists(__DIR__ . '/services.local.yml')) {
  $settings['container_yamls'][] = __DIR__ . '/services.local.yml';
}


/**
 * Disable CSS and JS aggregation.
 */
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
$settings['skip_permissions_hardening'] = TRUE;


$config['grafisk_service_order.settings']['harvest']['api']['username'] = 'harvest@example.com';
$config['grafisk_service_order.settings']['harvest']['api']['password'] = 'changethis';
$config['grafisk_service_order.settings']['harvest']['api']['account'] = 'example';
 
$settings['file_private_path'] = 'sites/default/private-files';

/**
 * Set Hash salt value
 */
$settings['hash_salt'] = '1234567890';


/**
 * Set local db
 */
$databases['default']['default'] = array (
  'database' => 'db',
  'username' => 'root',
  'password' => 'vagrant',
  'prefix' => '',
  'host' => 'localhost',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);


/**
 * Set sync path
 */
$config_directories['sync'] = '/vagrant/htdocs/config/sync';
