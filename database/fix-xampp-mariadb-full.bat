@echo off
setlocal EnableDelayedExpansion
cd /D "%~dp0.."

echo.
echo ============================================================
echo  Nectra - Full XAMPP MariaDB repair (1130 / admin login)
echo ============================================================
echo.
echo Stop MySQL from XAMPP Control Panel first, then press any key...
pause >nul

taskkill /F /IM mysqld.exe >nul 2>&1
timeout /t 3 /nobreak >nul

echo Cleaning corrupt replication junk from mysql\data ...
del /F /Q "C:\xampp\mysql\data\master-*" 2>nul
del /F /Q "C:\xampp\mysql\data\relay-log-*" 2>nul
del /F /Q "C:\xampp\mysql\data\mysql-relay-bin-*" 2>nul
echo (my.ini should include skip-slave-start and log-basename=xampp — already set by Nectra setup)

echo Starting MariaDB (skip-grant-tables + skip-slave-start)...
start "MariaDB-Repair" /MIN cmd /c "cd /D C:\xampp\mysql\bin && mysqld.exe --defaults-file=C:\xampp\mysql\bin\my.ini --skip-grant-tables --skip-slave-start --console"

echo Waiting 15 seconds for server...
timeout /t 15 /nobreak >nul

echo Restoring root@localhost / root@127.0.0.1 privileges...
"C:\xampp\mysql\bin\mysql.exe" -u root -e "REPAIR TABLE mysql.global_priv;" 2>nul
"C:\xampp\mysql\bin\mysql.exe" -u root -h 127.0.0.1 < "%~dp0restore-root-privileges.sql"
"C:\xampp\mysql\bin\mysql.exe" -u root -h 127.0.0.1 < "%~dp0fix-root-password.sql"
if errorlevel 1 (
  echo.
  echo [ERROR] Could not apply SQL. Close MariaDB-Repair window and run this script again.
  pause
  exit /b 1
)

echo.
echo Stopping repair mysqld...
taskkill /F /IM mysqld.exe >nul 2>&1
timeout /t 3 /nobreak >nul

echo Start MySQL normally from XAMPP Control Panel.
echo Then test: C:\xampp\php\php.exe database\test-connect.php
echo Seed admin: C:\xampp\php\php.exe database\run-seed-admin.php  (user admin / pass admin123)
echo.
pause
