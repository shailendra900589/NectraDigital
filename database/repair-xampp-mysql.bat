@echo off
setlocal
cd /D "%~dp0.."

echo.
echo ============================================================
echo  Nectra Digital - Fix XAMPP MariaDB "Host localhost not allowed"
echo ============================================================
echo.
echo 1) Open XAMPP Control Panel and STOP MySQL if it is running.
echo 2) Press any key here when MySQL is stopped...
pause >nul

echo.
echo Starting MariaDB in repair mode (skip-grant-tables)...
start "MariaDB-Repair" /MIN cmd /c "cd /D C:\xampp\mysql\bin && mysqld.exe --defaults-file=C:\xampp\mysql\bin\my.ini --skip-grant-tables --skip-networking --console"

echo Waiting for server...
timeout /t 8 /nobreak >nul

echo Applying root privileges...
"C:\xampp\mysql\bin\mysql.exe" -u root --protocol=PIPE -e "FLUSH PRIVILEGES; CREATE USER IF NOT EXISTS 'root'@'localhost' IDENTIFIED BY ''; CREATE USER IF NOT EXISTS 'root'@'127.0.0.1' IDENTIFIED BY ''; CREATE USER IF NOT EXISTS 'root'@'::1' IDENTIFIED BY ''; GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION; GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' WITH GRANT OPTION; GRANT ALL PRIVILEGES ON *.* TO 'root'@'::1' WITH GRANT OPTION; FLUSH PRIVILEGES;"

if errorlevel 1 (
  echo.
  echo [WARN] PIPE connect failed — trying named pipe via default client...
  "C:\xampp\mysql\bin\mysql.exe" -u root -e "FLUSH PRIVILEGES; CREATE USER IF NOT EXISTS 'root'@'localhost' IDENTIFIED BY ''; CREATE USER IF NOT EXISTS 'root'@'127.0.0.1' IDENTIFIED BY ''; GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION; GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' WITH GRANT OPTION; FLUSH PRIVILEGES;"
)

echo.
echo Close the "MariaDB-Repair" window (or stop that mysqld process).
echo Then START MySQL normally from XAMPP Control Panel.
echo.
echo Test: php database\test-connect.php
echo.
pause
