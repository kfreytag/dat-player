<?php

/**
 * @package utilities
 * Common routines that don't logically fit in any of the other
 * common/function modules.
 */

// TODO : Actually implement this based on YAML config
function get_debug ($option)
{
    return false;
}

function ip_match($remote, $ipcidr)
{
    if (strpos($ipcidr, '/') !== FALSE)
    {
        list($net, $mask) = explode('/', $ipcidr);
    }
    else
    {
        // If no mask, default to 32.
        $net = $ipcidr;
        $mask = 32;
    }

    $lnet = ip2long($net);
    $lip = ip2long($remote);
    $binnet = str_pad(decbin($lnet), 32, '0', STR_PAD_LEFT);
    $firstpart = substr($binnet, 0, $mask);
    $binip = str_pad(decbin($lip), 32, '0', STR_PAD_LEFT);
    $firstip = substr($binip, 0, $mask);

    return ($firstpart === $firstip);
}

function print_r_array_rec($array, $tab = NULL)
{
    $str = null;

    if (is_array($array))
    {
        $str = 'array(' . "\n";

        foreach ($array as $key => $value)
        {
            if (is_string($value))
            {
                $str .= "{$tab}\t'{$key}' => '{$value}',\n";
            }
            else if (is_numeric($value))
            {
                $str .= "{$tab}\t'{$key}' => {$value},\n";
            }
            else if (is_array($value))
            {
                $str .= "{$tab}\t'{$key}' => " . print_r_array_rec($value, "{$tab}\t");
            }
            else if (is_bool($value))
            {
                $str .= "{$tab}\t'{$key}' => " . ($value ? 'TRUE' : 'FALSE') . ",\n";
            }
            else if ($value === NULL)
            {
                $str .= "{$tab}\t'{$key}' => NULL,\n";
            }
            else
            {
                $str .= "{$tab}\t'{$key}' => 'Unknown Object',\n";
            }
        }

        $str .= ($tab) ? "\t{$tab}),\n" : "\t{$tab});\n";
    }

    return $str;
}

function var_dump_array($var)
{
    ob_start();
    var_dump($var);
    $a=ob_get_contents();
    ob_end_clean();
    return $a;
}

function print_r_array($array)
{
    echo '<pre>' . print_r_array_rec($array) . '</pre>';
}

//alias for print_r_pre
function pr($var, $return = FALSE, $label = NULL)
{
    return print_r_pre($var, $return = FALSE, $label = NULL);
}

function print_r_pre($var, $return = FALSE, $label = NULL)
{
    $htmlOpen = '';
    $htmlClose = '';

    if (isset($label))
    {
        $label = h($label);
        $htmlOpen = "<a href=\"javascript://\" onclick=\"document.getElementById('$label').style.display='block'\">$label</a><div style='display:none;' id='$label'>";
        $htmlClose = "</div>";
    }

    $html = $htmlOpen.' <pre style="text-align: left;">';

    if ($var === NULL)
        $html .= 'Null object';
    else
    {
        $text = print_r($var, TRUE);
        preg_match('#\n(?: *)\[resources:protected\].*?\n( *)\(#', $text, $match);
        if (isset($match[1]))
        {
            $whitespace = $match[1];
            $text = preg_replace("#\n( *)\[resources:protected\][^\n]*\n$whitespace\((.*)\n$whitespace\)\n#s", "\n\$1[resources:protected] => ***hidden***", $text);
        }
        $html .= $text;
    }

    $html .= "</pre>\n$htmlClose";

    if ($return)
        return $html;
    else
        echo $html;
}

function base64_json_encode($value)
{
    return 'b64' . base64_encode(json_encode($value));
}

function print_r_compact($var, $return = FALSE)
{
    $result = print_r_array_rec($var, TRUE);
    $result = preg_replace('/\s*\n\s*/', ' ', $result);
    if ($return)
        return $result;
    else
        echo $result;
}

