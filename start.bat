@echo off
title News Blog Server
cd /d "%~dp0"
echo Starting Laravel Server at http://127.0.0.1:8000 ...
C:\laragon\bin\php\php-8.4.12-nts-Win32-vs17-x64\php.exe artisan serve --host=127.0.0.1 --port=8000
pause
