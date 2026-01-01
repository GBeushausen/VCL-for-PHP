<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_component.inc.php";
        use_unit("styles.inc.php");



        class CustomStyleSheetTest extends ComponentTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new CustomStyleSheet();
                        $this->object->Name="myobject";
                }

                function test_BuildStyleList()
                {
                        $c=$this->object->BuildStyleList("style.css",1,0,0);
                        $expected=array(0 => "h4",1 => ".date","2" => "body",
                                3 => "ul", 4 =>"*", 5 => ".hidden");
                        $this->assertEquals($c,$expected);

                        $c=$this->object->BuildStyleList("style.css",0,1,1);
$expected= array
(
    0 => ".date",
    1 => ".date a",
    2 => "#content h2",
    3 => "#content h4",
    4 => "#content a",
    5 => "#content p",
    6 => "#content .title",
    7 => "#page-container",
    8 => "#menu",
    9 => "#menu a",
    10 =>"#menu a:hover",
    11 => "#top",
    12 => "#top .comment",
    13 => "#top input.search",
    14 => "#top input.submit",
    15 => "#top a",
    16 => "#header",
    17 => "#prefooter",
    18 => "#prefooter p",
    19=> "#prefooter a",
    20 => "#hrgreen",
    21 => "#content",
    22 => "#content .padding",
    23 => "#sidebar-a",
    24 => "#sidebar-a p",
    25 => "#sidebar-a .padding",
    26=> "#footer",
    27 => "#footer a",
    28 => "#footer a:hover",
    29=> "#footer #copyright",
    30 => ".hidden"
);
                $this->assertEquals($c,$expected);

                }

                function test_readStyles(){/*checked in test_Styles*/}
                function test_writeStyles(){/*checked in test_Styles*/}

                function test_dumpHeaderCode()
                {
                        ob_start();
                        $this->object->dumpHeaderCode();
                        $c=ob_get_contents();
                        ob_clean();
                        print_r($c);
                        $expected='<link rel="stylesheet" href="" type="text/css" />';
                        $this->assertEquals($this->cleanString($expected),$this->cleanString($c));

                }

                function test_loaded()
                {
                        $this->test_ParseCSSFile();
                }

                function test_ParseCSSFile()
                {
                        $this->test_BuildStyleList();
                }

                //All other var members are tested in children class where this proerties are published
        }
      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_customstylesheet.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "CustomStyleSheetTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
