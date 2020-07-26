@echo off
echo.
echo --------------------------------------------------------------
echo.
echo Please close this Command Prompt only for Shutdown !
echo.
echo --------------------------------------------------------------
echo.
echo Apache server is running ...

c:\xampp\apache\bin\httpd.exe

if errorlevel 255 goto finish
if errorlevel 1 goto error
goto finish

:error
echo.
echo Apache could not be started
pause

:finish