<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

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
define('DB_NAME', 'db_psaa_wp');

/** MySQL database username */
define('DB_USER', 'psaa');

/** MySQL database password */
define('DB_PASSWORD', 'Bismillah1');

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
define('AUTH_KEY',         'jbdql8gflgvgfz3vired07njbuf1pksvijzakm97gowo7jwot3dz4sgyi6c5qxg5');
define('SECURE_AUTH_KEY',  'cezaa56ggh1bm8bttanwabhxixa1lpohwzqmwwu8a7aiulstxssrcq7tqhbg2prr');
define('LOGGED_IN_KEY',    'jf7fwue3gpfqjhpikyyfira7ebco8apn4cu1cx6ciqfjpckalihg94l1973gidsc');
define('NONCE_KEY',        'tydn3m4dqkmgfxaks906ncwatdu0cqjl0m8qy8qlcarmiud1amvte6e6bzut4ezf');
define('AUTH_SALT',        'i206ql8ba8rk86wuhlezznehurh8iljmcsxankbosxuxzmzswt3nungvfgboaqbc');
define('SECURE_AUTH_SALT', 'v8nlyohh3pyavneml70vp1ad7tiakh5laheozwxuejd0ghxkutfbfas1xkshgxsb');
define('LOGGED_IN_SALT',   'lh8owqok3klemvtmlod2iq00wy9cymsdisq0coeblyadqylwf4p3mqgf3vrnpyht');
define('NONCE_SALT',       'oitqumo8afxjr8ecwlgicllykiasuuynfutfaqaxejqr3ck80agyiibvsp4qbybb');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp4n_';

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
