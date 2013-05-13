<?php

class WebClient
{
    private $context;
    private $requestURL;
    private $scriptURIWithQS;
    private $queryStringArray;

    function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function getCookie($name)
    {
        if (isset($_COOKIE[$name]))
            return $_COOKIE[$name];

        return NULL;
    }

    public function setCookie($name, $value, $expiration, $cookieDomain = NULL)
    {
        /*

               if ($cookieDomain === NULL)
                   $cookieDomain = cookie_domain($this->context);
        */
        /*			print_r_pre($name);
              print_r_pre($value);
              print_r_pre($expiration);*/
//		setcookie($name, $value, $expiration, '/', $cookieDomain);
        setcookie($name, $value, $expiration, '/');
        $_COOKIE[$name] = $value;
        // log_msg('DBG', "Set cookie $name = $value on $cookieDomain");
    }

    function unsetCookie($cookieName, $cookieDomain = NULL)
    {
        /*
          if (!$cookieDomain)
              $cookieDomain = cookie_domain($this->context);
          */

        //	setcookie($cookieName, '', 0, '/', $cookieDomain);
        setcookie($cookieName, '', 0, '/');
    }

    public function getRemoteAddress()
    {
        if (isset($_SERVER['REMOTE_ADDR']))
            return $_SERVER['REMOTE_ADDR'];

        return NULL;
    }

    public function getHTTPClientIP()
    {
        if (isset($_SERVER["HTTP_CLIENT_IP"]))
            return $_SERVER["HTTP_CLIENT_IP"];

        return NULL;
    }

    public function getUserAgent()
    {
        if (get_debug('test_client') && isset($_GET['user_agent']))
            return $_GET['user_agent'];

        if (isset($_SERVER['HTTP_USER_AGENT']))
            return $_SERVER['HTTP_USER_AGENT'];

        return NULL;
    }

    public function getReferrer()
    {
        if (get_debug('test_client') && isset($_GET['referrer']))
            return $_GET['referrer'];

        if (isset($_SERVER['HTTP_REFERER']))
            return $_SERVER['HTTP_REFERER'];

        return NULL;
    }

    public function isHTTPS()
    {
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
    }

    // http://www.dat.com/login/?username=me => http://www.dat.com/login/
    public function getScriptURI()
    {
        if (isset($_SERVER['SCRIPT_URI']))
            return $_SERVER['SCRIPT_URI'];

        return NULL;
    }

    public function getScriptURIWithQS()
    {
        $qs = $this->getQueryString();
        if ($qs)
        {
            return $this->getScriptURI() . '?' . $qs;
        }
        else
        {
            return $this->getScriptURI();
        }
    }

    // http://www.dat.com/login/?username=me => /login/
    public function getScriptURL()
    {
        if (isset($_SERVER['SCRIPT_URL']) || isset($_SERVER['SCRIPT_NAME']))
        {
            // try to prevent a lot of XSS injection attacks by
            // disallowing the following characters (we'll just
            // convert them to harmless underscores
            $script = isset($_SERVER['SCRIPT_URL']) ? $_SERVER['SCRIPT_URL'] : $_SERVER['SCRIPT_NAME'];
            if (preg_match('/[<>"]/', $script))
            {
                error_log("Possible XSS attack on SCRIPT_URL: " . $script);
                return strtr($script, '<>"', '___');
            }
            return $script;
        }

        return NULL;
    }

    public function getScriptURLWithQS()
    {
        $qs = $this->getQueryString();
        if ($qs)
        {
            return $this->getScriptURL() . '?' . $qs;
        }
        else
        {
            return $this->getScriptURL();
        }
    }

    // http://www.dat.com/login/?username=me => username=me
    public function getQueryString()
    {
        $requestURI = $this->getRequestURI();
        $queryString = null;

        if ($requestURI)
        {
            // Strip the query string
            if (preg_match('@\?(.+)@i', $requestURI, $matches) > 0)
            {
                $queryString = substr($matches[0], 1);
                $queryString = $this->stripForXSS($queryString);
            }

        }
        return $queryString;
    }

