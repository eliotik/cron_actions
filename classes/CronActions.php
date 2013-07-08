<?php

class CronActions extends CronActionsBase
{
    private static $instance = null;

    private function __construct()
    {

    }

    public static function getInstance()
    {
        if (null === self::$instance)
            self::$instance = new CronActions();
        return self::$instance;
    }

    /**
     * @param array $data array of parameters for action: class, method, data
     * @param int $seconds amount of second when to run action(default: 65)
     * @return bool false on failure. no exception will be thrown
     */
    public static function push(array $data = array(), $seconds = 65)
    {
        if ( !is_array($data) or empty($data) ) return false;
        $seconds = ( ($seconds === false) or (intval($seconds) == 0) ) ? 65 : $seconds;

        if ( !array_key_exists('class', $data) or
             !array_key_exists('method', $data) or
             !array_key_exists('data', $data) ) return false;

        if ( is_null($data['class']) or empty($data['class']) ) return false;
        if ( is_null($data['method']) or empty($data['method']) ) return false;
        if ( is_null($data['data']) or !is_array($data['data']) ) return false;

        $action = new CronAction(self::getInstance());
        return $action->create($data, $seconds);
    }
}