<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_focuscontrol.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");

        class frameTest extends focuscontrolTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new frame();
                        $this->object->name="myobject";
                }

                function test_ControlStyle($styles=array())
                {
                        $styles["csAcceptsControls"]=1;
                        parent::test_ControlStyle($styles);
                }

                function test_Align()
                {
                        $this->assertEquals($this->object->Align,'alLeft');
                        $this->assertEquals($this->object->Align,$this->object->defaultAlign());
                        $this->object->Align=alClient;
                        $this->assertEquals($this->object->Align,'alClient');
                }

                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim('<frame src="" name="myobject" id="myobject" scrolling="auto"  marginwidth="0" marginheight="0" frameborder="1"    >');
                        $this->assertEquals(trim($c),$compare);
                }

                function test_jsonload()
                {
                        $this->assertEquals($this->object->jsonload,null);
                        $this->assertEquals($this->object->jsonload,$this->object->defaultjsOnLoad());
                        $this->object->jsonload="myevent";
                        $this->assertEquals($this->object->jsonload,"myevent");
                }
                function test_source()
                {
                        $this->assertEquals($this->object->source,"");
                        $this->assertEquals($this->object->source,$this->object->defaultSource());
                        $this->object->source="page.html";
                        $this->assertEquals($this->object->source,"page.html");
                }
                function test_borders()
                {
                        $this->assertEquals($this->object->borders,1);
                        $this->assertEquals($this->object->borders,$this->object->defaultBorders());
                        $this->object->borders=0;
                        $this->assertEquals($this->object->borders,0);
                }
                function test_MarginWidth()
                {
                        $this->assertEquals($this->object->MarginWidth,0);
                        $this->assertEquals($this->object->MarginWidth,$this->object->defaultMarginWidth());
                        $this->object->MarginWidth=15;
                        $this->assertEquals($this->object->MarginWidth,15);
                }
                function test_MarginHeight()
                {
                        $this->assertEquals($this->object->MarginHeight,0);
                        $this->assertEquals($this->object->MarginHeight,$this->object->defaultMarginHeight());
                        $this->object->MarginHeight=10;
                        $this->assertEquals($this->object->MarginHeight,10);
                }
                function test_Resizeable()
                {
                        $this->assertEquals($this->object->Resizeable,1);
                        $this->assertEquals($this->object->Resizeable,$this->object->defaultResizeable());
                        $this->object->Resizeable=0;
                        $this->assertEquals($this->object->Resizeable,0);
                }

                function test_FrameJSEvents()
                {
                        $this->assertEquals($this->object->FrameJSEvents(),"");
                        $this->object->jsOnLoad="mionload";
                        $this->assertEquals($this->object->FrameJSEvents(),' onload="return mionload(event)" ');
                }


                function test_setSource()
                {
                        //tested within test_source();
                }

                function test_getSource()
                {
                        //tested within test_source();
                }

                function test_setBorders()
                {
                        //tested within test_borders();
                }

                 function test_getBorders()
                {
                        //tested within test_borders();
                }
                 function test_getMarginWidth()
                {
                        //tested within test_marginwidth();
                }
                 function test_setMarginWidth()
                {
                        //tested within test_marginwidth();
                }
                 function test_getMarginHeight()
                {
                        //tested within test_marginheight();
                }
                 function test_setMarginHeight()
                {
                        //tested within test_marginheight();
                }
                 function test_getResizeable()
                {
                        //tested within test_resizeable();
                }
                 function test_setResizeable()
                {
                        //tested within test_resizeable();
                }
                 function test_getScrolling()
                {
                }
                 function test_setScrolling()
                {
                }
                function test_Scrolling()
                {
                        $this->assertEquals($this->object->Scrolling,fsAuto);
                        $this->assertEquals($this->object->Scrolling,$this->object->defaultScrolling());
                        $this->object->Scrolling=fsYes;
                        $this->assertEquals($this->object->Scrolling,fsYes);
                }

                function test_getjsOnLoad()
                {
                        //tested within test_jsonload();
                }
                 function test_setjsOnLoad()
                {
                        //tested within test_jsonload();
                }

                //tested within test_LongDesc();
                function test_getLongDesc(){}
                //tested within test_LongDesc();
                function test_setLongDesc(){}
                function test_LongDesc()
                {
                        $this->assertEquals($this->object->LongDesc,$this->object->defaultLongDesc(),"a");
                        $this->object->LongDesc="mylongdesc";
                        $this->assertEquals($this->object->LongDesc,'mylongdesc',"b");

                }
                //tested within test_Title()
                function test_getTitle(){}
                //tested within test_Title()
                function test_setTitle(){}
                function test_Title()
                {
                        $this->assertEquals($this->object->Title,$this->object->defaultTitle(),"a");
                        $this->object->Title="myTitle";
                        $this->assertEquals($this->object->Title,'myTitle',"b");
                }
        }
        if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
            else $script=$_GET['script'];

        if (basename($script)=='test_frame.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "frameTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
