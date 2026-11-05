UPDATE mysql.global_priv
SET priv = '{"access":18446744073709551615,"version":1,"plugin":"mysql_native_password","authentication_string":"","password_last_changed":1730812800}'
WHERE User = 'root';

FLUSH PRIVILEGES;
