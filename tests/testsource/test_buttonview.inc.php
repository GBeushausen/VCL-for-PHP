<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_custombuttonview.inc.php";
        use_unit("extctrls.inc.php");



        class ButtonViewTest extends CustomButtonViewTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new ButtonView();
                        $this->object->Name="myobject";
                }

                function test_addOtherChildren()
                {
                  //This method is not implemented in this component, so override it
                }

                function test_Items()
                {
                        $this->test_dumpContents();
                }
        }

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_buttonview.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "ButtonViewTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
