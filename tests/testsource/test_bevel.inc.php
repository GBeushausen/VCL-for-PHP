<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_graphiccontrol.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");

        class BevelTest extends GraphicControlTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new Bevel();
                        $this->object->Name="myobject";
                }


                function test_dumpHeaderCode()
                {
                        ob_start();
                        $this->object->dumpHeaderCode();
                        $c=ob_get_contents();
                        ob_end_clean();
                        $c=$this->fixString($c);
                        $this->assertEquals('<script type="text/javascript" src="/vcl-bin/walterzorn/wz_jsgraphics.js"></script>',$c,'Header code initialization');
                }

                function test_dumpContents()
                {
                        $this->object->_canvas->InitLibrary();
                        $c=$this->object->show(true);
                        $c=$this->fixString($c);
                        $compare=trim('<script type="text/javascript">
  var myobject_Canvas = new jsGraphics("myobject_outer");
  myobject_Canvas.setStroke(1);
  myobject_Canvas.setColor("#000000");
  myobject_Canvas.setColor("#000000");
  myobject_Canvas.drawLine(0, 0, 0, 0);
  myobject_Canvas.drawLine(0, 0, 0, 0);
  myobject_Canvas.setColor("#EEEEEE");
  myobject_Canvas.drawLine(0, 0, 0, 0);
  myobject_Canvas.drawLine(0, 0, 0, 0);
  myobject_Canvas.paint();
</script>');
                        $this->assertEquals(trim($c),$compare);
                }



                function test_Shape()
                {
                        $this->assertEquals($this->object->Shape,"bsBox");
                        $this->object->Shape="bsFrame";
                        $this->assertEquals($this->object->Shape,"bsFrame");
                }

                function test_BevelStyle()
                {
                        $this->assertEquals($this->object->BevelStyle,"bsLowered","a");
                        $this->object->BevelStyle="bsFrame";
                        $this->assertEquals($this->object->BevelStyle,"bsFrame","b");
                }

         }
      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_bevel.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "BevelTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }
?>