function var_export_compact($var, $return = FALSE)
{
    if (is_array($var))
    {
        $toImplode = array();
        foreach ($var as $key => $value)
        {
            $toImplode[] = var_export($key, true) . '=>' . var_export_compact($value, true);
        }
        $str = 'array('.implode(',', $toImplode).')';

        if ($return)
        {
            return $str;
        }
        else
        {
            echo $str;
        }
    }
    else
    {
        return var_export($var, $return);
    }
}

function implode_to_english(array $array, $glue = ', ', $lastGlue = ' and ')
{
    $last = array_pop ($array);
    if (count ($array) == 0)
        return $last;

    return implode ($glue, $array). $lastGlue .$last;
}

function db_error($result, $type)
{
    if (isset($result['error']) && $result['error'])
        return $result['error'];

    if (isset($result["$type.error"]) && $result["$type.error"])
        return $result["$type.error"];

    if (!isset($result["$type.rows"]) || $result["$type.rows"] == 0)
        return 'empty set';
}

function get_browser_name_and_version(Context $context)
{
    $userAgent = $context->getClient()->getUserAgent();

    if (preg_match('|MSIE ([0-9].[0-9]{1,2})|', $userAgent, $matched))
    {
        $browserVersion = $matched[1];
        $browser = 'IE';
    }
    else if (preg_match('|Opera ([0-9].[0-9]{1,2})|', $userAgent, $matched))
    {
        $browserVersion = $matched[1];
        $browser = 'Opera';
    }
    else if(preg_match('|Firefox/([0-9\.]+)|', $userAgent, $matched))
    {
        $browserVersion = $matched[1];
        $browser = 'Firefox';
    }
    else if(preg_match('|Safari/([0-9\.]+)|', $userAgent, $matched))
    {
        $browserVersion = $matched[1];
        $browser = 'Safari';
    }
    else
    {
        $browserVersion = 0;
        $browser = 'other';
    }
    return array($browser, $browserVersion);
}

function strip_leading_slash($str)
{
    return strip_leading($str, '/');
}

function strip_trailing_slash($str)
{
    return strip_trailing($str, '/');
}

function strip_leading($str, $toRemove)
{
    if (startsWith($str, $toRemove))
        return substr($str, strlen($toRemove));

    return $str;
}

function strip_trailing($str, $toRemove)
{
    $length = strlen($str);

    if (endsWith($str, $toRemove))
        return substr($str, 0, $length - strlen($toRemove));

    return $str;
}

function mb_strip_trailing($str, $toRemove, $encoding = 'UTF-8')
{
    $length = mb_strlen($str, $encoding);

    if (mb_endsWith($str, $toRemove, $encoding))
        return mb_substr($str, 0, $length - mb_strlen($toRemove, $encoding), $encoding);

    return $str;
}

function mb_trim($str, $encoding = 'UTF-8')
{
    $newstr = $str;
    //.Trim the end of the string first
    $trimming = true;
    while($trimming)
    {
        $len = mb_strlen($newstr, $encoding);
        if ($len > 0)
        {
            $char = mb_substr($newstr, $len - 1, 1, $encoding);
            switch ($char)
            {
                case " ":
                case "\t":
                case "\n":
                case "\r":
                case "\0":
                case "\x0B":
                    $newstr = mb_substr($newstr, 0, $len - 1, $encoding);
                    break;
                default:
                    $trimming = false;
                    break;
            }
        }
        else
        {
            $trimming = false;
        }
    }

    //.Trim the beginning of the string
    $trimming = true;
    while($trimming)
    {
        $len = mb_strlen($newstr, $encoding);
        if ($len > 0)
        {
            $char = mb_substr($newstr, 0, 1, $encoding);
            switch ($char)
            {
                case " ":
                case "\t":
                case "\n":
                case "\r":
                case "\0":
                case "\x0B":
                    $newstr = mb_substr($newstr, 1, $len - 1, $encoding);
                    break;
                default:
                    $trimming = false;
                    break;
            }
        }
        else
        {
            $trimming = false;
        }
    }
    return $newstr;
}


