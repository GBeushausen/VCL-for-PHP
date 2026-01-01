<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_focuscontrol.inc.php";
        use_unit("stdctrls.inc.php");

        class DWidgetTest extends FocusControlTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new DWidget();
                        $this->object->Name="myobject";
                }

                function test_dumpHeaderCode()
                {
                        ob_start();
                        $this->object->dumpHeaderCode();
                        $c=ob_get_contents();
                        $c=$this->cleanString($c);
                        //print_r($c);
                        $compare=$this->cleanString("<scripttype=\"text/javascript\"src=\"/vcl-bin/dynapi/src/dynapi.js\"></script><scripttype=\"text/javascript\">dynapi.library.setPath('/vcl-bin/dynapi/src/');dynapi.library.include('dynapi.api');</script><scripttype=\"text/javascript\">dynapi.library.include('');</script>");
                        $this->assertEquals($c,$compare);
                }

                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        //print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim('<script type="text/javascript">
  dynapi.document.insertChild(myobject);
</script>');
                        $this->assertEquals(trim($c),$compare);
                }


                function test_ControlStyle($styles=array())
                {
                        $styles["csSlowRedraw"]=1;
                        parent::test_ControlStyle($styles);
                }


                function test_readDWidgetClassName()
                {
                        //tested in DWidgetClassName
                }

                function test_writeDWidgetClassName()
                {
                        //tested in DWidgetClassName
                }
                function test_DWidgetClassName()
                {
                        $this->object->writeDWidgetClassName("MyWidgetClass");
                        $this->assertEquals($this->object->readDWidgetClassName(),"MyWidgetClass");
                }


      }

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_dwidget.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "DWidgetTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
