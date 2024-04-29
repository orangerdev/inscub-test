<?php

/*
* Plugin Name: Incsub Test - Ridwan Arifandi
* Description: This is a test plugin for Incsub
* Version: 1.0
* Author: Ridwan Arifandi
* Author URI: https://ridwan-arifandi.com
*/

define('INCSUB_TEST_PATH', plugin_dir_path(__FILE__));
define('INCSUB_TEST_URL',  plugin_dir_url(__FILE__));
define('INCSUB_TABLE',     'incsub_test');

/**
 * Create a new table when the plugin is activated
 * @since   1.0.0
 * @author  Ridwan Arifandi
 */
register_activation_hook(__FILE__, 'activate_incsub_test');

function activate_incsub_test()
{
  global $wpdb;

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

  $charset_collate = $wpdb->get_charset_collate();

  $table = $wpdb->prefix . INCSUB_TABLE;

  dbDelta("CREATE TABLE IF NOT EXISTS `$table` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `phone` varchar(255) NOT NULL,
    `address` text NOT NULL,
    `created_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
  ) $charset_collate");
};

require_once INCSUB_TEST_PATH . 'inc/main.php';

$main = new \IncsubTest\Main();
