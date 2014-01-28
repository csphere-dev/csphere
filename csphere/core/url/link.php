<?php

/**
 * Creates the target url for links
 *
 * PHP Version 5
 *
 * @category  Core
 * @package   URL
 * @author    Hans-Joachim Piepereit <contact@csphere.eu>
 * @copyright 2013 cSphere Team
 * @license   http://opensource.org/licenses/bsd-license Simplified BSD License
 * @link      http://www.csphere.eu
 **/

namespace csphere\core\url;

/**
 * Creates the target url for links
 *
 * PHP Version 5
 *
 * @category  Core
 * @package   URL
 * @author    Hans-Joachim Piepereit <contact@csphere.eu>
 * @copyright 2013 cSphere Team
 * @license   http://opensource.org/licenses/bsd-license Simplified BSD License
 * @link      http://www.csphere.eu
 **/

abstract class Link
{
    /**
     * Checks if options are fetched already
     **/
    private static $_init = false;

    /**
     * Stores if ajax links are activated
     **/
    private static $_ajax = false;

    /**
     * Stores if pretty links are activated
     **/
    private static $_pretty = false;

    /**
     * Stores the request array data
     **/
    private static $_request = '';

    /**
     * Stores the plugin part of links
     **/
    private static $_plugin = '';

    /**
     * Stores the action part of links
     **/
    private static $_action = '';

    /**
     * Stores the key part of links
     **/
    private static $_key = '';

    /**
     * Stores the value part of links
     **/
    private static $_value = '';

    /**
     * Fetch options and set flags
     *
     * @return void
     **/

    private static function _init()
    {
        self::$_request = \csphere\core\http\Request::get();

        // Fetch view options
        $loader = \csphere\core\service\Locator::get();
        $view   = $loader->load('view');

        self::$_ajax   = $view->getOption('links_ajax');
        self::$_pretty = $view->getOption('links_pretty');

        // Set flags
        if (empty(self::$_pretty) AND empty(self::$_ajax)) {

            self::$_plugin = '?plugin=';
            self::$_action = '&amp;action=';
            self::$_key   = '&amp;';
            self::$_value = '=';

        } else {

            self::$_plugin = empty(self::$_ajax) ? '' : '#';
            self::$_action = '/';
            self::$_key   = '/';
            self::$_value = '/';
        }

        self::$_init = true;
    }

    /**
     * Creates a link for the specified parameters
     *
     * @param string $plugin Plugin
     * @param string $action Action
     * @param array  $params Array of key value pairs where value can be empty
     *
     * @return string
     **/

    public static function href($plugin, $action, array $params = array())
    {
        // Check and set options if init is not done yet
        if (self::$_init == false) {

            self::_init();
        }

        // @TODO: Use urlencode on dynamic parts

        // Add plugin and action
        $link = self::$_request['dirname'] . self::$_plugin . $plugin;

        if ($action != '') {

            $link .= self::$_action . $action;
        }

        // Add params
        unset($params['plugin'], $params['action'], $params['']);

        foreach ($params AS $key => $value) {

            // Only add params that are not empty
            if ($value != '') {

                $link .= self::$_key . $key . self::$_value . $value;
            }
        }

        return $link;
    }

    /**
     * Creates a link for the current parameters
     *
     * @return string
     **/

    public static function current()
    {
        $plugin = self::$_request['data']['plugin'];
        $action = self::$_request['data']['action'];
        $params = self::$_request['data'];

        // Unset plugin and action from params
        unset($params['plugin'], $params['action']);

        // Proceed with usual link creation now
        $result = self::href($plugin, $action, $params);

        return $result;
    }

    /**
     * Combines the request data for sharing the link
     *
     * @param string $link Unencoded local part e.g. generated by href method
     *
     * @return string
     **/

    public static function share($link)
    {
        // Next to the request dns should be a slash provided by the link variable
        $result = self::$_request['protocol']
                . '://'
                . self::$_request['dns']
                . $link;

        return $result;
    }

    /**
     * Prepares link urls
     *
     * @param string $url The internal url that is used in slash syntax
     *
     * @throws \Exception
     *
     * @return string
     **/

    public static function params($url)
    {
        $link = '';

        // Determine plugin and action
        $target = explode('/', $url, 2);
        $plugin = $target[0];

        if (empty($plugin)) {

            throw new \Exception('URL without plugin: ' . $url);

        } else {

            // Sort link parameters
            $params = array();

            if (empty($target[1])) {

                $action = '';
            } else {

                // Remove action from parameters
                $split  = explode('/', $target[1]);
                $action = $split[0];

                unset($split[0]);

                // Create valid key value pairs
                $split_key = '';

                foreach ($split AS $splitted) {

                    if ($split_key == '') {

                        $split_key          = $splitted;
                        $params[$split_key] = '';

                    } else {

                        $params[$split_key] = $splitted;
                        $split_key          = '';
                    }
                }
            }

            $link .= self::href($plugin, $action, $params);
        }

        return $link;
    }

    /**
     * Transforms a slash-separated string to an key value array
     *
     * @param string $url The internal url that is used in slash syntax
     *
     * @return array
     **/

    public static function transform($url)
    {
        $params = array();
        $split  = explode('/', $url);
        $splits = count($split);

        // Just use every second entry due to key value structure
        for ($i = 0; $i < $splits; $i+=2) {

            // Not every value might contain data
            $params[$split[$i]] = isset($split[($i+1)]) ? $split[($i+1)] : '';
        }

        // Clear empty keys
        unset($params['']);

        return $params;
    }
}