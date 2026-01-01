<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_control.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");


        class ShapeTest extends ControlTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new Shape();
                        $this->object->Name="myobject";
                }

                function test_dumpHeaderCode()
                {
                        ob_start();
                        $this->object->dumpHeaderCode();
                        $c=ob_get_contents();
                        $c=$this->cleanString($c);
                        $compare=$this->cleanString('<script type="text/javascript" src="/vcl-bin/walterzorn/wz_jsgraphics.js"></script>');
                        $this->assertEquals($c,$compare);
                }

                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim('<script type="text/javascript">
  var  = new jsGraphics("");
  .setColor("#FFFFFF");
  .fillRect(1, 1, 63 - 1, 63 - 1);
  .fillRect(1, 1, 63 - 1 + 1, 63 - 1 + 1);
  .setStroke(1);
  .setColor("#000000");
  .drawRect(1, 1, 63 - 1 + 1, 63 - 1 + 1);
  .paint();
</script>');
                        $this->assertEquals(trim($c),$compare);
                }


                function test_readShape()
                {
                        //Tested within test_Shape()
                }
                function test_writeShape()
                {
                        //Tested within test_Shape()
                }
                function test_getShape()
                {
                        //Tested within test_Shape()
                }
                function test_setShape()
                {
                        //Tested within test_Shape()
                }
                function test_Shape()
                {
                        $this->assertEquals($this->object->Shape,"stRectangle");
                        $this->object->Shape="stCircle";
                        $this->assertEquals($this->object->Shape,"stCircle");
                }
                function test_getPen()
                {
                        //Tested within test_Shape()
                }
                function test_setPen()
                {
                        //Tested within test_Shape()
                }
                function test_Pen()
                {
                        $pen=new Pen();
                        $pen2=$this->object->Pen;
                        //to make both equal we must null this. recursive problem if comparing
                        //directly
                        $pen2->_control=null;
                        $this->assertEquals($pen2,$pen,"a");
                }

                function test_getBrush()
                {
                        //Tested within test_Shape()
                }
                function test_setBrush()
                {
                        //Tested within test_Shape()
                }
                function test_Brush()
                {
                        $brush=new Brush();
                        $brush2=$this->object->Brush;
                        //to make both equal we must null this. recursive problem if comparing
                        //directly
                        $brush2->_control=null;
                        $this->assertEquals($brush2,$brush,"a");

                }

        }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_shape.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "ShapeTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>
