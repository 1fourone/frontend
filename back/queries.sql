-- queries.sql
--  Contains all queries used in application
--  Assumes mariadb MySQL implementation

-- LOGIN
$sql = "SELECT id, sid, iid FROM USER WHERE name='$name' AND password='$pw'";

-- 