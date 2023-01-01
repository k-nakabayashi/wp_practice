<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */


define('MY_APP', __DIR__ . "/myapp/");

require_once './vendor/autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__)->load();


// HACK:  恐らく不要
// ini_set('session.cookie_domain', $_ENV["SITE_DOMAIN"].":".$_ENV["WP_PORT"]);
ini_set('session.cookie_domain', $_ENV["SITE_DOMAIN"].":".$_ENV["API_PORT"]);
session_start();


define( 'WP_USE_THEMES', true );

// /** Loads the WordPress Environment and Template */
require __DIR__ . '/wp-blog-header.php';
