<?php

interface Client
{
    // IP address of the client at in the HTTP header
/*
    public function getHTTPClientIP();

    public function getUserAgent();
    public function getReferrer();
    public function isHTTPS();
*/
    public function getRequestURL();
    public function getQueryString();
    /*
    public function getScriptURL();

    public function isRobot();
    public function isSearchRobot();
    public function isSearchReferral();

    public function getHost();
    public function getScriptURI();
    public function getScriptURIWithQS();
*/
}
?>