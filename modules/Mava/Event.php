<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 3/15/14
 * Time: 2:32 PM
 * To change this template use File | Settings | File Templates.
 */
class Mava_Event
{
    protected static $_listeners = false;

    private function __construct()
    {
    }

    public static function fire($event, array $args = array(), $hint = null)
    {
        if (!self::$_listeners || empty(self::$_listeners[$event]))
        {
            return true;
        }

        if ($hint !== null)
        {
            if (!empty(self::$_listeners[$event]['_']))
            {
                foreach (self::$_listeners[$event]['_'] AS $callback)
                {
                    if (is_callable($callback))
                    {
                        $return = call_user_func_array($callback, $args);
                        if ($return === false)
                        {
                            return false;
                        }
                    }
                }
            }

            if ($hint !== '_' && !empty(self::$_listeners[$event][$hint]))
            {
                foreach (self::$_listeners[$event][$hint] AS $callback)
                {
                    if (is_callable($callback))
                    {
                        $return = call_user_func_array($callback, $args);
                        if ($return === false)
                        {
                            return false;
                        }
                    }
                }
            }
        }
        else
        {
            foreach (self::$_listeners[$event] AS $callbacks)
            {
                foreach ($callbacks AS $callback)
                {
                    if (is_callable($callback))
                    {
                        $return = call_user_func_array($callback, $args);
                        if ($return === false)
                        {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    public static function getEventListeners($event)
    {
        if (isset(self::$_listeners[$event]))
        {
            return self::$_listeners[$event];
        }
        else
        {
            return false;
        }
    }

    public static function setListeners(array $listeners, $keepExisting = true)
    {
        if (!self::$_listeners || !$keepExisting)
        {
            self::$_listeners = $listeners;
        }
        else
        {
            self::$_listeners = array_merge_recursive(self::$_listeners, $listeners);
        }
    }

    public static function addListener($event, $callback, $hint = '_')
    {
        if (!is_array(self::$_listeners))
        {
            self::$_listeners = array();
        }

        self::$_listeners[$event][$hint][] = $callback;
    }

    public static function removeListeners()
    {
        self::$_listeners = false;
    }
}