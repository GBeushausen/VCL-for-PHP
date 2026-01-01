<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_qwidget.inc.php";
        use_unit ("menus.inc.php");
        use_unit ("stdctrls.inc.php");


        class CustomMainMenuTest extends QWidgetTest
        {
                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;
                        $this->object=new CustomMainMenu();
                        $this->object->Name="myobject";
                }


                function test_init()
                {
                        $this->aowner->insertComponent($this->object);

                        $this->assertEquals($this->aowner->executed,false,'Init test 1');

                        $_POST['myobject_state']='myobject_DummyEvent';

                        $this->object->owner->UseAjax=false;
                        $this->aowner->executed=false;
                        $this->object->OnClick="DummyEvent";
                        $this->object->init();
                        $this->assertEquals($this->aowner->executed,true,'Init test 2');

                        $this->aowner->removeComponent($this->object);
                }

                function test_ControlStyle($style=array())
                {
                        $style["csTopOffset"]=1;
                        $style["csLeftOffset"]=1;
                        $style["csSlowRedraw"]=1;
                        parent::test_ControlStyle($style);
                }

                //function test_dumpHeaderCode(){} checked in children because of duplicated names

                function test_dumpMenuItems()
                {
                        //Done within DumpContents test
                }
                function test_dumpTopButtons()
                {
                        //Done within DumpContents test
                }

                //No need to check dumpMenuItems, dumpTopButtons, dumpContents beause
                //it's called from DumpContents (called from Show(true))
                function test_dumpContents()
                {
                $items=array();

                $subitems=array();
                $subitems[]=array(
                       'Caption'=>'Sub Menu1',
                       'ImageIndex'=>0,
                       'SelectedIndex'=>0,
                       'StateIndex'=>-1,
                       'Tag'=>1
                );

                $subitems[]=array(
                       'Caption'=>'Sub Menu2',
                       'ImageIndex'=>0,
                       'SelectedIndex'=>0,
                       'StateIndex'=>-1,
                       'Tag'=>2
                );

                $items[]=array(
                       'Caption'=>'Top Menu',
                       'ImageIndex'=>0,
                       'SelectedIndex'=>0,
                       'StateIndex'=>-1,
                       'Tag'=>0,
                       'Items'=>$subitems
                );

                        $this->object->Items=$items;

                        $c=trim($this->object->show(true));
                        //print_r($c);
                        $expected=$this->cleanString('<input type="hidden" id="myobject_state" name="myobject_state" value="" /> <script type="text/javascript">  var myobject = new qx.ui.toolbar.ToolBar; myobject.removeAll(); myobject.setLeft(0); myobject.setTop(0); myobject.setWidth(300); myobject.setHeight(23); <!-- Topbutton Start -->  var m0 = new qx.ui.menu.Menu; d.add(m0); var mb0_0 = new qx.ui.menu.Button("Sub Menu1", null, null, null); mb0_0.addEventListener("execute", myobject_clickwrapper); mb0_0.tag=1; m0.add(mb0_0); var mb0_1 = new qx.ui.menu.Button("Sub Menu2", null, null, null); mb0_1.addEventListener("execute", myobject_clickwrapper); mb0_1.tag=2; m0.add(mb0_1); var mb = new qx.ui.toolbar.MenuButton("Top Menu",m0); myobject.add(mb); <!-- Topbutton End --> </script> ');
                        $c=$this->cleanString($c);
                        $this->assertEquals($c,$expected);
                }

                function test_getImages()
                {
                        //Check within test_Images();
                }

                function test_setImages()
                {
                        //Check within test_Images();
                }

                function test_Images()
                {
                        //at start Images must be null
                        $this->assertEquals($this->object->Images,null);

                        //get/set check
                        $ImageList=new ImageList();
                        $ImageList->Images=array(0=>"image1", 1=>"image2", 2=>"image3");
                        $MainMenu1= new CustomMainMenu();
                        $MainMenu2= new CustomMainMenu();
                        $MainMenu1->Images=$ImageList;
                        $MainMenu2->Images=$ImageList;
                        $this->assertEquals($MainMenu1->Images,$MainMenu2->Images,"a");
                        $this->assertEquals($ImageList,$MainMenu1->Images,"b");

                }

                function test_getItems()
                {
                        //Check within test_Items();
                }

                function test_setItems()
                {
                        //Check within test_Items();
                }

                function test_Items()
                {

                        $this->assertEquals($this->object->Items,array());


                        $Items=array(
                                 0=>
                                 array("Caption"=>"333","ImageIndex"=>"0","SeelectedIndex"=>"0",
                                       "StateIndex"=>"-1","Tag"=>"0",
                                       "Items"=>
                                          array(0=>
                                           array("Caption"=>"subitemtext","ImageIndex"=>"0","SeelectedIndex"=>"0","StateIndex"=>"-1","Tag"=>"0","Items"=>array())
                                               )
                                       )
                                     );

                        //Read/write items check
                        $MainMenu1= new CustomMainMenu();
                        $MainMenu2= new CustomMainMenu();
                        $MainMenu1->Items=$Items;
                        $MainMenu2->Items=$Items;
                        $this->assertEquals($MainMenu1->Items,$MainMenu2->Items,"a");
                        $this->assertEquals($Items,$MainMenu1->Items,"b");


                }


                function test_OnClick()
                {
                        $this->assertEquals($this->object->OnClick,null);
                        //$this->assertEquals($this->object->OnClick,$this->object->defaultOnClick());
                        $this->object->OnClick='onClickEvent';
                        $this->assertEquals($this->object->OnClick,'onClickEvent');
                }

                function test_commonScript()
                {
                        ob_start();
                        $this->object->commonScript();
                        $actual=ob_get_contents();
                        ob_end_clean();
                        //print_r($actual);
                        $expected=trim('myobject.removeAll();
        myobject.setLeft(0);
        myobject.setTop(0);
        myobject.setWidth(300);
        myobject.setHeight(23);');

                        $actual=$this->fixString($actual);
                        $this->assertEquals($expected,$actual);
                }

                function test_dumpForAjax()
                {
                        //dumpForAjax just calls commonScript
                        $this->test_CommonScript();
                }

                function test_addOtherChildren()
                {
                        if ($this->object->className()!="CustomMainMenu")
                                $this->assertEquals(true,false);
                }

        }

        if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
           else $script=$_GET['script'];

        if (basename($script)=='test_custommainmenu.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "CustomMainMenuTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }
?>
