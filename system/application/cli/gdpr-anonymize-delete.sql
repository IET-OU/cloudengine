-- /* GDPR/privacy */

-- Delete (not anonymize ?!) in-active user accounts in the Cloudworks database.
--
-- Copyright © 2018 The Open University (IET).

USE `cloudworks_live`;

-- 1. Backup!
-- sudo mysqldump cloudworks_live | gzip > ~/cloudworks_live-lindir--all--04-dec-2018.sql.gzip
-- sudo mysqldump cloudworks_live user user_profile user_temp | gzip > ~/evolutionmegalab-lindir--user_profile-etc--04-dec-2018.sql.gz

-- 2. Test / check.

SHOW CREATE TABLE `cloudworks_live`.`user`;
SHOW CREATE TABLE cloudworks_live.user_profile;
SHOW CREATE TABLE cloudworks_live.user_temp;

SELECT count(*) FROM cloudworks_live.user;

SELECT COUNT(*) FROM cloudworks_live.user WHERE last_visit IS NULL OR last_visit NOT REGEXP '201[7-9]-.+';
SELECT COUNT(*) FROM cloudworks_live.user WHERE last_visit REGEXP '201[7-9]-.+';

SELECT * FROM cloudworks_live.user WHERE role = 'admin';

-- Never logged in, and created before 2017.
SELECT count(*) FROM cloudworks_live.user WHERE last_visit IS NULL AND created NOT REGEXP '^201[8]-[01]';

-- Never logged in, or not logged in since 2016.
SELECT id FROM cloudworks_live.user WHERE last_visit IS NULL OR last_visit NOT REGEXP '^201[78]-[01]' LIMIT 10;

SELECT GROUP_CONCAT(id ORDER BY id ASC SEPARATOR ',') AS User_IDs FROM cloudworks_live.user WHERE last_visit < '2017-01-01';

-- ...
-- ...

-- End.