    public function getQueryStringArray()
    {
        if ($this->queryStringArray == null && $this->getQueryString() != null)
        {
            parse_str($this->getQueryString(), $this->queryStringArray);
        }
        return $this->queryStringArray;
    }

    // http://www.dat.com/login/?username=me => /login/?username=me
    public function getRequestURI()
    {
        if (isset($_SERVER['REQUEST_URI']))
            return $_SERVER['REQUEST_URI'];

        return NULL;
    }

    // http://www.dat.com/login/?username=me => /login/
    public function getRequestURL()
    {
        $requestURI = $this->getRequestURI();
        $requestURL = null;

        if ($requestURI)
        {
            // Strip the query string
            if (preg_match('@([^?]+)@i', $requestURI, $matches) > 0)
            {
                $requestURL = $matches[0];
                $requestUrl = $this->stripForXSS($requestURL);
            }
        }
        return $requestURL;
    }

    // http://www.dat.com/login/?username=me => www.dat.com
    public function getHost()
    {
        if (get_debug('test_client') && isset($_GET['host']))
            return $_GET['host'];

        if (isset($_SERVER['HTTP_HOST']))
            return $_SERVER['HTTP_HOST'];

        return NULL;
    }

    // http://www.dat.com/login/?username=me => www.dat.com
    public function getHTTPHost()
    {
        if (isset($_SERVER['HTTP_HOST']))
            return $_SERVER['HTTP_HOST'];

        return NULL;
    }

    public function getRequest()
    {
        return $_REQUEST;
    }

    public function getPost()
    {
        return $_POST;
    }

    public function getRequestBody()
    {
        return file_get_contents("php://input");
    }

//	public function getGet()
//	{
//		return $_GET;
//	}

    public function getParsedGet()
    {
        parse_str($this->context->getClient()->getQueryString(), $get);
        return $get;
    }

    public function getFiles()
    {
        return $_FILES;
    }

    public function getCookies()
    {
        return $_COOKIE;
    }

    public function getSessionIdentifier()
    {
        if (isset($_REQUEST['session']))
            return $_REQUEST['session'];

        return NULL;
    }

    public function getPHPAuthUser()
    {
        if (isset($_SERVER['PHP_AUTH_USER']))
            return $_SERVER['PHP_AUTH_USER'];

        return NULL;
    }

    public function getServer()
    {
        return $_SERVER;
    }

    public function getPHPAuthPW()
    {
        if (isset($_SERVER['PHP_AUTH_PW']))
            return $_SERVER['PHP_AUTH_PW'];

        return NULL;
    }

    public function isRobot()
    {
        $user_agent = $this->getUserAgent();

        if (get_debug('robot'))
        {
            return TRUE;
        }

        // no user-agent string = robot
        if (!$user_agent)
        {
            return TRUE;
        }

        // Let Snap.com through, agents:
        //	"Snapbot/1.0"
        //	"Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.0.7) Gecko/20060909 Firefox/1.5.0.7 SnapPreviewBot"
        // Let Oodlebot through (for post-allowed)
        if (preg_match('/Snapbot|SnapPreviewBot|Oodlebot/i', $user_agent))
        {
            return FALSE;
        }

        if (preg_match("/bot|edgeio|fast|googlebot|ia_archiver|omniexplorer|yahoo(\!|seeker|feedseeker)|\blwp|\bhtdig|mechanize|perl|libwww|libcurl|\bwget|python-urllib|scrape|spider|spyder|validator|emailsiphon|teoma/i", $user_agent))
        {
            return TRUE;
        }

        return FALSE;
    }

    // @TODO: Use $context->getUserType()->type == UserType::ROBOT instead.
    public function isSearchRobot()
    {
        if (!$this->getUserAgent())
            return FALSE;

        return preg_match('/googlebot|yahoo\! slurp|msnbot|teoma/i', $this->getUserAgent());
    }

    public function isSearchReferral()
    {
        return preg_match('/www.google.com|www.google.co.uk|www.yahoo.com|www.ask.com|www.bing.com/', $this->getReferrer());
    }

    protected function stripForXSS ($url)
    {
        if (preg_match('/[<>"]/', $url))
        {
            error_log("Possible XSS attack on URL: " . $url);
            return strtr($url, '<>"', '___');
        }
        return $url;
    }
}
