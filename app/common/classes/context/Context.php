<?php

interface Context
{
    /**
     * Return the Client object
     *
     * @return Client
     */
    public function getClient();

    /**
     * Return the Resources object
     *
     * @return Resources
     */
    public function getResources();
}

?>