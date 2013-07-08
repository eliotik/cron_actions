<?php

class nxcCronAction implements nxcCronActionInterface
{
    /**
     * @var nxcCronActions
     */
    private $parent;
    private $id = null;
    private $class = null;
    private $method = null;
    private $data = array();
    private $status = null;

    public function __construct(nxcCronActions $parent)
    {
        $this->parent = $parent;
        $this->status = $parent::STATUS_FREE;
    }

    public function create(array $data = array(), $seconds = 65)
    {
        $seconds = ( ($seconds === false) or (intval($seconds) == 0) ) ? 65 : $seconds;
        $db = eZDB::instance();
        $parent = $this->parent;
        $table = $parent::TABLE;
        $status = $parent::STATUS_FREE;
        $time = time()+$seconds;
        $data = serialize($data);
        $query = "insert into {$table} (status, execute_time, data) values ({$status}, {$time}, '{$data}')";
        $db->query( $query );
        $this->status = $status;
        return true;
    }

    public function init($id, array $data = array())
    {
        $this->id = $id;
        $this->class = $data['class'];
        $this->method = $data['method'];
        $this->data = $data['data'];
    }

    public function run()
    {
        $this->lock();
        $reflection = new ReflectionMethod($this->class, $this->method);
        $reflection->setAccessible(true);
        if ($reflection->isStatic())
        {
            $result = $reflection->invokeArgs(null, $this->data);
        } else {
            if (method_exists($this->class, '__construct') and is_callable(array($this->class,'__construct')))
            {
                $object = new $this->class();
            } elseif (method_exists($this->class,'getInstance') and is_callable(array($this->class,'getInstance'))) {
                $class = $this->class;
                $object = $class::getInstance();
            } else {
                throw new Exception('Action cannot be executed. Class "'.$this->class.
                                    '" must has public method "__construct" or static method "getInstance" or method "'.$this->method.
                                    '" should be static.');
            }
            $result = $reflection->invokeArgs($object, $this->data);
        }
        $this->remove();
        /*if ($result === true)
        {
            echo 'remove';
            $this->remove();
        } else {
            echo 'postpone';
            $this->postpone();
        }*/
    }

    public function remove()
    {
        $db = eZDB::instance();
        $parent = $this->parent;
        $table = $parent::TABLE;
        $id = $this->id;
        $query = "delete from {$table} where id = {$id}";
        $db->query( $query );
    }

    public function postpone($seconds = 65)
    {
        $seconds = ( ($seconds === false) or (intval($seconds) == 0) ) ? 65 : $seconds;
        $db = eZDB::instance();
        $parent = $this->parent;
        $table = $parent::TABLE;
        $status = $parent::STATUS_FREE;
        $id = $this->id;
        $time = time()+$seconds;
        $query = "update {$table} set status = {$status}, execute_time = {$time} where id = {$id}";
        $db->query( $query );
        $this->status = $status;
    }

    public function lock()
    {
        $db = eZDB::instance();
        $parent = $this->parent;
        $table = $parent::TABLE;
        $status = $parent::STATUS_LOCKED;
        $id = $this->id;
        $query = "update {$table} set status = {$status} where id = {$id}";
        $db->query( $query );
        $this->status = $status;
    }
}