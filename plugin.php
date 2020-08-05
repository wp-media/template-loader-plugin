<?php
/**
 * Plugin Name: WP Media | Template Loader plugin.
 * Description: A WordPress plugin to loading specific page templates regardless of theme.
 * Plugin URI: https://github.com/wp-media/template-loader-plugin
 * Version: 1.0.0
 * Author: WP Rocket PHP Engineering Team
 * Author URI: https://wp-rocket.me
 * License:    GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Copyright 2020 WP Media <support@wp-rocket.me>
 */

defined( 'ABSPATH' ) || die();

require_once 'functions.php';
require_once 'MetaBox.php';

add_action( 'plugins_loaded', 'wp_media_qa_templates' );