function mb_endsWith($str, $sub, $encoding = 'UTF-8')
{
    $strlen = mb_strlen($str, $encoding);
    $sublen = mb_strlen($sub, $encoding);
    return (mb_substr($str, $strlen - $sublen, $sublen, $encoding) === $sub);
}

/**
 * A multibyte version of str_replace
 */
function mb_str_replace($search, $replace, $subject, &$count = 0, $encoding = 'UTF-8')
{
    if (is_array($search))
    {
        if (is_array($replace))
        {
            $len = count($search);
            $theSubject = $subject;
            for ($i = 0 ; $i < $len ; $i++)
            {
                $theSubject = mb_str_replace($search[$i], $replace[$i], $theSubject, $count, $encoding);
            }
            return $theSubject;
        }
        else
        {
            throw new Exception('mb_str_replace: Array passed for search, but not replace');
        }
    }
    elseif (is_array($replace))
    {
        throw new Exception('mb_str_replace: Array passed for replace, but not search');
    }

    if (!$search)
    {
        return $subject;
    }

    $offset = 0;
    $theSubject = $subject;
    $pos = mb_strpos($theSubject, $search, $offset, $encoding);

    $searchLen = mb_strlen($search, $encoding);
    while($pos !== FALSE)
    {
        $count++;
        $part1 = '';
        if ($pos > 0)
            $part1 = mb_substr($theSubject, 0, $pos, $encoding);

        $theSubjectLen = mb_strlen($theSubject, $encoding);
        $part2 = '';


        if ($theSubjectLen != $pos + $searchLen)
        {
            $part2 = mb_substr($theSubject, $pos + $searchLen, $theSubjectLen - $pos - $searchLen, $encoding);
        }
        $theSubject = $part1 . $replace;
        $offset = mb_strlen($theSubject, $encoding);
        $theSubject .= $part2;

        /*		print_r_pre( array('pos' => $pos,
                     'part1' => $part1,
                     'searchLen' => $searchLen,
                     'offset' => $offset,
                     'theSubjectLen' => mb_strlen($theSubject, $encoding),
                     'theSubject' => $theSubject)); */

        $pos = mb_strpos($theSubject, $search, $offset, $encoding);
    }
    return $theSubject;
}

function stripEmailDomain($value, $encoding = 'UTF-8')
{
    if (!$value)
        return $value;

    if (($pos = mb_strpos($value, '@', 0, $encoding)) !== false)
    {
        return(mb_substr($value, 0, $pos, $encoding));
    }
    return $value;
}

// Checks a string for validity as a hex color
// Please remove any '//' from the string first
// Note that a valid hex color is either 3 or 6 chars long (3-char shortcut for ffcc00 is fc0)
function is_hex_color($c)
{
    return preg_match('/^[0-9a-f]{3}([0-9a-f]{3})?$/i', $c);
}

// sets the type of each element in the array
function array_settype(array &$array, $type)
{
    foreach ($array as &$value)
        settype($value, $type);
}

function array_flatten(array $array)
{
    $out = array();
    foreach($array as $val){
        if(is_array($val))
        {
            $out = array_merge($out, array_flatten($val));
        }
        else
        {
            $out[] = $val;
        }
    }
    return $out;
}


// rounds the value to the closest multiple of $multiple
function roundToMultiple($value, $multiple)
{
    return round($value / $multiple) * $multiple;
}

function info_name(Context $context, $infoyml, $infourl)
{
    $partner = $context->getPartner();

    if (isset($infoyml[$infourl]['name']) && ($partner->isDefaultOrClone() || !isset($infoyml[$infourl]['partner_name'])))
        return $infoyml[$infourl]['name'];
    else if (isset($infoyml[$infourl]['partner_name']))
        return $infoyml[$infourl]['partner_name'];

    return NULL;
}

