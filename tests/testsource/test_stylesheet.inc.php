<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_customstylesheet.inc.php";
        use_unit ("comctrls.inc.php");

        class StyleSheetTest extends CustomStyleSheetTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new StyleSheet();
                        $this->object->Name="myobject";
                }

                function test_FileName()
                {
                        $this->assertEquals($this->object->FileName,$this->object->defaultFileName(),"a");
                        $this->object->FileName="myfilename";
                        $this->assertEquals($this->object->FileName,"myfilename","b");
                }

                function test_IncludeStandard()
                {
                        $this->assertEquals($this->object->IncludeStandard,$this->object->defaultIncludeStandard(),"a");
                        $this->object->IncludeStandard=true;
                        $this->assertEquals($this->object->IncludeStandard,true,"b");
                }

                function test_IncludeID()
                {
                        $this->assertEquals($this->object->IncludeID,$this->object->defaultIncludeID(),"a");
                        $this->object->IncludeID=true;
                        $this->assertEquals($this->object->IncludeID,true,"b");
                }

                function test_IncludeSubStyle()
                {
                        $this->assertEquals($this->object->IncludeSubStyle,$this->object->defaultIncludeSubStyle(),"a");
                        $this->object->IncludeSubStyle=true;
                        $this->assertEquals($this->object->IncludeSubStyle,true,"b");
                }

        }

        if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
           else $script=$_GET['script'];

        if (basename($script)=='test_stylesheet.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "StyleSheetTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }
?>
