<?php

require_once('player/functions/sqlite3.php');

/**
 * Created by JetBrains PhpStorm.
 * User: kurt
 * Date: 4/30/12
 * Time: 4:35 PM
 * To change this template use File | Settings | File Templates.
 */
class BaseService
{

    public static $db;

    public static function init ($db) {
        self::$db = $db;
    }

}
