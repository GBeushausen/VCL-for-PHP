<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_qwidget.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");
        use_unit("menus.inc.php");

        class ScrollBarTest extends QWidgetTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new ScrollBar();
                        $this->object->Name="myobject";
                }

                function test_PopupMenu()
                {
                  $p=new PopupMenu();
                  $this->defaultTest('PopupMenu',null,$p);
                }

                function test_Kind()
                {
                        $this->assertEquals($this->object->Kind, sbHorizontal);
                        $this->assertEquals($this->object->defaultKind(),$this->object->Kind);
                        $this->object->Kind= sbVertical;
                        $this->assertEquals($this->object->Kind, sbVertical);
                        $this->object->Kind= 'new kind';
                        $this->assertEquals($this->object->Kind, 'new kind');
                }

                function test_Min()
                {
                        $this->assertEquals($this->object->Min, 0);
                        $this->assertEquals($this->object->defaultMin(),$this->object->Min);
                        $this->object->Min= 20;
                        $this->assertEquals($this->object->Min, 20);
                }

                function test_Max()
                {
                        $this->assertEquals($this->object->Max, 500);
                        $this->assertEquals($this->object->defaultMax(),$this->object->Max);
                        $this->object->Max= 10000;
                        $this->assertEquals($this->object->Max, 10000);
                }



                function test_Position()
                {
                        $this->assertEquals($this->object->Position, 0);
                        $this->assertEquals($this->object->defaultPosition(),$this->object->Position);
                        $this->object->Position= 45;
                        $this->assertEquals($this->object->Position, 45);
                        $this->object->Position= 'nowhere';
                        $this->assertEquals($this->object->Position, 'nowhere');
                }

                function test_PageSize()
                {
                        $this->assertEquals($this->object->PageSize, 0);
                        $this->assertEquals($this->object->defaultPageSize(),$this->object->PageSize);
                        $this->object->PageSize= 45;
                        $this->assertEquals($this->object->PageSize, 45);
                        $this->object->PageSize= 'nowhere';
                        $this->assertEquals($this->object->PageSize, 'nowhere');
                }
                function test_dumpContents()
                {
                        $actual=$this->object->Show(true);
                        print_r($actual);
                        $expected='<input type="hidden" id="myobject_state" name="myobject_state" value="" />
<script type="text/javascript">
  var myobject = new qx.ui.core.ScrollBar(true);
  myobject.setLeft(0);
  myobject.setTop(0);
  myobject.setWidth(200);
  myobject.setHeight(17);
  myobject.setMaximum(100001);
  myobject.setValue(0);
  myobject.setEnabled(true);
  myobject.setVisibility(true);
</script>';
                        $actual=$this->cleanString($this->fixString($actual));
                        $this->assertEquals($actual,$this->cleanString($expected));
                }

                function test_addOtherChildren()
                {
                        if ($this->object->className()!="ScrollBar")
                                $this->assertEquals(true,false);
                }

                function test_getSmallChange(){}
                function test_setSmallChange(){}
                function test_getLargeChange(){}
                function test_setLargeChange(){}

/*                function test_PopupMenu()
                {
                        $PopupMenu=new PopupMenu();
                        $this->assertEquals($this->object->PopupMenu,$this->object->defaultPopupMenu());
                        $this->object->PopupMenu=$PopupMenu;
                        $this->assertEquals($PopupMenu,$this->object->PopupMenu);
                }*/

         }
      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_scrollbar.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "ScrollBarTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
