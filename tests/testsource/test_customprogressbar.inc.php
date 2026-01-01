<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_dwidget.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("comctrls.inc.php");

        class CustomProgressBarTest extends DWidgetTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new CustomProgressBar();
                        $this->object->Name="myobject";
                }
                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim('<script type="text/javascript">
  dynapi.document.insertChild(myobject);
</script>');
                        $this->assertEquals(trim($c),$compare);
                }

                function test_dumpHeaderCode()
                {
                        ob_start();
                        $this->object->dumpHeaderCode();
                        $c=ob_get_contents();
                        $c=$this->cleanString($c);
                        //print_r($c);
                        $compare=$this->cleanString("<scripttype=\"text/javascript\"src=\"/vcl-bin/dynapi/src/dynapi.js\"></script><scripttype=\"text/javascript\">dynapi.library.setPath('/vcl-bin/dynapi/src/');dynapi.library.include('dynapi.api');</script><scripttype=\"text/javascript\">dynapi.library.include('ProgressBar');</script><scripttype=\"text/javascript\">varmyobject=newProgressBar('horz',0,0,200,17,50);myobject.setRange(0,100);myobject.setValue(50);dynapi.document.addChild(myobject);</script>");
                        $this->assertEquals($c,$compare);
                }

                function test_StepBy()
                {
                        //checked in test_StepIt();
                }

                function test_StepIt()
                {
                        $this->assertEquals($this->object->Step,10,"a");
                        $this->object->Position=0;
                        $this->object->Step=1;
                        $this->object->StepIt();
                        $this->assertEquals($this->object->Step,1,"b");

                }

                function test_Position()
                {
                        //$this->assertEquals($this->object->Position,$this->object->defaultPosition());
                        $this->assertEquals($this->object->Position,50);
                        $this->object->Position=1;
                        $this->assertEquals($this->object->Position,1);
                }

                function test_Min()
                {
                        $this->assertEquals($this->object->Min,0);
                        $this->assertEquals($this->object->Min,$this->object->defaultMin());
                        $this->object->Min=1;
                        $this->assertEquals($this->object->Min,1);
                }

                function test_MaxLength()
                {
                        $this->assertEquals($this->object->Max,100);
                        $this->assertEquals($this->object->Max,$this->object->defaultMax());
                        $this->object->Max=1;
                        $this->assertEquals($this->object->Max,1);
                }
         }

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_customprogressbar.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "CustomProgressBarTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
