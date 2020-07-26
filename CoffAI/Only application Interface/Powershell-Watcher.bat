@echo off
start "" ".\apache_start_2.bat"
PowerShell.exe -ExecutionPolicy Bypass -Command "& '%~dpn0.ps1'"
REM File name of this batch file has to match the file name of the ps1 file