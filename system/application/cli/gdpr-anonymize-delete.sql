-- /* GDPR/privacy */

-- Delete (not anonymize ?!) in-active user accounts in the Cloudworks database.
--
-- Copyright © 2018 The Open University (IET).

USE `cloudworks_live`;

-- 1. Backup!
-- sudo mysqldump cloudworks_live | gzip > ~/cloudworks_live-lindir--all--04-dec-2018.sql.gzip
-- sudo mysqldump cloudworks_live user user_profile user_temp | gzip > ~/cloudworks-lindir-live--user_profile-etc--04-dec-2018.sql.gz
-- sudo mysqldump cloudworks_live user_temp | gzip > ~/cloudworks-lindir-live--user_temp--06-dec-2018.sql.gzip

-- 2. Test / check.

SHOW CREATE TABLE `cloudworks_live`.`user`;
SHOW CREATE TABLE cloudworks_live.user_profile;
SHOW CREATE TABLE cloudworks_live.user_temp;

SELECT count(*) FROM cloudworks_live.user;      -- 06-Dec-2018 rows = 11142.
SELECT count(*) FROM cloudworks_live.user_temp; -- 06-Dec-2018 rows = 154.

SELECT COUNT(*) FROM cloudworks_live.user WHERE last_visit IS NULL OR last_visit NOT REGEXP '201[7-9]-.+';
SELECT COUNT(*) FROM cloudworks_live.user WHERE last_visit REGEXP '201[7-9]-.+';

SELECT id,user_name,created FROM cloudworks_live.user_temp WHERE created < '2018-09-01' ORDER BY created ASC; -- 06-Dec-2018 rows = 154.

SELECT id,user_name,email FROM cloudworks_live.user WHERE role = 'admin'; -- 06-Dec-2018 rows = 7.

-- 3. Never logged in, and created before 2017.
SELECT count(*) FROM cloudworks_live.user WHERE last_visit IS NULL AND created < '2018-01-01' AND do_not_delete = 0; -- 06-Dec-2018 rows = 2026.

-- 3b. Never logged in, or not logged in since 2016.
SELECT id FROM cloudworks_live.user WHERE last_visit IS NULL OR last_visit NOT REGEXP '^201[78]-[01]' LIMIT 10;

SELECT GROUP_CONCAT(id ORDER BY id ASC SEPARATOR ',') AS User_IDs FROM cloudworks_live.user WHERE last_visit < '2017-01-01' AND do_not_delete = 0;

-- 4. Fix 'admin' list; Add do NOT delete (Emeritus) list etc.
-- ALTER TABLE cloudworks_live.`user` ADD COLUMN `do_not_delete` int(1) NOT NULL DEFAULT '0' COMMENT 'Emeritus, founders, past & present significant people.';

SET @admin_list = '1,1040,1965,2755,3162,3614,6378'; -- 7.

SET @do_not_delete = '1,3,5,6,7,8,9,10,13,15,23,28,32,66,99,103,104,110,356,358,359,363,365,367,368,983,1040,1057,1126,1933,2276,2485,6651,6809,7623,7831'; -- 41 ?!

-- UPDATE cloudworks_live.`user` SET role = 'admin' WHERE id IN ( 1,1040,1965,2755,3162,3614,6378 ); -- 7 admins.
-- UPDATE cloudworks_live.`user` SET do_not_delete = 1 WHERE role = 'admin' OR id IN ( 1,3,5,6,7,8,9,10,13,14,15 ); -- ...

-- UPDATE cloudworks_live.`user` SET role = 'user' WHERE id IN ( 3 ); -- GC.
-- UPDATE cloudworks_live.`user` SET do_not_delete = 0 WHERE id IN ( 14, 21, 24 ); -- Woops, undo!


-- 5. Delete in-active users.

-- DELETE FROM cloudworks_live.user WHERE last_visit IS NULL AND created < '2018-01-01' AND do_not_delete = 0; -- DONE. rows = 2024;

-- 6. Delete old temporary user accounts.
-- DELETE FROM cloudworks_live.user_temp WHERE created < '2018-09-01';

SELECT count(*) FROM user AS u JOIN user_profile AS p ON p.id = u.id WHERE p.moderate = 1; -- rows = 0; ??

-- 7. Hard delete previously "soft deleted" users.
SELECT count(*) FROM user AS u JOIN user_profile AS p ON p.id = u.id WHERE p.deleted = 1; -- rows = 233;
SELECT GROUP_CONCAT(id ORDER BY id ASC SEPARATOR ',') AS User_IDs FROM cloudworks_live.user_profile WHERE deleted = 1;
-- DELETE user FROM user JOIN user_profile AS p ON p.id = user.id WHERE p.deleted = 1; -- DONE.
-- DELETE FROM cloudworks_live.user_profile WHERE deleted = 1; -- DONE.

-- End.
