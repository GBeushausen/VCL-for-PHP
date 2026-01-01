@cls
@ECHO You must have PHP 5 setup on the machine you want to run this script
@ECHO You can download it from http://www.php.net/downloads.php
@ECHO Change PHPDIR to the parent dir of your php installation
@ECHO Change SVN path to your svn bin directory or put it into the system path var
@ECHO Change email, smtpserver and sender account if you want to be reported by email
@ECHO ===============================================================================
@SET PHPEXE="C:\Archivos de programa\CodeGear\Delphi for PHP\2.0\php\php.exe"
@SET PHPINI="C:\Archivos de programa\CodeGear\Delphi for PHP\2.0\php\php.ini"
@SET EMAIL="support@qadram.com"
@SET SMTPSERVER="mail.qadram.com"
@SET SENDER="tester@vcltester.com"
@SET SVNEXE="c:\Archivos de programa\svn\bin\svn.exe"
@SET MYSQLEXE="C:\Archivos de programa\MySQL\MySQL Server 5.0\bin\mysql.exe"
@SET MYSQLPASS="test"
@SET MYSQLUSER="root"
@SET ISQLEXE="C:\Archivos de programa\Borland\InterBase\bin\isql.exe"

@rem Enter Full path here
@SET IBDATABASEFILE="localhost:C:\Archivos de programa\Borland\InterBase\PRUEBA_DB.GDB"

@rem Check all paths
@if not exist %PHPEXE% goto NoPHPEXE
@if not exist %PHPINI% goto NoPHPINI
@if not exist %SVNEXE% goto NoSVNEXE
@if not exist %MYSQLEXE% goto NoMYSQLEXE
@if not exist %ISQLEXE% goto NoISQLEXE

@ECHO Checking out test dir
@call run_svncheckout.bat

@ECHO Building test databases (servers need to be installed)
@ECHO Building mysql databases
@%MYSQLEXE% -u %MYSQLUSER% -p%MYSQLPASS%  < "testsource/mysqldump.sql"
@ECHO Building interbase databases
@%PHPEXE% insertibdump.php %ISQLEXE% %IBDATABASEFILE%

@ECHO Running test batching, this may take some minutes, be patient...
@call run_php_tests.bat %1 >output.html 2>errors.txt

@echo Sending email to %EMAIL%
%PHPEXE% -d SMTP=%SMTPSERVER% -d sendmail_from=%SENDER% mail.php %EMAIL%

@echo Test passed Ok... Check Output.html for results.
@goto Exit

:NoPHPEXE
@ECHO Could not find %PHPEXE%, review your settings.
@goto Exit
:NoPHPINI
@ECHO Could not find %PHPINI%, review your settings.
@goto Exit
:NoSVNEXE
@ECHO Could not find %SVNEXE%, review your settings.
@goto Exit
:NoMYSQLEXE
@ECHO Could not find %MYSQLEXE%, review your settings.
@goto Exit
:NoISQLEXE
@ECHO Could not find %ISQLEXE%, review your settings.
@goto Exit


:Exit