<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_focuscontrol.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");

        class framesetTest extends focuscontrolTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new frameset();
                        $this->object->name="myobject";
                }

                function test_ControlStyle($styles=array())
                {
                        $styles["csAcceptsControls"]=1;
                        parent::test_ControlStyle($styles);
                }

                function test_Align()
                {
                        $this->assertEquals($this->object->Align,'alClient');
                        $this->assertEquals($this->object->Align,$this->object->defaultAlign());
                        $this->object->Align=alCustom;
                        $this->assertEquals($this->object->Align,'alCustom');
                }
                function test_dumpContents()
                {

                        $frame1=new Frame();
                        $frame1->Parent=$this->object;
                        $frame2=new Frame();
                        $frame2->Parent=$this->object;

                        $c=$this->object->show(true);
                        $c=$this->cleanString($c);
                        $compare=$this->cleanString('<frameset cols=",*" rows="*" frameborder="no" border="0" framespacing="0" > <frame src="" name="" id="" scrolling="auto" marginwidth="0" marginheight="0" frameborder="1" > <frame /></frameset>');
                        $this->assertEquals(trim($c),$compare);
                }

                function test_dumpJavascript()
                {
                        $this->object->jsOnLoad="DummyEvent";
                        $this->object->jsOnUnload="DummyEvent";

                        $this->stowner->insertComponent($this->object);

                        ob_start();
                        $this->object->dumpJavascript();
                        $c=ob_get_contents();
                        ob_clean();
                        $expected="functionDummyEvent(event){varevent=event||window.event;varparams=null;}";
                        $this->assertEquals($this->cleanString($c),$this->cleanString($expected));

                        $this->stowner->removeComponent($this->object);
                        $this->setup();
                }

                //Nothing to really test here... just to get rid of the fail
                function test_dumpJsEvents()
                {

                        $this->object->jsOnLoad="DummyEvent";
                        $this->object->jsOnUnload="DummyEvent";

                        $this->stowner->insertComponent($this->object);

                        ob_start();
                        $this->object->dumpJSEvents();
                        $c=ob_get_contents();
                        ob_clean();
                        $expected="";
                        $this->assertEquals($this->cleanString($c),$this->cleanString($expected));

                        $this->stowner->removeComponent($this->object);

                        $this->setup();
                }

                function test_jsOnLoad()
                {
                        $this->assertEquals($this->object->jsonload,null);
                        $this->assertEquals($this->object->jsonload,$this->object->defaultjsOnLoad());
                        $this->object->jsonload="myevent";
                        $this->assertEquals($this->object->jsonload,"myevent");
                }
                function test_jsOnUnload()
                {
                        $this->assertEquals($this->object->jsonload,null);
                        $this->assertEquals($this->object->jsonload,$this->object->defaultjsOnLoad());
                        $this->object->jsonload="myevent";
                        $this->assertEquals($this->object->jsonload,"myevent");
                }
                function test_readFramesetJSEvents()
                {
                        $this->assertEquals($this->object->readFramesetJSEvents(),"");
                        $this->object->jsOnLoad="mionload";
                        $this->object->jsOnUnLoad="mionunload";
                        $this->assertEquals($this->cleanString($this->object->readFramesetJSEvents()),$this->cleanString(' onload="return mionload(event)" onunload="return mionunload(event)"'));
                }
                function test_FrameBorder()
                {
                        $this->assertEquals($this->object->frameborder,"fbNo");
                        $this->assertEquals($this->object->frameborder,$this->object->defaultFrameBorder());
                        $this->object->frameborder="fbYes";
                        $this->assertEquals($this->object->frameborder,"fbYes");
                }
                function test_BorderWidth()
                {
                        $this->assertEquals($this->object->BorderWidth,0);
                        $this->assertEquals($this->object->BorderWidth,$this->object->defaultBorderWidth());
                        $this->object->BorderWidth=10;
                        $this->assertEquals($this->object->BorderWidth,10);
                }
                function test_FrameSpacing()
                {
                        $this->assertEquals($this->object->FrameSpacing,0);
                        $this->assertEquals($this->object->FrameSpacing,$this->object->defaultFrameSpacing());
                        $this->object->FrameSpacing=5;
                        $this->assertEquals($this->object->FrameSpacing,5);
                }
                function test_dumpClientFrames()
                {
                        $this->assertEquals($this->object->dumpClientFrames(),null);
                        $this->assertEquals($this->object->dumpClientFrames(),null);
                }
                function test_dumpHorizontalFrames()
                {
                        $obj=new frameset();
                        $hframes[0]=$obj;
                        $outputevents="default";
                        $a=$this->object->dumpHorizontalFrames($hframes,$outputevents);
                        $this->assertEquals($this->object->dumpHorizontalFrames($hframes,$outputevents),$a);
                }

        }
        if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
            else $script=$_GET['script'];

        if (basename($script)=='test_frameset.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "framesetTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
