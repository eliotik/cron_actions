/*!40014 SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0 */;

SET NAMES 'utf8';

USE skiioo;

DROP TABLE IF EXISTS cron_actions;
CREATE TABLE IF NOT EXISTS cron_actions (
  id           BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  status       TINYINT(4)          NOT NULL DEFAULT 0,
  execute_time INT(10) UNSIGNED    NOT NULL,
  data         TEXT                NOT NULL,
  added        DATETIME            NOT NULL,
  updated      DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  INDEX IDX_cron_actions_execute_time (execute_time),
  INDEX IDX_cron_actions_id (id),
  INDEX IDX_cron_actions_status (status)
)
  ENGINE = INNODB
  AUTO_INCREMENT = 1
  CHARACTER SET utf8
  COLLATE utf8_general_ci
  COMMENT = 'actions to be executed by CronActions class functionality';

DELIMITER $$

DROP TRIGGER IF EXISTS cronactions_before_add$$
CREATE TRIGGER cronactions_before_add
BEFORE INSERT
ON cron_actions
FOR EACH ROW
  BEGIN
    SET new.added = CONCAT(CURRENT_DATE(), ' ', CURRENT_TIME());
  END
$$

DROP TRIGGER IF EXISTS cronactions_before_update$$
CREATE TRIGGER cronactions_before_update
BEFORE UPDATE
ON cron_actions
FOR EACH ROW
  BEGIN
    SET new.updated = CONCAT(CURRENT_DATE(), ' ', CURRENT_TIME());
  END
$$

DELIMITER ;

/*!40014 SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS */;