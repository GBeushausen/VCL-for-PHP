<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_focuscontrol.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("comctrls.inc.php");

        class MonthCalendarTest extends FocusControlTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new MonthCalendar();
                        $this->object->Name="myobject";
                }

                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        //print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim('<div id="myobject_container">
<script type="text/javascript">var myobject=Calendar.setup({"ifFormat":"%d/%m/%Y %H:%i:%s %P","daFormat":"%Y/%m/%d","flat":"myobject_container","firstDay":1,"showsTime":true,"width":200,"height":200,"showOthers":true,"timeFormat":12});</script>
</div>
<script type="text/javascript">
  myobject.table.width=\'197px\';
  myobject.table.height=\'197px\';
</script>');
                        $this->assertEquals(trim($c),$compare);
                }

                function test_dumpHeaderCode()
                {
                        ob_start();
                        $this->object->dumpHeaderCode();
                        $c=ob_get_contents();
                        ob_clean();
                        $expected=$this->cleanString("<linkrel=\"stylesheet\"type=\"text/css\"media=\"all\"href=\"/vcl-bin/jscalendar/calendar-win2k-2.css\"/><scripttype=\"text/javascript\"src=\"/vcl-bin/jscalendar/calendar.js\"></script><scripttype=\"text/javascript\"src=\"/vcl-bin/jscalendar/lang/calendar-en.js\"></script><scripttype=\"text/javascript\"src=\"/vcl-bin/jscalendar/calendar-setup.js\"></script>");
                        $this->assertEquals($this->cleanString($c),$expected);

                }

         }

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_monthcalendar.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "MonthCalendarTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>