<?php

require_once('../app/player/setup/set_paths.php');

require_once('player/functions/setup.php');
require_once('player/functions/format.php');
require_once('player/functions/utilities.php');
require_once('player/functions/log.php');


// Include the global WebContext object that provides access to
// all needed resources and applications.
require_once('common/classes/context/WebContext.php');
require_once('common/classes/context/WebResources.php');

// Set up context
$context = new WebContext();
$context->setResources(new WebResources());

// Now do common setup for all pages. Takes care of location
// redirects, cookies, etc.
include('player/setup/start.php');

done();