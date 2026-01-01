@cls
@ECHO This script runs the selenium tests, you must have java and a working PHP and Apache
@ECHO setup.
@ECHO Edit this file and set paths according to your system
@ECHO ===============================================================================
@SET APACHE_EXE=C:\astro2007\apache2\bin\apache.exe
@SET PHP_EXE="C:\astro2007\php\php.exe"
@SET TEST_FOLDER=C:\astro2007\vcl\tests\selenium\vcltests\tests
@SET PHPINI="C:\astro2007\php\php.ini"


start %APACHE_EXE% -c "DocumentRoot %TEST_FOLDER%"
start java -jar server\selenium-server.jar
cd vcltests
%PHP_EXE% VCLTests.php
cd ..