<?php

require_once('common/classes/views/SmartyView.php');

class BaseViewController extends BaseController
{

    public function _postprocess($action)
    {
    }

    protected function hasRequiredQueryParameter($context, $parameterName, &$destinationVariable)
    {
        if ($this->emptyQueryParameter($context, $parameterName)) {
            return false;
        }
        $destinationVariable = $this->safeGet($context, $parameterName);
        return true;
    }

    protected function safeGet($context, $parameterName)
    {
        if ($this->emptyQueryParameter($context, $parameterName)) {
            return null;
        }
        $get = $context->getClient()->getParsedGet();
        return $get[$parameterName];
    }

    protected function emptyQueryParameter ($context, $parameterName)
    {
        $get = $context->getClient()->getParsedGet();
        if (!isset($get) || !isset($get[$parameterName]) || (empty($get[$parameterName]) && $get[$parameterName] != '0')) {
            return true;
        }
        return false;
    }

    protected function jsonRenderView ($view)
    {
        ob_start();
        $response['success'] = true;
        $html = $view->render();
        $response['contents'] = ob_get_contents();
        ob_clean();

        $this->echoJSON($response);

    }

    protected function jsonRenderTemplate($template)
    {
        $view = new SmartyView($this->context->getResources()->getSmarty(), $template);

        ob_start();
        $response['success'] = true;
        $html = $view->render();
        $response['contents'] = ob_get_contents();
        ob_clean();

        $this->echoJSON($response);
    }

    protected function locateTemplate (WebContext $context, $path)
    {
        $application = $context->getApplication();
        if ($application != null) {
            $testPath = $application->getName() . '/' . $path;
            if (file_exists($testPath)) {
                return $testPath;
            }
        }
        error_log('failed to find template');
    }

    protected function jsonError ($message)
    {
        $result['success'] = false;
        $result['message'] = $message;
        $this->echoJSON($result);
    }

    protected function echoJSON ($content)
    {
        // TODO - Fix this once PHP 5.4 has fixed
        // its support for gzencode

        $output = json_encode($content);

        if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE) {

            header("Content-Encoding: gzip");
            header("Content-Type: text/plain");

            try {
                $output = gzencode($output, 1, ZLIB_ENCODING_GZIP);
            } catch (Exception $e) {
                error_log($e->getMessage());
            }

        }

        echo $output;

    }

}
?>