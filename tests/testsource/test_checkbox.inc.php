<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_customcheckbox.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");

        class CheckBoxTest extends CustomCheckboxTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new Checkbox();
                        $this->object->name="myobject";
                }

                //Nothing to test here these are only published properties
                function test_getOnSubmit(){}
                function test_setOnSubmit(){}
                function test_getPopupMenu(){}
                function test_setPopupMenu(){}
         }
      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_checkbox.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "CheckboxTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
