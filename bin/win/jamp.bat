@echo off

rem jamp - Easy to use PHP scripts.
rem Author: jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
rem License: GPL-2.0

rem Get the JAMP_BASE as shown by
rem https://stackoverflow.com/a/16623984/1616963
for %%i in ("%~dp0..\..") do set "JAMP_BASE=%%~fi"
set JAMP_BASE=%JAMP_BASE%\

rem Use the first argument as the JAMP_SCRIPT
set JAMP_SCRIPT=%1

rem Shift will then skip %1 and treat what was formerly %2 as %1 and so on.
shift

rem Now, we can start building a list of arguments that we can pass to our PHP
rem helper.
set argv=%1

rem Loop through the list until we have collected all remaining arguments into
rem our argv variable.
:loop
shift
if [%1]==[] goto afterloop
set argv=%argv% %1
goto loop
:afterloop

rem Setup the PHP environment to run the script in and run it.
php %JAMP_BASE%bin\entry.php %argv%
