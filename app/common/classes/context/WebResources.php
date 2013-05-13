<?php

require_once('common/classes/context/Resources.php');

class WebResources extends Resources
{

    private $logger;
    private $smarty;

    function setLogger (Logger $logger)
    {
        $this->logger = $logger;
    }

    function getLogger ()
    {
        if (!$this->logger)
        {
            require_once('common/classes/log/BasicLogger.php');
            $this->logger = new BasicLogger();
        }
        return $this->logger;
    }

    function getSmarty()
    {
        if (!$this->smarty)
        {
            require_once('smarty/Smarty.php');
            $this->smarty = new Smarty();
            $this->smarty->setTemplateDir('app/templates');
            $this->smarty->setCompileDir('/tmp/smarty/templates_c');
            $this->smarty->setCacheDir('/tmp/smarty/cache');
        }
        return $this->smarty;
    }

}