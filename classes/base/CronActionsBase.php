<?php

abstract class CronActionsBase
{
    /**
     * @var array array of CronAction objects
     */
    private $actions = array();
    const TABLE = 'cron_actions';
    const STATUS_FREE = 0;
    const STATUS_LOCKED = 1;
    const STATUS_REMOVE = 2;

    public function log($data)
    {

    }

    public function getActions()
    {
        $this->actions = array();

        $db = eZDB::instance();
        $status = self::STATUS_FREE;
        $time = time()+1;
        $table = self::TABLE;
        $query = "select * from {$table} where status = {$status} and execute_time < {$time}";

        $actions = $db->arrayQuery( $query );

        if (!empty($actions))
        {
            foreach($actions as $action)
            {
                $action_object = new CronAction($this);
                $action_object->init(intval($action['id']),unserialize($action['data']));
                $this->actions[] = $action_object;
            }
        }

        return $this;
    }

    public function executeActions()
    {
        $count = count($this->actions);
        echo "Actions amount: ".$count."\n";
        if ($count == 0) return $this;

        foreach($this->actions as $index=>$action)
        {
            $num = $index + 1;
            echo "Executing Action {$num}/{$count}...\n";
            $action->run();
        }

        return $this;
    }

}

?>