<?php
define( 'WP_CACHE', true /* Modified by NitroPack */ );

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

 * @link https://wordpress.org/documentation/article/editing-wp-config-php/

 *

 * @package WordPress

 */

define( 'DB_NAME', 'kidsrdb' );

/** Database username */
define( 'DB_USER', 'kidsruser' );

/** Database password */
define( 'DB_PASSWORD', '=nL~-EgYuZOR' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );




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

define('AUTH_KEY',         'J zZk#I?ng;`Gts@CQ2C4[dHvjSd.rCpm-5q7OyP?Yq`L8+[Txcp)h AoS8gn{1V');
define('SECURE_AUTH_KEY',  'a@&nCO>8s+uJ)yMR+OuHnBmw`/;?AZ2%1xl?3AP`fmDKz|bO_t_Ala!k+Uw2t#!s');
define('LOGGED_IN_KEY',    'k|W|Y/Q7c53TVkSWtY@R$Hx{woqz~k[V^z[{UBHY-uR]u)50+`{27X%mj`R>1Je+');
define('NONCE_KEY',        'N^Ei~hg{|zd@Wv7i3|lpF^@Qmnf=PS:jOibPy&9heG>ooI+zTdi2BkcPk#*u5F(p');
define('AUTH_SALT',        'd]>dm|_y6^c010y]zjARYjiu ( |$[c5)F%2@+:/.m;)K=(>x1ihoz:tK6Nin|(4');
define('SECURE_AUTH_SALT', 'zs]5xfA#(BWlVfwg90+&~o+8E[z?*6UWa $JF07NF} ~Zq!NSF+EBPy8XGGykpPk');
define('LOGGED_IN_SALT',   'li*e!NVu hrwRBK,,Do*b(KL.P|.PA460)b3CkkDpC+KS,`nHFvtRr%<XqnPH-s8');
define('NONCE_SALT',       'F6[cou+Pn-+X-:RP}b-tCRp%lN`7GK|o/-^S}|VU 0/p*7,}+ew6||8+{^2H_p#L');


define('WP_MEMORY_LIMIT', '256M');



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

 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/

 */

define( 'WP_DEBUG', false );



/* Add any custom values between this line and the "stop editing" line. */







/* That's all, stop editing! Happy publishing. */



/** Absolute path to the WordPress directory. */

if ( ! defined( 'ABSPATH' ) ) {

	define( 'ABSPATH', __DIR__ . '/' );

}



/** Sets up WordPress vars and included files. */

require_once ABSPATH . 'wp-settings.php';


define('DISALLOW_FILE_EDIT', true);
define('DISALLOW_FILE_MODS', true);