function info_validurl(Context $context, $infoyml, $infourl)
{
    $partner = $context->getPartner();

    if ($partner->isDefaultOrClone() && isset($infoyml[$infourl]))
    {
        if (isset($infoyml[$infourl]))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    else
    {
        return $context->getPartner()->hasInfoPage($infourl);
    }
}

function feature_enabled(Context $context, array $menu, $page)
{
    $partner = $context->getPartner();
    if ($partner->isDefaultOrClone())
        return TRUE;

    // WTF explanation:
    // some info pages exist but are only shown in the footer
    // i.e not in the info left nav (more info: Matt D)

    //@abhi: Removing "&& !context->getPartner()->isFooterOnlyInfoPage($page))" for side_nav to appear for partners
    return (isset($menu[$page]) &&
        $context->getPartner()->hasInfoPage($page));
}

// Returns a string of length $length that is guarenteed to be unique over time
// for a reasonable period of time...
// @param int $length
// @param string $allowedChars
// @return string
function unique_string($length, $allowedChars = '0123456789abcdefghijklmnopqrstuvwxyz')
{
    $rand = ''; // start with a blank string
    $lastIndex = strlen($allowedChars) - 1;

    $today = date("YmdHis");

    if($length>strlen($today))
    {
        $rand = $today;
    }

    // add random characters to $rand until $length is reached
    // pick a random character from the allowed characters
    for ($i=strlen($rand); $i<$length; $i++)
        $rand .= $allowedChars[mt_rand(0, $lastIndex)];

    return $rand;
}

function swap(&$a, &$b)
{
    $c = $a;
    $a = $b;
    $b = $c;
}

function get_context($throws = TRUE)
{
    $context = isset($GLOBALS['context']) ? $GLOBALS['context'] : NULL;

    if ($context == NULL && $throws)
        throw new Exception("No context. Did you write a bad unit test?");

    return $context;
}

function get_config($throws = TRUE)
{
    $context = isset($GLOBALS['context']) ? $GLOBALS['context'] : NULL;

    if ($context == NULL && $throws)
        throw new Exception("No context. Did you write a bad unit test?");

    $config = $context->getResources()->getConfig();

    return $config;
}


// returns TRUE if the value is remotely in the affirmative, things
// for example that equate to a TRUE return value:
// 1
// yes
// TRUE
// case insensitive
// datatype insensitive
function is_affirmative($value)
{
    if (isset($value))
    {
        if (!is_null($value))
        {
            if (is_bool($value))
                return $value;
            if (is_numeric($value))
                return $value > 0;
            if (is_string($value))
            {
                if ((0 == strcasecmp($value, 'yes')) ||
                    (0 == strcasecmp($value, 'true')) ||
                    (0 == strcasecmp($value, 'ok')) ||
                    (0 == strcasecmp($value, 'sure')) ||
                    (0 == strcasecmp($value, 'absolutely')))
                    return TRUE;
            }
        }
    }

    return FALSE;
}

function exit_with_trace($outputToScreen = TRUE)
{
    try
    {
        throw new Exception();
    }
    catch (Exception $e)
    {
        if ($outputToScreen)
        {
            print_r($e->getTraceAsString());
        }
        else
        {
            log_msg('DBG', print_r($e->getTraceAsString(), TRUE));
        }
        done();
    }
}

function done_on_abort()
{
    if (connection_aborted())
    {
        log_msg('ERR', 'Connection aborted!');
        if ($context = get_context())
        {
            if ($performance = $context->getPerformance())
            {
                $timings = $performance->getTiming()->getTimingSummary();
                log_msg('DBG', print_r($timings, TRUE));
                foreach ($timings as $service => $time)
                {
                    log_msg('DBG', "$service: $time");
                    log_msg('DBG', print_r($performance->getTiming()->getTimingByService($service), TRUE));
                }
            }
        }
        done();
    }
}

function get_api_keys()
{
    $context = get_context();
    return $context->getResources()->getConfig()->getValue('globals', 'api_keys');
}

function get_current_trace()
{
    try
    {
        throw new Exception();
    }
    catch (Exception $e)
    {
        return $e->getTraceAsString();
    }
}

function h($string, $quote_style = ENT_COMPAT, $doubleEncode = false)
{
    return $string;
}

function jslink($urlString)
{
    $strAsArray = array(); // not using str_split because it's not mb-safe

    $length = mb_strlen($urlString);
    for ($i = 0; $i < $length; $i += 8)
    {
        $strAsArray[] = mb_substr($urlString, $i, 8);
    }

    return 'rd(' . json_encode($strAsArray) . '); return false;';
}

function sendExceptionEmail(Exception $e)
{
    require_once('common/classes/email/Email.php');
    log_exception('ERR', $e, 'sendExceptionEmail called');
    global $context;
    Email::sendException($context, $e, 'kurt@digitaladtech.com', 'web-errors@entourageadmin.com');
}

function gethost($ip)
{
    if (preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $ip))
    {
        $host = `host -W 1 $ip`;
        return ($host ? rtrim(end(explode(' ', $host)), "\n.") : $ip);
    }
    return $ip;
}

