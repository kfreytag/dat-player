<?php

require_once('common/classes/context/Context.php');

class WebContext implements Context {

    private $client;
    private $resources;

    public function getClient()
    {
        if (!$this->client)
        {
            require_once('common/classes/context/WebClient.php');
            $this->client = new WebClient($this);
        }

        return $this->client;
    }

    public function setResources($resources)
    {
        $this->resources = $resources;
    }

    public function getResources()
    {
        if (!$this->resources)
        {
            require_once('common/classes/context/WebResources.php');
            $this->resources = new WebResources();
        }
        return $this->resources;
    }

}

?>