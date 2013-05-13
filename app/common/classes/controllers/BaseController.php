<?php

require_once('common/classes/controllers/exceptions/HTTP404Exception.php');
require_once('common/classes/controllers/exceptions/HTTP503Exception.php');
require_once('common/classes/services/BaseService.php');

class BaseController
{

    /**
     * @var WebContext
     */
    protected $context;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $urlPath;

    /**
     * @var string
     */
    protected $view;

    public final function __construct(Context $context, $action = 'index', $urlPath = '')
    {
        $this->context = $context;
        $this->action = $action;
        $this->urlPath = $urlPath;
        $db = new PDO('sqlite:' . SQLITE_FILE_LOCATION);
        BaseService::init($db);
    }

    public final static function locateController(array $pathArray)
    {
        $controllerRaw = $pathArray[count($pathArray) - 1];
        $controllerClass = ucfirst(camelize($controllerRaw)) . 'Controller';

        $urlPath = '/' . implode($pathArray, '/');

        /*************************************
         * Support Application-Specific
         * Controllers
         *************************************/

        $applicationControllerPath = APPLICATION_HOME . 'player/classes/controllers';

        $applicationControllerPath .= $urlPath . '/' . $controllerClass . '.php';

        if (file_exists($applicationControllerPath))
        {
            return array($urlPath, $applicationControllerPath, $controllerClass);
        }

        /*************************************
         * Fallback to common Controllers
         *************************************/

        $controllerFilePath = APPLICATION_HOME . 'common/classes/controllers';
        $controllerFilePath .= $urlPath . '/' . $controllerClass . '.php';

        if (file_exists($controllerFilePath))
        {

            return array($urlPath, $controllerFilePath, $controllerClass);
        }
        else
        {
            $pathArray = array_splice($pathArray, 0, count($pathArray) - 1);
            if (empty($pathArray))
            {
                return NULL;
            }
            else
            {
                return self::locateController($pathArray);
            }
        }
    }

    public final static function dispatch(Context $context)
    {
        /** @var $client Client */
        $client = $context->getClient();
        $pathString = $client->getRequestURL();
        if ($pathString == "/")
        {
            $pathString = '/' . getenv('HOME_CONTROLLER');
        }

        $pathArray = explode('/', trim($pathString, '/'));
        list($urlPath, $controllerFile, $controllerClass) = self::locateController($pathArray);


        // We have to correct the action, id, etc based on the controller path
        //	IE: in url /grid/admin/realogy/client_listing/show/
        //		If ClientListingController exists in the path,
        //			then $url->getAction() should be show()

        $action = null;

        if ($urlPath)
        {
            $action = substr($pathString, strlen($urlPath) + 1);
        }

        if (file_exists($controllerFile))
        {
            require_once($controllerFile);

            $reflective_controller = new ReflectionClass($controllerClass);

            if (!$action)
            {
                $action = 'index';
            }

            $controller = new $controllerClass($context, $action, $urlPath);

            if ($reflective_controller->hasMethod('_filehandler'))
            {
                if ($controller->_filehandler($action))
                {
                    return TRUE;
                }
            }

            $varAction = variablize($action);

            if ($varAction === 'new') // Hack since 'new' is a reserved word and can not be used as a function name
            {
                $varAction = '_new';
            }
            if ($reflective_controller->hasMethod($varAction))
            {
                try
                {
                    // preprocesses for controllers
                    if ($reflective_controller->hasMethod('_preprocess'))
                    {
                        $controller->_preprocess($varAction);
                    }

//					log_msg('VBS', "BaseController: invoking {$controllerClass}->{$action}");
                    $controller->$varAction();

                    // postprocesses for controllers
                    if ($reflective_controller->hasMethod('_postprocess'))
                    {
                        $controller->_postprocess($varAction);
                    }

                    return TRUE;
                }
                catch (HTTP404Exception $ex)
                {
                    if ($reflective_controller->hasMethod('_handlePageNotFound'))
                        $controller->_handlePageNotFound($varAction, $ex);
                    else
                        throw $ex;
                }
                catch (HTTP503Exception $ex)
                {
                    if ($reflective_controller->hasMethod('_handleServiceTooBusy'))
                        $controller->_handleServiceTooBusy($varAction, $ex);
                    else
                        throw $ex;
                }
                catch (Exception $ex)
                {
                    if ($reflective_controller->hasMethod('_handleUnexpectedException'))
                        $controller->_handleUnexpectedException($varAction, $ex);
                    else
                        throw $ex;
                }

                return true;
            }
            else if ($reflective_controller->hasMethod('_failover'))
            {
                log_msg("DBG", "BaseController failing over on action {$action}");
                return $controller->_failover($varAction);
            }
            else
            {
                log_msg('ERR', "BaseController: $controllerClass::$varAction does not exist and the controller does not provide a failover handler.");
            }
        }

        return false;
    }
/*
    public final static function exit404(Context $context)
    {
        error_log('404');
        echo '404';
    }

    public final static function exit503(Context $context)
    {
        error_log('503');
        echo '503';
    }
*/
}