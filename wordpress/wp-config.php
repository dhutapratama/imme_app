<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'zadmin_immewp');

/** MySQL database username */
define('DB_USER', 'z');

/** MySQL database password */
define('DB_PASSWORD', 'z');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'Rxt0u~RkZ.R%5c)Nmes>!kyP6nmOZt:|rPU@w)%dVmC8~pbE`f/Fm`W_ GaGeeoq');
define('SECURE_AUTH_KEY',  'VK~(PY).M72itRkkgd8Z#rF!bbJl k!P2rJU#<7K=gluPLk^>6mC1Z#/pw?kT*v]');
define('LOGGED_IN_KEY',    'B;~Boz9.S}f>SbQ5od{L.KHl[R,7CQ%$s~/N%Q_8WbHeDBwb:L}[3j)4z0><L9ym');
define('NONCE_KEY',        '--&Yx#~5T8?eb=jyz9k0 ?nysdKe6 ,ZD<*]I6gkd>&-T0Iqie?zi)Z>7zOPSg.2');
define('AUTH_SALT',        '[lK4cyff5P+Cq|3b fZJA,n2)Q0p_l^Mhyl%)Ik%Y(ykuJ47Gid_fJ}LV:*tC<_V');
define('SECURE_AUTH_SALT', ' c?RzXmroI/Gu1i.ik7x?.H+WQ`(ebHLRb<? nz$Nf{Z7<=!!L&Z}nMrDVY4-8Ah');
define('LOGGED_IN_SALT',   'ac2Ac5sx>!S~UEXj0@CZt>-cz,o]-89D ]TD5Qj-lE:CZ*kp#E^V1X*x57?]{clp');
define('NONCE_SALT',       'X|`y(/PzK0x]X~|7P/@%_Z5e eMIgTfbm_c]pVJyTz&`VIK8),cO[!]4NOp$MWjE');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
