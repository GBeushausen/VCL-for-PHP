<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_focuscontrol.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("comctrls.inc.php");

        class DateTimePickerTest extends FocusControlTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new DateTimePicker();
                        $this->object->Name="myobject";
                }

                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        //print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim('<table cellpadding="0" cellspacing="0" border="0"><tr><td><input style=" font-family: Verdana; font-size: 10px;  height:16px;width:185px;" name="myobject" value="" id="f-calendar-field-1" type="text" /></td><td><a href="#" id="f-calendar-trigger-1"><img align="absmiddle" border="0" src="/vcl-bin/jscalendar/img.gif" alt="" /></a></td></tr></table><script type="text/javascript">var dcalendar=Calendar.setup({"ifFormat":"%Y-%m-%d %I:%M","daFormat":"%Y/%m/%d","firstDay":1,"showsTime":true,"showOthers":true,"timeFormat":24,"inputField":"f-calendar-field-1","button":"f-calendar-trigger-1"});</script>');
                        $this->assertEquals(trim($c),$compare);
                }

                function test_preinit()
                {
                        $_POST['myobject']="this is my text";
                        $this->object->preinit();
                        $this->assertEquals($this->object->Text, "this is my text");

                }

                function test_dumpHeaderCode()
                {
                        ob_start();
                        $this->object->dumpHeaderCode();
                        $c=ob_get_contents();
                        ob_clean();
                        $compare=$this->cleanString("<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"/vcl-bin/jscalendar/calendar-win2k-2.css\" /> <script type=\"text/javascript\" src=\"/vcl-bin/jscalendar/calendar.js\"></script> <script type=\"text/javascript\" src=\"/vcl-bin/jscalendar/lang/calendar-en.js\"></script> <script type=\"text/javascript\" src=\"/vcl-bin/jscalendar/calendar-setup.js\"></script> ");
                        $this->assertEquals($this->cleanString($c),$compare);

                }

                function test_getTimeZone() { /*tested within test_Property*/ }
                function test_setTimeZone() { /*tested within test_Property*/ }
                function test_getText() { /*tested within test_Property*/ }
                function test_setText() { /*tested within test_Property*/ }
                function test_getShowsTime() { /*tested within test_Property*/ }
                function test_setShowsTime() { /*tested within test_Property*/ }
                function test_getIfFormat() { /*tested within test_Property*/ }
                function test_setIfFormat() { /*tested within test_Property*/ }

                function test_TimeZone()
                {
                        $this->assertEquals($this->object->defaultTimeZone(),$this->object->TimeZone);
                        $this->object->TimeZone="UFC";
                        $this->assertEquals($this->object->TimeZone, "UFC");
                }
                function test_Text()
                {
                        $this->assertEquals($this->object->defaultText(),$this->object->Text);
                        $this->object->Text="MyText";
                        $this->assertEquals($this->object->Text, "MyText");
                }
                function test_ShowsTime()
                {
                        $this->assertEquals($this->object->defaultShowsTime(),$this->object->ShowsTime);
                        $this->object->ShowsTime=false;
                        $this->assertEquals($this->object->ShowsTime, false);
                }
                function test_IfFormat()
                {
                        $this->assertEquals($this->object->defaultIfFormat(),$this->object->IfFormat);
                        //not really a valid format, but just to check if it sis correctly set/read
                        $this->object->IfFormat="myformat";
                        $this->assertEquals($this->object->IfFormat, "myformat");
                }

                function test_getParentFont(){}
                function test_setParentFont(){}


        }

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_datetimepicker.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "DateTimePickerTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
