<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_customchecklistbox.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");
        use_unit("menus.inc.php");

        class CheckListboxTest extends CustomCheckListboxTest
        {
                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;
                        $this->object=new CheckListbox();
                        $this->object->Name="myobject";
                }




                function test_getPopupMenu()
                {
                        //tested within test_PopupMenu
                }

                function test_setPopupMenu()
                {
                        //tested within test_PopupMenu
                }

        }

        if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_checklistbox.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "CheckListboxTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
