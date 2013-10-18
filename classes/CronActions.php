<?php
namespace extension\cron_actions\classes;

use extension\cron_actions\classes\actions\CronAction;

class CronActions
{
    /**
     * @var array array of CronAction objects
     */
    private $actions = array();
    const TABLE = 'cron_actions';
    const STATUS_FREE = 0;
    const STATUS_LOCKED = 1;
    const STATUS_REMOVE = 2;
    private static $instance = null;
    private $config = array();

    private function __construct()
    {
        $this->initConfig();
    }

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new CronActions();
        }

        return self::$instance;
    }

    /**
     * @param array $data  array of parameters for action: class, method, data
     * @param int $seconds amount of second when to run action(default: 65)
     *
     * @return bool false on failure. no exception will be thrown
     */
    public static function push(array $data = array(), $seconds = 65)
    {
        if (!is_array($data) or empty($data)) {
            return false;
        }
        $seconds = (($seconds === false) or (intval($seconds) == 0)) ? 65 : $seconds;

        if (!array_key_exists('class', $data) or
            !array_key_exists('method', $data) or
            !array_key_exists('data', $data)
        ) {
            return false;
        }

        if (is_null($data['class']) or empty($data['class'])) {
            return false;
        }
        if (is_null($data['method']) or empty($data['method'])) {
            return false;
        }
        if (is_null($data['data']) or !is_array($data['data'])) {
            return false;
        }

        $action = new CronAction(self::getInstance());

        return $action->create($data, $seconds);
    }

    private function initConfig()
    {
        $this->config = array();
        $ini = \eZINI::instance("cron_actions.ini");
        $this->config['send_mail'] = intval($ini->variable("GeneralSettings", "SendEmail")) > 0;
        if ($this->config['send_mail'] == true) {
            $this->config['mail'] = array();
            $this->config['mail']['receiver'] = $ini->variable("GeneralSettings", "ReceiverEmail");
            $this->config['mail']['sender'] = $ini->variable("GeneralSettings", "SenderEmail");
            $this->config['mail']['subject'] = $ini->variable("GeneralSettings", "EmailSubject");
            $this->config['send_mail'] = !empty($this->config['mail']['receiver']);
            $this->config['send_mail'] = !empty($this->config['mail']['sender']);
            $this->config['send_mail'] = !empty($this->config['mail']['subject']);
        }
    }

    public function sendMail($body) {
        CronActions::log("trying to send email...");
        if (($this->config['send_mail'] == false) or empty($body)) return false;
        CronActions::log("sending email: start");
        $mail = new \eZMail();
        $mail->setSender($this->config['mail']['sender']);
        $mail->setReceiver($this->config['mail']['receiver']);
        $mail->setSubject($this->config['mail']['subject']);
        $mail->setBody($body);
        if (!$result = \eZMailTransport::send($mail)) {
            self::log("Cannot send email notification:\n".$body);
        }
        CronActions::log("sending email: end");
        return $result;
    }

    public static function log($data)
    {
        \eZDebug::writeError($data, "CronActions");
        \eZLog::write($data, 'cron_actions.log');
    }

    public function getActions()
    {
        $this->actions = array();

        $db = \eZDB::instance();
        $status = self::STATUS_FREE;
        $time = time() + 1;
        $table = self::TABLE;
        $query = "select * from {$table} where status = {$status} and execute_time < {$time}";

        $actions = $db->arrayQuery($query);

        if (!empty($actions)) {
            foreach ($actions as $action) {
                $action_object = new CronAction($this);
                $action_object->init(intval($action['id']), unserialize($action['data']));
                $this->actions[] = $action_object;
            }
        }

        return $this;
    }

    public function executeActions()
    {
        $count = count($this->actions);
        self::log("Actions amount: " . $count);
        if ($count == 0) {
            return $this;
        }

        /**
         * @var CronAction $action
         */
        foreach ($this->actions as $index => $action) {
            $num = $index + 1;
            self::log("Executing Action {$num}/{$count}...");
            $action->run();
        }

        return $this;
    }
}
