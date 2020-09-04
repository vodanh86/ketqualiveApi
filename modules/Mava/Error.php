<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 3/15/14
 * Time: 10:38 AM
 * To change this template use File | Settings | File Templates.
 */

abstract class Mava_Error {
    public static $_messageVisible = 'Xay ra loi, vui long thu lai sau.';
    const INVALID_REQUEST = 400;
    const ACCESS_DENIED = 403;
    const NOT_FOUND = 404;
    const SERVER_ERROR = 500;
    public function __construct()
    {
    }


    public static function handlePhpError($errorType, $errorString, $file, $line)
    {

        if ($errorType & error_reporting())
        {
            $trigger = true;
            if (!Mava_Application::debugMode())
            {
                if ($errorType & E_STRICT
                    || (defined('E_DEPRECATED') && $errorType & E_DEPRECATED)
                    || (defined('E_USER_DEPRECATED') && $errorType & E_USER_DEPRECATED))
                {
                    $trigger = false;
                }
                else if ($errorType & E_NOTICE || $errorType & E_USER_NOTICE)
                {
                    $trigger = false;
                    $e = new ErrorException($errorString, 0, $errorType, $file, $line);
                    self::logException($e, false);
                }
            }

            if ($trigger)
            {
                throw new ErrorException($errorString, 0, $errorType, $file, $line);
            }
        }
    }

    public static function handleException($e)
    {
        self::logException($e);
        $message = $e->getMessage();
        $message .= "\n". $e->getFile() .':'. $e->getLine();
        if(is_debug()){
            echo '<h2>Error:</h2><xmp>';
            print_r($message);
            echo '</xmp>';
        }else{
            echo __('an_unexpected_error_occurred') . '<!-- '. $message .' -->';
        }
    }

    public static function handleFatalError()
    {
        $error = @error_get_last();
        if (!$error)
        {
            return;
        }

        if (empty($error['type']) || !($error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR)))
        {
            return;
        }

        try
        {
            self::logException(
                new ErrorException("Fatal Error: " . $error['message'], $error['type'], 1, $error['file'], $error['line'])
            );
            if(is_debug()){
                echo '<h2>Error:</h2><xmp>';
                print_r($error['message'] ."\r\n". $error['file'] .": ". $error['line']);
                echo '</xmp>';
            }else{
                echo __('an_unexpected_error_occurred');
            }
        }
        catch (Exception $e) {}
    }

    public static function logException($e, $rollbackTransactions = true, $messagePrefix = ''){

    }
}