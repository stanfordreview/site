<?php

/** 
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information by
 * visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */
define('WP_MEMORY_LIMIT', '90M');

 // Enable WP_DEBUG mode
define('WP_DEBUG', true);

define('WP_DEBUG_LOG', true);

// Disable display of errors and warnings 
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors',0);

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'stanfordreviewmain');

/** MySQL database username */
define('DB_USER', 'tsrmain');

/** MySQL database password */
define('DB_PASSWORD', 'fsd9237sdtafhhs344s');

/** MySQL hostname */
define('DB_HOST', 'mysql.cardinalpoll.com');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',        'LFd>y~UT 0BHiqx{{.Zz(PP7XYB}9PiQ7>/Q0.}J5wo 7haG@B5|a~KBB1=M~~ij');
define('SECURE_AUTH_KEY', 'wMJA@ww?/ 9,l~mByO4J~xU_B^7cOXU{ y>:AN-o~|<rbb]K_&3Qf]%ikw/*5*W~');
define('LOGGED_IN_KEY',   '$TRUh5t(lgu+qfzx|4$Tq#f$9>.jngF3MWi+%8YD|lo$I&~ns$mahnB3KsY&M]Bt');
define('NONCE_KEY',       '%QeA6IF@X`?j> Q17nxc5m}<-c~<0!mV4|:d$;%U!|f6XS^,L*]^|4x(#L%R jkX');
/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress.  A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de.mo to wp-content/languages and set WPLANG to 'de' to enable German
 * language support.
 */
define ('WPLANG', '');

/* That's all, stop editing! Happy blogging. */

/** WordPress absolute path to the Wordpress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
