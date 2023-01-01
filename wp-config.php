<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */

require_once __DIR__."/vendor/autoload.php";
Dotenv\Dotenv::createImmutable(__DIR__)->load();

define( 'DB_NAME', $_ENV["WORDPRESS_DB_NAME"] );

/** Database username */
define( 'DB_USER', $_ENV["WORDPRESS_DB_USER"]  );

/** Database password */
define( 'DB_PASSWORD', $_ENV["WORDPRESS_DB_PASSWORD"] );

/** Database hostname */
define( 'DB_HOST', $_ENV["WORDPRESS_DB_HOST"] );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', $_ENV["DB_CHARSET"] );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', $_ENV["DB_COLLATE"] );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'Bp20Y3tW7hw]`o#6ETr*r|]&{/ty5@~2c@OW^^T<[EW+6s255s^#?bM{*;dqm/xC');
define('SECURE_AUTH_KEY',  'N3Io+n+` 3!mtaX:<QJe`5SU~2.jGeox(Yqv/XdP#}9@3NKe]|CE8i|$}I_x^LEK');
define('LOGGED_IN_KEY',    'e|6/h =AL|upfROlQ!/@PK(60(BNP mo4=X9~6WAe|/3PwZoH?C:~-wJC!Su-Lni');
define('NONCE_KEY',        '+Zd0`w,K[$91L>a8i6H! vf6;Lh^O#4c*@1!Vhux-u]3XP9LnQG^sM+Qw[)5,<r%');
define('AUTH_SALT',        '@O`Cf7^{vZy<K**x{C8|ti77xp58&+ogFq+X-&{lqO,4*IcshQ<,Z:V5JOvAn.F[');
define('SECURE_AUTH_SALT', 'IHrUQ||iAvDfb9#tuxPsPhnWNs28R)04e6R.E_cz77,2t^xHqaI?<%V]pM$da0A2');
define('LOGGED_IN_SALT',   'eAl%w*a_S}(($Vmp|*>NZm][s;NN1P`0~N=MulUTd|-C6@2}6+fo`#oCN#Yv2*hV');
define('NONCE_SALT',       'P+kw2&-:)1>$`K)=^a3zx*Q|a;9~Q5ke`607|V GCd/yx%5,!bl01>54G13}i?HZ');

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', True );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
