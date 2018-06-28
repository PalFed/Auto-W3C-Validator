<?php
/* Rename this file to validator.config.php */

/* Where are you hosting your instance of the W3C validator? */
define('ValidatorHost', 'localhost');
define('ValidatorPath', '/w3c-validator');
define('ValidatorPort', 80);
define('ValidatorUseSSL', false);

/* The snippet length is the number of characters of the HTML to show before and after an error.
 * A length of 30 will show 30 characters before and 30 characters after the error.
 * The snippet can be viewed by mousing over the error when it is displayed.
 */
define('SNIPPET_LENGTH', 30);