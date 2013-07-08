<?php

interface nxcCronActionInterface
{

    public function __construct(nxcCronActions $parent);
    public function create(array $data = array(), $seconds = 65);
    public function init($id, array $data = array());
    public function run();
    public function remove();
    public function postpone($seconds = 65);
    public function lock();


}

?>