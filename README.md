nxc_cron_actions
================

Extension for eZ Publish

Push events to cronjob

Example of use:
```javascript
nxcCronActions::push(
    array(
        'class' => 'ClassName',
        'method' => 'MethodName',
        'data' => array(
            'ArgumentName' => 'ArgumentValue'
        )
    ),
    300
);
```

Example of crontab configuration:
```javascript
# The path to the eZ Publish directory.
EZPUBLISH=/var/vhosts/www/ezpublish

# Location of the PHP command line interface binary.
PHPCLI=/usr/bin/php

*/1 * * * * cd $EZPUBLISH && $PHPCLI runcronjobs.php cronactions 2>&1 | /usr/bin/mail -s "crontab actions" WRITE@YOUR.MAIL
```
