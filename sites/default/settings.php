<?php

/**
 * Load services definition file.
 */
$settings['container_yamls'][] = __DIR__ . '/services.yml';

/**
 * Include the Pantheon-specific settings file.
 *
 * n.b. The settings.pantheon.php file makes some changes
 *      that affect all envrionments that this site
 *      exists in.  Always include this file, even in
 *      a local development environment, to insure that
 *      the site settings remain consistent.
 */
include __DIR__ . "/settings.pantheon.php";

$secrets_file = $_SERVER['HOME'] . '/files/private/secrets.json';
if (file_exists($secrets_file)) {
  $secrets = json_decode(file_get_contents($secrets_file), 1);
  $databases['legacy']['default'] = array(
    'database' => $secrets['legacy_database'],
    'username' => $secrets['legacy_username'],
    'password' => $secrets['legacy_password'],
    'prefix' => '',
    'host' => $secrets['legacy_host'],
    'port' => $secrets['legacy_port'],
    'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
    'driver' => 'mysql',
  );
}

/**
 * If there is a local settings file, then include it
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}

