<?php
define('DOCUMENT_ROOT', dirname($_SERVER['DOCUMENT_ROOT']));
define('APPLICATION_HOME', DOCUMENT_ROOT . '/app/');
define('COMMON_TEMPLATE_DIR', APPLICATION_HOME . 'common/templates/');
define('APPLICATION_TEMPLATE_DIR', APPLICATION_HOME . 'player/templates/');
define('SMARTY_DIR', DOCUMENT_ROOT . '/lib/smarty/');
define('SQLITE_FILE_LOCATION', getenv('SQLITE_FILE_LOCATION'));

$paths = array(
    APPLICATION_HOME,
    DOCUMENT_ROOT . '/lib/',
    '.'
);

ini_set('include_path', implode(":", $paths));
