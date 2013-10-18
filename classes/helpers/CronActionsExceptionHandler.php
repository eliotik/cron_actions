<?php
namespace extension\cron_actions\classes\helpers;

class CronActionsExceptionHandler
{
    protected static $ErrorList = array();

    /**
     * Adds exception message to error list
     *
     * @param \Exception $e
     * @param bool $title
     * @param bool $log
     *
     * @internal param $ (Exception) $e
     * @internal param $ (string)        $title Group name of exceptions
     * @internal param $ (bool)          $log TRUE means the error should be logged
     */
    public static function add(\Exception $e, $title = false, $log = true)
    {
        if (!$title) {
            $title = 'An error has occurred';
        }
        $error = $e->getMessage();
        if ($log) {
            \eZDebug::writeError($error, $title);
            \eZLog::write('[' . self::getIP() . '] ' . $error, 'cron_actions.log');
        }
        self::$ErrorList[$title][] = $e->getMessage();
    }

    /**
     * Returns error list
     * @return array (array)
     */
    public static function getErrorList()
    {
        return self::$ErrorList;
    }

    /**
     * Returns error message list
     * @return array (array)
     */
    public static function getErrorMessageList()
    {
        $errorList = self::getErrorList();
        $result = array();

        foreach ($errorList as $titleList) {
            foreach ($titleList as $error) {
                $result[] = $error;
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public static function getIP()
    {
        $strRemoteIP = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : false;
        if (!$strRemoteIP) {
            $strRemoteIP = urldecode(getenv('HTTP_CLIENTIP'));
        }

        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $strIP = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $strIP = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $strIP = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $strIP = getenv('HTTP_FORWARDED');
        } else {
            $strIP = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : 'UNDEFINED';
        }

        if ($strRemoteIP != $strIP) {
            $strIP = (!empty($strRemoteIP)) ? ($strRemoteIP . ', ' . $strIP) : $strIP;
        }

        return $strIP;
    }
}
