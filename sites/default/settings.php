<?php
$config_directories = array(
	CONFIG_SYNC_DIRECTORY => '/var/tmp',
);

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
 * If there is a local settings file, then include it
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}

$settings['simplesamlphp_dir'] = '/var/www/html/simplesamlphp';
$settings['file_private_path'] = 'sites/default/files/private';

// SimpleSAMLphp_auth module settings
$config['simplesamlphp_auth.settings'] = [
 // Basic settings.
    'activate'                => TRUE, // Enable or Disable SAML login.
    'auth_source'             => 'default-sp',
    'login_link_display_name' => 'Login with your SSO account',
    'register_users'          => TRUE,
    'debug'                   => FALSE,
 // Local authentication.
    'allow' => array(
      'default_login'         => TRUE,
      'set_drupal_pwd'        => TRUE,
      'default_login_users'   => '',
      'default_login_roles'   => array(
        'authenticated' => FALSE,
        'administrator' => 'administrator',
      ),
    ),
    'logout_goto_url'         => '',
 // User info and syncing.
 // 'unique_id' the value which is unique in the saml response coming from IDP.
    'unique_id'               => 'mail',
    'user_name'               => 'username',
    'mail_attr'               => 'mail',
    'sync' => array(
        'mail'      => TRUE,
        'user_name' => TRUE,
    ),
];
