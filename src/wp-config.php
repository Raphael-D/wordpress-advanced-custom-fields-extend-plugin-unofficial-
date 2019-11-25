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
define( 'DB_NAME', 'wp_dev_customfields' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'J/zfX1==MURUG_O+5OFMC6.`d5$/c3>q(I4l.EFMjM|$k4MBa+fM`yB^^_]&P@H>' );
define( 'SECURE_AUTH_KEY',  'F)_4.JU%e>*v c[(u< @]2ZoBr$Ss|<)yvd^{o2cUE+&BqVg6Qp5D}4-^.I=9w8S' );
define( 'LOGGED_IN_KEY',    'cS26K*Ggt]y/.JS/gUH*px;)!% 1A?VRef- <L}sv]ju__OG;;0I:`YB/+64:OdJ' );
define( 'NONCE_KEY',        '>WqXkjCvL~9WMr`h$D~#CBgr4=YmQ@@q@e]~:}SHJ85~3;65bWoz0/@e[cH6+AHa' );
define( 'AUTH_SALT',        'v{4f1v1^,P|$TM-~cBdlZNMQucP*wq3|Bt48ZX>:,|`JfnY5<2C_nVz6FTCw0g-3' );
define( 'SECURE_AUTH_SALT', 'Y7>yl>EjP-y,_I~2@+8tZt^T:$@#)dn6p,NP);9Ac+B+,$_5$gof{H5r?}#kOZc)' );
define( 'LOGGED_IN_SALT',   'envwmW26a6tK ms4JPh3BZ&j)dG:}GB[o7/`lt^dA]d{2j4MxTRbd?cobDZC|C!)' );
define( 'NONCE_SALT',       'sWa+6Q:L$Y.;w.JHP18a]i}S+SOilu9{>|htNy!qVpJP.BnU}V4QEgm,}sm2Erh&' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
