<?php
       ini_set('display_errors',1);
       error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_customedit.inc.php";
        use_unit("menus.inc.php");
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");

        class EditTest extends CustomEditTest
        {
                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;
                        $this->object=new Edit();
                        $this->object->name="myobject";
                }

                function test_getPopupMenu(){}
                function test_setPopupMenu(){}
                function test_getParentFont(){}
                function test_setParentFont(){}
        }

           if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_edit.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "EditTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
