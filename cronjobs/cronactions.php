<?php
/**
 * @author     : ap@nxc.no
 * @version    : 260613
 * @copyright  : NXC (c) 2013
 * @description: script for crontab part of NxcCronActions class functionality
 * @run        :  php runcronjobs.php -d -d cronactions
 * */
use extension\nxc_cron_actions\classes\helpers\NxcCronActionsExceptionHandler;
use extension\nxc_cron_actions\classes\NxcCronActions;
$user = \eZUser::currentUser();

try {
    $eol = "\n";
    echo 'Starting work...' . $eol;
    NxcCronActions::getInstance()->getActions()->executeActions();

} catch (Exception $e) {
    NxcCronActionsExceptionHandler::add($e);
}

$errorList = NxcCronActionsExceptionHandler::getErrorMessageList();

if (count($errorList) > 0) {
    $errors = '';
    foreach ($errorList as $error) {
        $errors .= $error . $eol;
    }

    echo 'Got error: ' . $errors . $eol;
    \eZDebug::writeError($errors, 'Error occured');
} else {
    echo $eol . 'Done with cron actions...' . $eol . $eol;
}
