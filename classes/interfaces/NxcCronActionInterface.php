<?php
namespace extension\nxc_cron_actions\classes\interfaces;

use extension\nxc_cron_actions\classes\NxcCronActions;

interface NxcCronActionInterface
{
    public function __construct(NxcCronActions $parent);

    public function create(array $data = array(), $seconds = 65);

    public function init($id, array $data = array());

    public function run();

    public function remove();

    public function postpone($seconds = 65);

    public function lock();
}
