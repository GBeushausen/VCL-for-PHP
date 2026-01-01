<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_focuscontrol.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");

        class QWidgetTest extends FocusControlTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new QWidget();
                        $this->object->Name="myobject";
                }

                function test_dumpCommonQWidgetProperties()
                {
                        ob_start();
                        $this->object->dumpCommonQWidgetProperties($this->object->Name,1);
                        $actual=ob_get_contents();
                        ob_clean();

                        $expected=trim("myobject.setEnabled(true);
  myobject.setFont(\"10px 'Verdana' \");
  myobject.setVisibility(true);");
                        $this->assertEquals($expected,$this->fixString($actual));
                }
                function test_PrepareQWJSEvent()
                {
                        $this->test_dumpCommonQWidgetProperties();
                }

                function test_dumpCommonQWidgetJSEvents()
                {
                        ob_start();
                        $this->object->dumpCommonQWidgetJSEvents('QWidgetJSEvents', 2);
                        $c=ob_get_contents();
                        ob_end_clean();
                        $this->assertEquals($c,'');


                        $this->object->jsOnActivate= "focusinevent";
                        $this->object->jsOnDeActivate= "focusoutevent";
                        $this->object->jsOnBlur= "blurevent";
                        $this->object->jsOnClick= "clickevent";
                        $this->object->jsOnDblClick= "dblclickevent";
                        $this->object->jsOnFocus= "focusevent";
                        $this->object->jsOnKeyDown= "keydownevent";
                        $this->object->jsOnKeyPress= "keypressevent";
                        $this->object->jsOnKeyUp= "keyupevent";
                        $this->object->jsOnMouseDown= "mousedownevent";
                        $this->object->jsOnMouseUp= "mouseupevent";
                        $this->object->jsOnMouseMove= "mousemoveevent";
                        $this->object->jsOnMouseOut= "mouseoutevent";
                        $this->object->jsOnMouseOver= "mouseoverevent";
                        $this->object->jsOnContextMenu="OnContextMenuHandler";

                        ob_start();
                        $this->object->dumpCommonQWidgetJSEvents('QWidgetJSEvents', 2);
                        $actual=trim(ob_get_contents());
                        ob_end_clean();
                        $expected="QWidgetJSEvents.addEventListener('focusin',function(e){focusinevent(e);});QWidgetJSEvents.addEventListener('focusout',function(e){focusoutevent(e);});QWidgetJSEvents.addEventListener('blur',function(e){blurevent(e);});QWidgetJSEvents.addEventListener('click',function(e){clickevent(e);});QWidgetJSEvents.addEventListener('dblclick',function(e){dblclickevent(e);});QWidgetJSEvents.addEventListener('focus',function(e){focusevent(e);});QWidgetJSEvents.addEventListener('keydown',function(e){keydownevent(e);});QWidgetJSEvents.addEventListener('keypress',function(e){keypressevent(e);});QWidgetJSEvents.addEventListener('keyup',function(e){keyupevent(e);});QWidgetJSEvents.addEventListener('mousedown',function(e){mousedownevent(e);});QWidgetJSEvents.addEventListener('mouseup',function(e){mouseupevent(e);});QWidgetJSEvents.addEventListener('mousemove',function(e){mousemoveevent(e);});QWidgetJSEvents.addEventListener('mouseout',function(e){mouseoutevent(e);});QWidgetJSEvents.addEventListener('mouseover',function(e){mouseoverevent(e);});QWidgetJSEvents.addEventListener('contextmenu',function(e){OnContextMenuHandler(e);});";
                        $actual=$this->fixString($actual);
                        $this->assertEquals($this->cleanString($expected),$this->cleanString($actual));

                }


                function test_dumpCommonContentsTop()
                {
                        ob_start();
                        $this->object->dumpCommonContentsTop();
                        $actual=trim(ob_get_contents());
                        ob_end_clean();
                        $expected='<input type="hidden" id="myobject_state" name="myobject_state" value="" /><script type="text/javascript">';
                        $actual=trim($actual);
                        $actual=str_replace("\n","",$actual);
                        $this->assertEquals($expected,$actual);
                }

                function test_dumpCommonContentsBottom()
                {
                        ob_start();
                        $this->object->dumpCommonContentsBottom();
                        $actual=trim(ob_get_contents());
                        ob_end_clean();
                        $expected='</script>';
                        $actual=trim($actual);
                        $this->assertEquals($expected,$actual,"a");

                        $this->object->parent=new FocusControl();
                        ob_start();
                        $this->object->dumpCommonContentsBottom();
                        $actual=$this->fixString(ob_get_contents());
                        ob_end_clean();
                        $expected='d.add(inline_div);
  inline_div.add(myobject);
</script>';
                        $this->assertEquals($expected,$actual,"b");
                }

                function test_ControlStyle($styles=array())
                {
                        $styles["csTopOffset"]=1;
                        $styles["csLeftOffset"]=1;
                        $styles["csSlowRedraw"]=1;
                        parent::test_ControlStyle($styles);
                }

                function test_addOtherChildren()
                {
                        //nothing to test
                        if($this->object->className()!="QWidget")
                                $this->assertEquals(false,true);
                }

                function test_dumpContents()
                {
                        //Nothing to test
                        if($this->object->className()!="QWidget")
                                $this->assertEquals(false,true);
                }

                function test_dumpChildrenControls()
                {
                        $child=new QWidget();
                        $child->Name="MyChild";
                        $this->object->controls->items[]=$child;
                        ob_start();
                        ob_clean();
                        $this->object->dumpChildrenControls(10,10,"myparent",0);
                        $c=ob_get_contents();
                        ob_clean();
                        $expected='</script><script type="text/javascript">
  MyChild.setLeft(10);
  MyChild.setTop(10);
  myparent.add(MyChild);';
                        $c=$this->fixString($c);
                        $this->assertEquals($c,$expected);
                }
        }
      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_qwidget.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "QWidgetTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
