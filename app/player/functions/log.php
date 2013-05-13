<?php

/**
 * @package log
 * Functions for logging messages, exceptions, timing stats, etc.
 */

function elapsed($start)
{
    return round(microtime(TRUE) - $start, 3);
}

// Log messages to the error log if they meet the error level threshold.
// Print messages to the screen if debug is set.
// $msg can be a Closure, in which case, it is lazily evaluated only after
// we've verified that the log statement passes the threshold.
function log_msg($level, $msg)
{
    //TODO : Map string levels to int levels
    $logger = null;

    require_once('common/functions/utilities.php');

    // FALSE means get_context can return NULL. Rare exemption.
    $context = get_context(FALSE);

    if ($context)
    {
        $resources = $context->getResources();

        if ($resources)
        {
            $logger = $context->getResources()->getLogger();
        }
    }

    if (!$logger)
    {
        require_once('common/classes/log/BasicLogger.php');
        $logger = new BasicLogger();
    }

    if ($msg instanceof Closure)
    {
//		if ($logger->isValidLevel($context, $level))
//		{
        $msg = $msg();
//		}
//		else
//		{
//			return;
//		}
    }

    if (strlen($msg) > 7000)
    {
        $loopCount = 0; // Prevent infinite looping and too much output to the log

        while (strlen($msg) > 7000 && $loopCount < 20) // Allow a maximum of about 140000 bytes to be output
        {
            $sliceLength = 7000;
            $part1 = $msg;
            while (mb_strlen($msg, 'UTF-8') < $sliceLength || strlen($part1) > 7000)
            {
                $sliceLength = round($sliceLength / 2, 0);
                $part1 = mb_substr($msg, 0, $sliceLength, 'UTF-8');
                if ($sliceLength < 100)
                    break;
            }
            $msg = mb_substr($msg, $sliceLength, mb_strlen($msg, 'UTF-8') - $sliceLength, 'UTF-8');
            $logger->logMessage($context, $level, $part1);
            $loopCount++;
        }
    }
    $logger->logMessage($context, $level, $msg);
}

function log_memory($level, $msg = null)
{
    log_msg($level, ($msg ? "$msg, " : '') . sprintf("Memory Usage: %u bytes", memory_get_usage()));
}

function valid_log_level($level)
{
    $logger = null;

    require_once('common/function/utilities.php');

    // FALSE means get_context can return NULL. Rare exemption.
    $context = get_context(FALSE);

    if ($context)
    {
        $resources = $context->getResources();

        if ($resources)
        {
            $logger = $context->getResources()->getLogger();
        }
    }

    if (!$logger)
    {
        require_once('common/classes/log/ApacheLogger.php');
        $logger = new ApacheLogger();
    }

    return $logger->isValidLevel($context, $level);
}

function log_db_error($function, $result, $data_type, $level)
{
    if (isset($result['error']) && $result['error'])
        log_msg($level, "$function failed: error='" . $result['error']);
    else if (isset($result["$data_type.error"]) && $result["$data_type.error"])
        log_msg($level, "$function failed: error='" . $result["$data_type.error"]);
    else if (isset($result["$data_type.rows"]) && $result["$data_type.rows"] == 0)
        log_msg($level, "$function failed: $data_type.rows == 0, no error returned");
    else
        log_msg($level, "$function failed: (?) $data_type.rows == " .
            (isset($result["$data_type.rows"]) ?
                $result["$data_type.rows"] : 'null') .
            ", no error returned");
}

function log_exception($log_level, Exception $e, $msg = '')
{
    // FALSE means get_context can return NULL. Rare exemption.
    $context = get_context(FALSE);

    if (!$e)
    {
        log_msg('WRN', 'Call to log_exception with a NULL exception');
        return;
    }

    $req = '(unknown)';
    if ($context && $context->getClient() && $context->getClient()->getScriptURI())
    {
        $req = $context->getClient()->getScriptURI();
        if ($qs = $context->getClient()->getQueryString())
        {
            $req .= '?' . $qs;
        }
    }

    $traceArr = explode("\n", $e->getTraceAsString());
    $trace = implode('|', $traceArr);
    $ua = '(none)';

    if ($context &&
        $context->getClient() &&
        $context->getClient()->getUserAgent())
    {
        $ua = $context->getClient()->getUserAgent();
    }

    if ($msg)
    {
        $msg .= ' ';
    }

    log_msg($log_level, $msg . get_class($e) . ':' . $e->getMessage() . "' trace: $trace");

    log_msg('DBG', "request: $req");
    log_msg('DBG', "useragent: '$ua'");
}

function timing_increase($key, $amount, $description = '')
{
    // FALSE means get_context can return NULL. Rare exemption.
    $context = get_context(false);

    if ($context)
    {
        $timing = $context->getPerformance()->getTiming();
        $timing->increase($key, $amount, $description);
    }
}

function timing_and_info_log()
{
    $context = get_context();
    $timing = $context->getPerformance()->getTiming();
    $timings = $timing->getTimingInfoByService();

    if (isset($timings['time_ds']['time']))
    {
        // Log successful data source request elapsed time.
        apache_note('time_ds', $timings['time_ds']['time']);
        unset($timings['time_ds']);
    }


    $logger = $context->getResources()->getLogger();
    if ($timings)
    {
        foreach ($timings as $key => $data)
        {
            $time = round($data['time'], 3);
            $logger->addInfo($key, $time . '/' . $data['calls']);
        }
    }

    $databaseTiming = $context->getPerformance()->getDatabaseTiming();
    $db = array();
    if ($databaseTiming)
    {
        $db = $databaseTiming->serializeToArray();
    }

    if (!empty($db))
    {
        $logger->addInfo('db', implode(',', $db));
    }

    //$info = $logger->getInfoSerialized();
    //apache_note('info', $info);
}
