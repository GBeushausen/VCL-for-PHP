<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_customlistview.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("comctrls.inc.php");

        class ListViewTest extends CustomListViewTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new listView();
                        $this->object->Name="myobject";
                }

                function test_unserialize()
                {
                        $this->aowner->insertComponent($this->object);
                        $this->object->addColumn("column1");
                        $this->object->addColumn("column2");
                        $this->object->addItem("item1", "sub11","sub12",true);
                        $this->object->addItem("item2", "sub21","sub22",false);
                        $this->object->SortAscending=1;
                        $this->object->SortColumnIndex=2;
                        $this->object->serialize();
                        $cols=$this->object->Columns;
                        $items=$this->object->Items;
                        $this->object->Columns=array();
                        $this->object->Items=array();
                        $this->object->unserialize();
                        $this->assertEquals($this->object->Columns,$cols,"Columns must be restored from session");
                        $this->assertEquals(count($this->object->Items),2,"Items must be restored from session");
                        $this->aowner->removeComponent($this->object);
                }

               function test_dumpContents()
                {
                        parent::test_dumpContents();
                }

                function test_addOtherChildren()
                {
                        if ($this->object->className()!="ListView")
                                $this->assertEquals(true,false);
                }



         }

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_listview.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "ListViewTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
