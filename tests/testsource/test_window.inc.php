<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_qwidget.inc.php";
        use_unit("stdctrls.inc.php");

        class WindowTest extends QWidgetTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new Window();
                        $this->object->Name="myobject";
//                        $this->object->owner=new Window();
                }

                function test_Modal()
                {
                  $this->defaultTest('Modal',0,true);
                }

                function test_IsVisible()
                {
                  $this->defaultTest('IsVisible',true,false);
                }



                function test_ControlStyle($styles=array())
                {
                        $styles["csAcceptsControls"]=1;
                        $styles["csSlowRedraw"]=1;
                        parent::test_ControlStyle($styles);
                }

                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        //print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim('<script type="text/javascript">
  var d = qx.ui.core.ClientDocument.getInstance();
  var myobject = new qx.ui.window.Window("", "icon/16/apps/accessories-disk-usage.png");
  myobject.setLocation(0, 0);
  myobject.setWidth();
  myobject.setHeight();
  myobject.setModal(false);
  myobject.setResizeable(true);
  myobject.setMoveable(true);
  myobject.setMoveMethod("opaque");
  myobject.setResizeMethod("frame");
  myobject.setShowClose(true);
  myobject.setShowMinimize(true);
  myobject.setShowMaximize(true);
  myobject.setShowIcon(true);
  myobject.setShowCaption(true);
  myobject.setShowStatusbar(false);
  d.add(myobject)
  myobject.open();