/**
 *  Returns a new array containing all the elements of the original array, except those that are rejected by $func
 */
function array_reject(&$arr, $func)
{
    $new = array();
    foreach ($arr as $elm)
    {
        if (!$func($elm))
        {
            $new[] = $elm;
        }
    }
    return $new;
}

/**
 * unshifts an associative array
 *  (note there may be a more clever way to do this with array_reverse,
 *   however when I tried it, it didn't work)
 */
function array_unshift_assoc(&$arr, $key, $value)
{
    $new = array($key => $value);
    foreach ($arr as $k => $v)
    {
        if (!isset($new[$k]))
        {
            $new[$k] = $v;
        }
    }
    $arr = $new;
    return count($arr);
}

/**
 * Does a recursive array_map on an array or array of arrays
 */
function array_map_recursive(&$arr, $func)
{
    $new = array();
    foreach ($arr as $key => $value)
    {
        if (is_array($value))
        {
            $new[$key] = array_map_recursive($value, $func);
        }
        else
        {
            $new[$key] = $func($value);
        }
    }
    return $new;
}

//returns base 64 encoded string with url unsafe characters replaced
function base64_url_safe_encode($str) {
    $map = array('/'=>'.s', '+'=>'.p', '='=>'.e');
    $str = base64_encode($str);
    foreach($map as $char=>$esc)
    {
        $str = str_replace($char, $esc, $str);
    }
    return $str;
}

//returns base 64 unencoded string, first reversing encoded characters
function base64_url_safe_decode($str)
{
    $map = array('/'=>'.s', '+'=>'.p', '='=>'.e');
    foreach($map as $char=>$esc)
    {
        $str = str_replace($esc, $char, $str);
    }
    $str = base64_decode($str);
    return $str;
}

/**
 * json_utf8 encodes, and then substitutes single quotes for double quotes (useful for onclick functions)
 */
function json_single_quote_encode($toEncode)
{
    $json = json_encode($toEncode);
    $json = str_replace("'", "\'", $json);
    $json = str_replace('\\"', "#!#OODLETEMP#!#", $json);
    $json = str_replace('"', "'", $json);
    $json = str_replace("#!#OODLETEMP#!#", '"', $json);
    return $json;
}

if (!function_exists('startsWith'))
{
    function startsWith($haystack,$needle,$case=true) {
        if ($haystack == null || $needle == null) return false;
        if($case){return (strcmp(substr($haystack, 0, strlen($needle)),$needle)===0);}
        return (strcasecmp(substr($haystack, 0, strlen($needle)),$needle)===0);
    }
}

if (!function_exists('endsWith'))
{
    function endsWith($haystack,$needle,$case=true) {
        if ($haystack == null || $needle == null) return false;
        if($case){return (strcmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);}
        return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);
    }
}

?>