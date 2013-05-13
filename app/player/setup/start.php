<?php

require_once("player/functions/http.php");
require_once("common/classes/controllers/BaseController.php");

// Setup the Context so we have all the info
// we need to determine how to handle the request
setup($context);

// Try to dispatch the request

try
{
    BaseController::dispatch($context);
    done();
}
catch (HTTP404Exception $e)
{
    BaseController::exit404($context);
}
catch (HTTP503Exception $e)
{
    BaseController::exit503($context);
}

?>