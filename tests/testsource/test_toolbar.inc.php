<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_customtoolbar.inc.php";
        use_unit ("comctrls.inc.php");

        class ToolBarTest extends CustomToolBarTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new ToolBar();
                        $this->object->Name="myobject";
                }


                function test_addOtherChildren()
                {
                        if ($this->object->className()!="ToolBar")
                                $this->assertEquals(true,false);
                }

                function test_getImages() {/*checked in test_Property*/}
                function test_setImages() {/*checked in test_Property*/}
                function test_getItems() {/*checked in test_Property*/}
                function test_setItems() {/*checked in test_Property*/}
                function test_getUseParts() {/*checked in test_Property*/}
                function test_setUseParts() {/*checked in test_Property*/}

                function test_Images()
                {
                        //at start Images must be null
                        $this->assertEquals($this->object->Images,null);

                        //get/set check
                        $ImageList=new ImageList();
                        $ImageList->Images=array(0=>"image1", 1=>"image2", 2=>"image3");
                        $ToolBar1= new ToolBar();
                        $ToolBar2= new ToolBar();
                        $ToolBar1->Images=$ImageList;
                        $ToolBar2->Images=$ImageList;
                        $this->assertEquals($ToolBar1->Images,$ToolBar2->Images,"a");
                        $this->assertEquals($ImageList,$ToolBar1->Images,"b");

                }

                function test_Items()
                {
                        $this->assertEquals($this->object->Items,array());
                        $Items=array(1=>"item1",2=>"item2",3=>"item3");
                        $this->object->Items=$Items;
                        $this->assertEquals($this->object->Items,$Items);
                }

                function test_UseParts()
                {
                        $this->assertEquals($this->object->UseParts,$this->object->defaultUseParts());
                        $this->object->UseParts=false;
                        $this->assertEquals($this->object->UseParts,false);
                }

        }

        if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
           else $script=$_GET['script'];

        if (basename($script)=='test_toolbar.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "ToolBarTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }
?>
