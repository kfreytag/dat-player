<?php

// @param int $count
// @param int $lowerBound
// @param int $upperBound
// @param string|int $mask
// @return string|int
// If the count is gt the upper bound, show the upper bound mask.
// If the count is lt the lower bound, show the lower bound mask.
function count_format($count, $lowerBound = 0, $lowerBoundMask = NULL, $upperBound = 1000000, $upperBoundMask = 'millions')
{
    if (!get_debug('showall'))
    {
        if ($count >= $upperBound)
            return $upperBoundMask;

        if ($count <= $lowerBound)
            return $lowerBoundMask;
    }

    return number_format($count);
}

function numbers_only($in)
{
    // Remove all non-numbers and periods.
    $in = preg_replace('/[^\d\.]+/', '', $in);
    // Remove everything after the last period.
    return preg_replace('/[.].*/', '', $in);
}

// Given a number, turn it into a price (ie format such as $1,234.56).
function price_format(Context $context, $price, $listingCountryCode = NULL)
{
    if (!isset($price))
        return NULL;

    // Price of 0 means free.
    if ($price == '0')
        return 'Free';

    $currencySymbol = NULL;

    $countrySource = $context->getResources()->getCountrySource();

    //setlocale(LC_MONETARY, 'en_US');
    if ($listingCountryCode)
    {
        // reference: http://www.jhall.demon.co.uk/currency/
        // The general principle used to construct these abbreviations is to
        // take the two-letter abbreviations defined in ISO 3166 (Codes for the
        // Representation of Names of Countries) and append the first letter of
        // the currency name (e.g., USD for the United States Dollar).getCountryCode
        $countryLanguage = CountryConfigYAML::getInstance()->getValue($listingCountryCode, 'default_lang');
        $countryCode2 = $countrySource->getCountry($listingCountryCode)->getCountryCode2();
        $locale =  $countryLanguage.'_'.$countryCode2. '.UTF8';
        setlocale(LC_MONETARY, $locale);
        $currencySymbol = CountryConfigYAML::getInstance()->getValue($listingCountryCode, 'currency_symbol');
    }

    if ($currencySymbol)
    {
        $price = $currencySymbol . ' ' . mb_convert_encoding(money_format('%!n', $price), 'HTML-ENTITIES', 'UTF-8');
    }
    elseif ($listingCountryCode &&
        !in_array($listingCountryCode, $context->getResources()->getRegions()->getFullySupportCountryCodes()))
    {
        $price = mb_convert_encoding(money_format('%!n', $price), 'HTML-ENTITIES', 'UTF-8');
    }
    else
    {
        if (!is_numeric($price))
        {
            $price = preg_replace('/\s|[,]|[$]|[£]/', '', $price);
        }
        $price = mb_convert_encoding(money_format('%n', $price), 'HTML-ENTITIES', 'UTF-8');
    }
    if ($listingCountryCode)
    {
        // reset the locale
        $countryLanguage = CountryConfigYAML::getInstance()->getValue($context->getCountryCode(), 'default_lang');
        $countryCode2 = $countrySource->getCountry($context->getCountryCode())->getCountryCode2();
        $locale =  $countryLanguage.'_'.$countryCode2. '.UTF8';
        setlocale(LC_MONETARY, $locale);
    }

    return preg_replace('/\.00$/', '', $price);
}

function attribute_format(Context $context, $value, $attribute)
{
    return AttributeTransform::generate($context, $attribute, $value);
}

function pluralize($noun, $count, $plural = NULL, $showCount = TRUE)
{
    if ($count == 1)
        return $showCount ? "1 $noun" : $noun;

    if ($showCount)
    {
        return number_format($count) . ' ' . (($plural !== NULL) ? $plural : "${noun}s");
    }
    else
    {
        return ($plural !== NULL) ? $plural : "${noun}s";
    }
}

function naturalize($str)
{
    $str = numbers_only($str);

    if (!is_numeric($str))
    {
        $natural = NULL;
    }
    else
    {
        $natural = abs(intval($str));
    }

    return $natural;
}

function truncate($text, $limit, $suffix = '...')
{
    if ($limit !== NULL && // dont bother when no limit specified
        is_numeric($limit) && // prevent unwanted behavior when incorrectly called
        mb_strlen($text,'UTF-8') && // prevents "" from being turned into "..." if limit=0
        mb_strlen($text,'UTF-8') > $limit)
    {
        return mb_substr($text, 0, $limit - mb_strlen($suffix,'UTF-8'), 'UTF-8') . $suffix;
    }

    return $text;
}

/*
 * Inflectors
 */

// foo_bar_baz => FooBarBaz
function camelize($lowerCaseAndUnderscoredWord)
{
    return str_replace(" ", "",
        ucwords(
            str_replace(array("-", "_"), " ", $lowerCaseAndUnderscoredWord)));
}

function underscorize($camelCasedWord)
{
    return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $camelCasedWord));
}

function hyphenize($camelCasedWord)
{
    return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '-\\1', $camelCasedWord));
}

/* Strict=TRUE strips non-alphanumeric characters */
function variablize($string, $strict = false)
{
    $string = $strict ? alphanumeric_only($string) : $string;
    $string = camelize(underscorize($string));
    $replace = strtolower(substr($string, 0, 1));
    $variable = preg_replace('/\\w/', $replace, $string, 1);
    return $variable;
}

function alphanumeric_only($string)
{
    // Remove all non-numbers and periods.
    // Remove everything after the last period.
    // @dwest: does this do anything useful? I took this from numeric_only.
    return preg_replace(array('/[^\d\.\w]+/', '/[.].*/'), '', $string);
}

function efn($name)
{
    static $cache = array();
    if (!isset($cache[$name]))
        $cache[$name] = str_replace(array('[','/',']'), '-', $name);
    return $cache[$name];
}
?>