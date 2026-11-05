SELECT COUNT(*) AS global_priv_rows FROM mysql.global_priv;

INSERT INTO mysql.global_priv (Host, User, Priv) VALUES
('localhost', 'root', '{"access":18446744073709551615,"version":1,"plugin":"mysql_native_password","authentication_string":"","password_last_changed":0}'),
('127.0.0.1', 'root', '{"access":18446744073709551615,"version":1,"plugin":"mysql_native_password","authentication_string":"","password_last_changed":0}'),
('::1', 'root', '{"access":18446744073709551615,"version":1,"plugin":"mysql_native_password","authentication_string":"","password_last_changed":0}');

FLUSH PRIVILEGES;

ALTER USER 'root'@'localhost' IDENTIFIED BY '';
ALTER USER 'root'@'127.0.0.1' IDENTIFIED BY '';
ALTER USER 'root'@'::1' IDENTIFIED BY '';
FLUSH PRIVILEGES;

CREATE DATABASE IF NOT EXISTS nectradigital CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
