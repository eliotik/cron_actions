<?php
/**
 *
 * @author: ap@nxc.no
 * @version: 260613
 * @copyright: NXC (c) 2013
 * @description: script for crontab part of nxcCronActions class functionality
 * @run:  php runcronjobs.php -d -d cronactions
 * */
try
{
    $eol = "\n";
    echo 'Starting work...'.$eol;
    nxcCronActions::getInstance()->getActions()->executeActions();

} catch ( Exception $e ) {
    nxcCronActionsExceptionHandler::add( $e );
}

$errorList = nxcCronActionsExceptionHandler::getErrorMessageList();

if ( count($errorList) > 0 )
{
    $errors = '';
    foreach($errorList as $error) $errors .= $error.$eol;

    echo 'Got error: '.$errors.$eol;
    eZDebug::writeError( $errors, 'Error occured');

    //$script->shutdown( 1 );
    //eZExecution::cleanExit();

} else {
    echo $eol.'Done with cron actions...'.$eol.$eol;
}
?>