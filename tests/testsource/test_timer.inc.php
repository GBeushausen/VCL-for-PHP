<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_component.inc.php";

        class TimerTest extends ComponentTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new Timer();
                        $this->object->name="myobject";
                }


                function test_getOnTimer()
                {
                        //tested within test_OnTimer
                }

                function test_setOnTimer()
                {
                        //tested within test_OnTimer
                }

                function test_jsOnTimer()
                {
                        $this->assertEquals($this->object->jsOnTimer,null);
                        $this->object->jsOnTimer="ontimer";
                        $this->assertEquals($this->object->jsOnTimer,"ontimer");
                }

                function test_Enabled()
                {
                        $this->object->Enabled=0;
                        $this->assertEquals($this->object->Enabled,0);
                }

                function test_getInterval()
                {
                        //tested within test_Interval
                }

                function test_setInterval()
                {
                        //tested within test_Interval
                }

                function test_Interval()
                {
                        $this->assertEquals($this->object->Interval,1000);
                        $this->object->Interval=10;
                        $this->assertEquals($this->object->Interval,10);
                }

                function test_dumpJavascript()
                {
                        $this->setup();
                        $this->object->jsOnTimer="DumpEvent";

                        ob_start();
                        $this->object->dumpJavascript();
                        $c=ob_get_contents();
                        ob_clean();
                        //print_r($c);
                        $expected='var myobject_TimerID = null;
  var myobject_OnLoad = null;


  function addEvent(obj, evType, fn)
  { if (obj.addEventListener)
    { obj.addEventListener(evType, fn, false);
      return true;
    }
    else if (obj.attachEvent)
    { var r = obj.attachEvent("on"+evType, fn);
      return r;
    } else {
      return false;
    }
  }

  function myobject_InitTimer()
  {  if (myobject_OnLoad != null) myobject_OnLoad();
     myobject_DisableTimer();
     myobject_EnableTimer();
  }

  function myobject_DisableTimer()
  {  if (myobject_TimerID)
     { clearTimeout(myobject_TimerID);
       myobject_TimerID  = null;
     }
  }

  function myobject_Event()
  {
  var event = event || window.event;
  if (myobject_TimerID)
    {  myobject_DisableTimer();
       DumpEvent(event);
       myobject_EnableTimer();
    }
  }

  function myobject_EnableTimer()
  { myobject_TimerID = self.setTimeout("myobject_Event()", 1000);
  }

  if (window.onload) myobject_OnLoad=window.onload;
  addEvent(window, \'load\', myobject_InitTimer);';
                        $this->assertEquals($this->cleanString($c),$this->cleanString($expected));
                }

        }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_timer.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "timerTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>
