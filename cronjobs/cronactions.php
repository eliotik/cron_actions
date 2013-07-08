<?php
/*
 * @run:  php runcronjobs.php -d -d cronactions
 **/
try
{
    $eol = "\n";
    echo 'Starting work...'.$eol;
    CronActions::getInstance()->getActions()->executeActions();

} catch ( Exception $e ) {
    CronActionsExceptionHandler::add( $e );
}

$errorList = CronActionsExceptionHandler::getErrorMessageList();

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