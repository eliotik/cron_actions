<?php
/**
 * @description: script for crontab part of CronActions class functionality
 * @run        :  php runcronjobs.php -d -d cronactions
 **/
use extension\cron_actions\classes\helpers\CronActionsExceptionHandler;
use extension\cron_actions\classes\CronActions;

try {
    $eol = "\n";
    echo 'Starting work...' . $eol;
    CronActions::getInstance()->getActions()->executeActions();

} catch (Exception $e) {
    CronActionsExceptionHandler::add($e);
}

$errorList = CronActionsExceptionHandler::getErrorMessageList();

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
