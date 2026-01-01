<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_customtreeview.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("comctrls.inc.php");
        use_unit("menus.inc.php");

        class TreeViewTest extends CustomTreeViewTest
        {
                function setup()
                {
                        parent::Setup();
                        $this->aowner=$this->stowner;
                        $this->object=new TreeView();
                        $this->object->Name="myobject";
                }

                function test_addOtherChildren()
                {
                        if ($this->object->className()!="TreeView")
                                $this->assertEquals(true,false);
                }

                function test_PopupMenu()
                {
                  $p=new PopupMenu();
                  $this->defaultTest('PopupMenu',null,$p);
                }

                function test_jsOnDeActivate()
                {
                  $this->defaultTest('jsOnDeActivate',null,"test");
                }

         }

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_treeview.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "TreeViewTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
