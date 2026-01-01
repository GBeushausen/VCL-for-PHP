<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_customupdown.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("comctrls.inc.php");

        class UpDownTest extends CustomUpDownTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new UpDown();
                        $this->object->Name="myobject";
                }


                function test_addOtherChildren()
                {
                        if ($this->object->className()!="UpDown")
                                $this->assertEquals(true,false);
                }
                function test_getjsOnDeActivate(){}
                function test_setjsOnDeActivate(){}

        }

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_updown.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "UpDownTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
