<?php

require_once('smarty/Smarty.php');

abstract class Resources
{
    /**
     * Return the Smarty object
     *
     * @return Smarty
     */
    public abstract function getSmarty();

    function __call($name, $args)
    {
        throw new Exception("Can't call Resources::$name");
    }
}
