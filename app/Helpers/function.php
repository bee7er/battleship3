<?php

use Illuminate\Support\Arr;

session_start();

if (!function_exists('setSessionVariable')) {
    /**
     * Set a session variable to a given value
     * @param $variableName
     * @param $value
     */
    function setSessionVariable($variableName, $value)
    {
        $_SESSION[$variableName] = $value;
    }
}

if (!function_exists('getSessionVariable')) {
    /**
     * Retrieve a session value providing a default
     * @param $variableName
     * @param $default
     * @return mixed
     */
    function getSessionVariable($variableName, $default)
    {
        $value = isset($_SESSION[$variableName]) ? $_SESSION[$variableName]: $default;
        return $value;
    }
}

if (!function_exists('segments')) {
    /**
     * Converts individual elements of a url into an array.
     */
    function segments($url)
    {
        $segments = explode('/', $url);

        return array_values(array_filter($segments, function ($v) {
            return $v != '';
        }));
    }
}

if (!function_exists('segment')) {
    /**
     * Retrieves the specified indexed element from a url.
     */
    function segment($url, $index, $default)
    {
        return Arr::get(segments($url), $index - 1, $default);
    }
}

if (!function_exists('urlAction')) {
    /**
     * Returns the action portion of the referer url.
     */
    function urlAction()
    {
        //print request()->headers->get('referer');

        return segment(request()->headers->get('referer'), count(segments(request()->headers->get('referer'))), 'error');
    }
}

if (!function_exists('getAlpha')) {
    /**
     * Returns the alpha equivalent of an integer
     */
    function getAlpha($i)
    {
        return substr('abcdefghij', ($i-1), 1); // nb zero based
    }
}

if (!function_exists('getFormattedDate')) {
    /**
     * Returns a formatted date, or blank if none
     */
    function getFormattedDate($date)
    {
        if (isset($date) && $date != null) {
            return date('d/m/Y H:i', strtotime($date));
        }
        return '';
    }
}
