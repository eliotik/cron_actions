<?php
namespace extension\cron_actions\classes\interfaces;

use extension\cron_actions\classes\CronActions;

interface CronActionInterface
{
    public function __construct(CronActions $parent);

    public function create(array $data = array(), $seconds = 65);

    public function init($id, array $data = array());

    public function run();

    public function remove();

    public function postpone($seconds = 65);

    public function lock();
}