</script>');
                        $this->assertEquals(trim($c),$compare);
                }

                function test_addOtherChildren()
                {
                        if ($this->object->className()!="Window")
                                $this->assertEquals(true,false);
                }

                function test_commonScript()
                {
                        ob_start();
                        $this->object->commonScript();
                        $actual=ob_get_contents();
                        ob_end_clean();
                        //print_r($actual);
                        $expected=trim('myobject.setModal(false);
  myobject.setResizeable(true);
  myobject.setMoveable(true);
  myobject.setMoveMethod("opaque");
  myobject.setResizeMethod("frame");
  myobject.setShowClose(true);
  myobject.setShowMinimize(true);
  myobject.setShowMaximize(true);
  myobject.setShowIcon(true);
  myobject.setShowCaption(true);
  myobject.setShowStatusbar(false);');

                        $actual=$this->fixString($actual);
                        $this->assertEquals($expected,$actual);


                }

                function test_dumpForAjax()
                {
                        //dumpForAjax just calls commonScript
                        $this->test_CommonScript();
                }

                function test_Resizeable()
                {
                        $this->assertEquals($this->object->Resizeable,$this->object->defaultResizeable());
                        $this->object->Resizeable=0;
                        $this->assertEquals($this->object->Resizeable,0);
                }

                function test_Moveable()
                {
                        $this->assertEquals($this->object->Moveable,$this->object->defaultMoveable());
                        $this->object->Moveable=0;
                        $this->assertEquals($this->object->Moveable,0);
                }

                function test_ShowMinimize()
                {
                        $this->assertEquals($this->object->ShowMinimize,$this->object->defaultShowMinimize());
                        $this->object->ShowMinimize=0;
                        $this->assertEquals($this->object->ShowMinimize,0);
                }


                function test_ShowMaximize()
                {
                        $this->assertEquals($this->object->ShowMaximize,$this->object->defaultShowMaximize());
                        $this->object->ShowMaximize=0;
                        $this->assertEquals($this->object->ShowMaximize,0);
                }

                function test_ShowClose()
                {
                        $this->assertEquals($this->object->ShowClose,$this->object->defaultShowClose());
                        $this->object->ShowClose=0;
                        $this->assertEquals($this->object->ShowClose,0);
                }

                function test_ShowIcon()
                {
                        $this->assertEquals($this->object->ShowIcon,$this->object->defaultShowIcon());
                        $this->object->ShowIcon=0;
                        $this->assertEquals($this->object->ShowIcon,0);
                }

                function test_ShowCaption()
                {
                        $this->assertEquals($this->object->ShowCaption,$this->object->defaultShowCaption());
                        $this->object->ShowCaption=0;
                        $this->assertEquals($this->object->ShowCaption,0);
                }

                function test_MoveMethod()
                {
                        $this->assertEquals($this->object->MoveMethod,$this->object->defaultMoveMethod());
                        $this->object->MoveMethod="mymethod";
                        $this->assertEquals($this->object->MoveMethod,"mymethod");
                }

                function test_ResizeMethod()
                {
                        $this->assertEquals($this->object->ResizeMethod,$this->object->defaultResizeMethod());
                        $this->object->ResizeMethod="mymethod";
                        $this->assertEquals($this->object->ResizeMethod,"mymethod");
                }

                function test_ShowStatusBar()
                {
                        $this->assertEquals($this->object->ShowStatusBar,$this->object->defaultShowStatusBar());
                        $this->object->ShowStatusBar=0;
                        $this->assertEquals($this->object->ShowStatusBar,0);
                }


                function test_IconSource()
                {
                        $this->assertEquals($this->object->IconSource,$this->object->defaultIconSource());
                        $this->object->IconSource="/dar1/myicon.ico";
                        $this->assertEquals($this->object->IconSource,"/dar1/myicon.ico");
                }

                function test_jsOnMinimize()
                {
                        $this->assertEquals($this->object->jsOnMinimize,null);
                        $this->object->jsOnMinimize="event";
                        $this->assertEquals($this->object->jsOnMinimize,"event");
                }

                function test_jsOnRestore()
                {
                        $this->assertEquals($this->object->jsOnRestore,null);
                        $this->object->jsOnRestore="event";
                        $this->assertEquals($this->object->jsOnRestore,"event");
                }

                function test_jsOnMaximize()
                {
                        $this->assertEquals($this->object->jsOnMaximize,null);
                        $this->object->jsOnMaximize="event";
                        $this->assertEquals($this->object->jsOnMaximize,"event");
                }

                function test_jsOnClose()
                {
                        $this->assertEquals($this->object->jsOnClose,null);
                        $this->object->jsOnClose="event";
                        $this->assertEquals($this->object->jsOnClose,"event");
                }

                function test_jsOnMove()
                {
                        $this->assertEquals($this->object->jsOnMove,null);
                        $this->object->jsOnMove="event";
                        $this->assertEquals($this->object->jsOnMove,"event");
                }

/*                function test_dumpJSEvents()
                {
                        $this->object->setjsOnMinimize("myonminimize");
                        $this->object->setjsOnRestore("myonrestore");
                        $this->object->setjsOnMaximize("myonmaximize");
                        $this->object->setjsOnClose("myonclose");
                        $this->object->setjsOnMove("myonmove");
                        $this->object->setjsOnResize("myonresize");

                        ob_start();
                        $this->object->dumpJSEvents();
                        $c=ob_get_contents();
                        ob_clean();
                        print_r($c);

                        $this->assertEquals(true,true);


                }

                */
                function test_dumpJavascript()
                {
/*
                        $this->setup();
                        ob_start();
                        $this->stowner->insertComponent($this->object);
                        $this->object->setjsOnMinimize("myonminimize");
                        $this->object->setjsOnRestore("myonrestore");
                        $this->object->setjsOnMaximize("myonmaximize");
                        $this->object->setjsOnClose("myonclose");
                        $this->object->setjsOnMove("myonmove");
                        $this->object->setjsOnResize("myonresize");
                        $this->object->dumpJavascript();
                        $c=ob_get_contents();
                        ob_end_clean();
                        //print_r($c);
                        $c=$this->fixString($c);
                        $real='function AnotherEvent(event)
{
var event = event || window.event;
var params=null;

}

function CheckListBoxClick(name, index)
{
  var event = event || window.event;
  var obj=document.getElementById(name);
  if (obj) {
    if (!obj.disabled) {
      obj.checked = !obj.checked;
      return obj.onclick();
    }
  }
  return false;
}';
                        $this->stowner->removeComponent($this->object);
                        $this->assertEquals($this->cleanString($c),$this->cleanString($real),'Testing if dump javascript generate the right code');
*/
                }

        }


        if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_window.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "WindowTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
