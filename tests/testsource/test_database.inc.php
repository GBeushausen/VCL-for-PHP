<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_user.inc.php";
        use_unit("auth.inc.php");



        class DatabaseTest extends UserTest
        {
                function setup()
                {
                        $this->object=new Database();
                        $this->object->Name="myobject";
                }

                //We need first to setup a mysql installation in order to check this.
                function test_Authenticate()
                {
//                        if($this->object->className()!="Database")
                                $this->assertEquals(true,false);
                }
        }
      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_database.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "DatabaseTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
