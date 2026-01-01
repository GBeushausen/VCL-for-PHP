<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_customcontrol.inc.php";
        use_unit("controls.inc.php");


        class GraphicControlTest extends ControlTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new GraphicControl();
                        $this->object->name="myobject";
                }

                function test_dumpContents()
                {
                        if($this->object->className()!="GraphicControl")
                                $this->assertEquals(true,false);
                }
        }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_graphiccontrol.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "GraphicControlTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>
