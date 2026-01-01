<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_customprogressbar.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("comctrls.inc.php");

        class ProgressBarTest extends CustomProgressBarTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new ProgressBar();
                        $this->object->Name="myobject";
                }


                function test_getOrientation()
                {
                        //tested in test_Orientation
                }
                function test_setOrientation()
                {
                        //tested in test_Orientation
                }

                function test_getPosition()
                {
                        //tested in test_Position
                }
                function test_setPosition()
                {
                        //tested in test_Position
                }
                function test_getMin()
                {
                        //tested in test_Min
                }
                function test_setMin()
                {
                        //tested in test_Min
                }

                function test_getMax()
                {
                        //tested in test_Max
                }
                function test_setMax()
                {
                        //tested in test_Max
                }

                function test_getStep()
                {
                        //tested in test_Orientation
                }
                function test_setStep()
                {
                        //tested in test_Orientation
                }

        }

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_progressbar.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "ProgressBarTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
