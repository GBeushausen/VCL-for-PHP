<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_control.inc.php";
        use_unit("controls.inc.php");
        use_unit("extctrls.inc.php");

        class PaintBoxTest extends ControlTest
        {
                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;
                        $this->object=new PaintBox();
                        $this->object->name="myobject";
                }

                function test_OnClick()
                {
                  $this->defaultTest('OnClick',null,'test');
                }

                function test_OnDblClick()
                {
                  $this->defaultTest('OnClick',null,'test');
                }

                function test_readCanvas()
                {
                        $obj=new Canvas();
                        $a='PaintBox';
                        $this->assertEquals($this->object->Canvas!=null,true,"a");
                        $this->object->classNameIs($a);
                        $this->assertEquals($a,'PaintBox',"b");
                }

                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        //print_r($c);
                        $c=$this->cleanString($c);
                        $compare=$this->cleanString('<divid="myobject"title=""style="height:100px;width:100px;"><scripttype="text/javascript">var=newjsGraphics("");.paint();</script></div>');
                        $this->assertEquals(trim($c),$compare);
                }


                function test_getOnPaint()
                {
                        //tested within test_OnPaint
                }

                function test_setOnPaint()
                {
                        //tested within test_OnPaint
                }
                 function test_OnPaint()
                {
                        $this->aowner->insertComponent($this->object);

                        $this->aowner->executed=false;

                        $this->object->OnPaint="DummyEvent";
                        $this->aowner->executed=false;
                        $this->object->dumpContents();
                        $this->assertEquals($this->aowner->executed,true,'Init test 1');

                        $this->aowner->removeComponent($this->object);

                }
                function test_dumpForAjax()
                {
                        ob_start();
                        $this->object->dumpForAjax();
                        $contents=ob_get_contents();
                        ob_end_clean();
                        //print_r($contents);
                        $expected='myobject_Canvas.paint();';
                        $this->assertEquals($expected,trim($contents));
                }

                function test_dumpHeaderCode()
                {
                        ob_start();
                        $this->object->dumpHeaderCode();
                        $contents=ob_get_contents();
                        ob_end_clean();
                        $expected='<script type="text/javascript" src="/vcl-bin/walterzorn/wz_jsgraphics.js"></script>';
                        $this->assertEquals($expected,trim($contents));
                }

                function test_getPopupMenu() {}
                function test_setPopupMenu() {}

        }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_paintbox.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "PaintBoxTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>



















