<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_custompanel.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");


        class PanelTest extends CustomPanelTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new Panel();
                        $this->object->Name="myobject";
                }

                function test_getParentFont(){}
                function test_setParentFont(){}

        }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_panel.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "PanelTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>
