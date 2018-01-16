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
define('DB_NAME', 'wordpressDB');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         '|58Kc<L7C9Qa~A=>gtqTv9!etAIZ?BW(1V(f`8CqL:j)T|[c:Yq*AhoYhSw&[7oe');
define('SECURE_AUTH_KEY',  'o9qdX50[U.x)>oyNV0%sx29Y6EVF~t($#,M>R+yWA0 Csse^0diYWi^yfU|X!:P<');
define('LOGGED_IN_KEY',    '{H,lEoncy?3}8jnai|pW O+IgO:rpBK g7^+4osxA2`!kWEG4#5L2i)cU@])W!_z');
define('NONCE_KEY',        ')+A$J;R2?9(?go5Z raCw:6aL^]WU=g+nyo0bSCz:=5 @t5hCi1/nk6._4-Q+y*l');
define('AUTH_SALT',        ';5N2<i7$Do?=~C32?xu@cZ <)w35%@1;%&H+C_!,o$r JX]VFC,4WMb3E,h.!r>|');
define('SECURE_AUTH_SALT', '&ah SzPQ1SMH?1~~S?zfWz^96~{lXo=<D$+LK|Ugy`r&SN%m4=AE-tuR~gT%f31k');
define('LOGGED_IN_SALT',   '{iFSjR9p5AoZ~&];VB-|HCO]:%|6|Ua-Rg~OiC2G:$v]Xk.!iF~%Ab9W$;D.q<TN');
define('NONCE_SALT',       'P2WRF(]s}GZa_R2O)X{I^?N|6|MAxeDH0Nuo%h3jb%ZQwe=h)DQ]Fr,$OHx7[r{#');
define( 'WP_MEMORY_LIMIT', '256M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_1';

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
