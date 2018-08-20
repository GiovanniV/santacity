<?php

$settings['install_profile'] = 'standard';
ini_set("pcre.backtrack_limit", 100000000000);
ini_set("pcre.recursion_limit", 10000000000);
$settings['big_pipe_override_enabled'] = TRUE;
ini_set('max_input_vars', -1);

$settings['twig_debug'] = TRUE;

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

/**
 * If there is a local settings file, then include it
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}