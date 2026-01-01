<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_custompagecontrol.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");
        use_unit("menus.inc.php");

        class PageControlTest extends CustomPageControlTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new PageControl();
                        $this->object->Name="myobject";
                }
                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        print_r($c);
                        $c=$this->fixString($c);
                        $compare=$this->cleanString('<input type="hidden" id="myobject_state" name="myobject_state" value="" /> <script type="text/javascript">  var myobject = new qx.ui.pageview.tabview.TabView; myobject.setLeft(0); myobject.setTop(0); myobject.setWidth(300); myobject.setHeight(400); myobject.setPlaceBarOnTop(true); myobject.setVisibility(true); </script>');
                        $this->assertEquals($this->cleanString($c),$compare);
                }

                function test_addOtherChildren()
                {
                        if ($this->object->className()!="PageControl")
                                $this->assertEquals(true,false);
                }

               function test_getPopupMenu()
                {
                        //tested within test_PopupMenu
                }

                function test_setPopupMenu()
                {
                        //tested within test_PopupMenu
                }


                function test_jsOnDeActivate()
                {
                        $this->assertEquals($this->object->getjsOnDeActivate(),null);
                        $this->assertEquals($this->object->getjsOnDeActivate(),$this->object->defaultJSOnDeActivate());
                        $this->object->setjsOnDeActivate("OnDeActivateHandler");
                        $this->assertEquals($this->object->getjsOnDeActivate(),"OnDeActivateHandler");
                }

                function test_Tabs()
                {
                        $tabs=null;
                        $this->assertEquals($tabs,$this->object->defaultTabs());
                        $tabs=array(0=>"tab1",1=>"tab2");
                        $this->object->Tabs=$tabs;
                        $this->assertEquals($tabs,$this->object->Tabs);
                }

                function test_TabIndex()
                {
                        $this->assertEquals($this->object->TabIndex,$this->object->defaultTabIndex());
                        $this->object->TabIndex=1;
                        $this->assertEquals($this->object->TabIndex,1);
                }

                function test_TabPosition()
                {
                        $this->assertEquals($this->object->TabPosition,$this->object->defaultTabPosition());
                        $this->object->TabPosition="tpBottom";
                        $this->assertEquals($this->object->TabPosition,"tpBottom");
                }
         }
      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_pagecontrol.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "PageControlTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }
?>
