<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_panel.inc.php";
        use_unit("clock.inc.php");



        class ClockTest extends PanelTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new Clock();
                        $this->object->Name="myobject";
                }

                function test_ParentFont()
                {
                        //Initially set
                        $this->assertEquals($this->object->ParentFont,1);
                        $this->assertEquals($this->object->ParentFont,$this->object->defaultParentFont());

                      //Modifying the font, must modify ParentFont
                        $font=$this->object->Font;
                        $font->Color="#FF0000";
                        $this->assertEquals($this->object->ParentFont,0);

                        //Now the font string must be modified
                        $font_string=$this->object->Font->FontString;
                        $this->assertEquals(trim($font_string),trim('font-family: Verdana; font-size: 10px; color: #FF0000;text-align: center;'),'Font must be the modified one, including color');
                        //Setting parentFont must work
                        $this->object->ParentFont=1;

                        $this->assertEquals($this->object->ParentFont,1);

                        //Setting the parent of the object, the fontstring must be the parent's
                        $parent=new Panel();
                        $parent->Name="testParent";
                        $this->object->Parent=$parent;
                        $font_string=$this->object->Font->FontString;
                        $this->assertEquals(trim($font_string),trim('font-family: Verdana; font-size: 10px;'),'Font must match with the parent font');

                        $font=$parent->Font;
                        $font->Color="#FF00FF";
                        $font_string=$this->object->Font->FontString;
                        $this->assertEquals(trim($font_string),trim('font-family: Verdana; font-size: 10px; color: #FF00FF;'),'Font must match with the modified parent font');
                }

                function test_dumpHeaderCode()
                {
                        ob_start();
                        $this->object->dumpHeaderCode();
                        $c=ob_get_contents();
                        ob_end_clean();
                        $c=$this->fixString($c);
                        $this->assertEquals('<script type="text/javascript" src="/vcl-bin/dynapi/src/dynapi.js"></script>
<script type="text/javascript">
dynapi.library.setPath(\'/vcl-bin/dynapi/src/\');
dynapi.library.include(\'dynapi.api\');
dynapi.library.include(\'TemplateManager\');
dynapi.library.include(\'HTMLClock\');
</script>',$c,'Header code initialization');
                }

                function test_dumpJavascript()
                {
                        //Nothing, is tested by dumpJsEvents
                }

                function test_Alarm()
                {
                        $this->assertEquals($this->object->Alarm,$this->object->defaultAlarm());
                        $this->object->Alarm="myalarm";
                        $this->assertEquals($this->object->Alarm,"myalarm");
                }

                function test_jsOnAlarm()
                {
                        $this->assertEquals($this->object->jsOnAlarm,$this->object->defaultjsOnAlarm());
                        $this->object->jsOnAlarm="alarmevent";
                        $this->assertEquals($this->object->jsOnAlarm, "alarmevent");
                }

                function test_dumpJsEvents()
                {
                        //Test generation of onalarm event
                        ob_start();
                        $this->stowner->insertComponent($this->object);
                        $this->object->jsOnAlarm='AnotherEvent';
                        $this->object->dumpJSEvents();
                        $c=ob_get_contents();
                        ob_end_clean();
                        $c=str_replace("\n","",$c);
                        $this->stowner->removeComponent($this->object);
                        $this->assertEquals(trim($c),'function AnotherEvent(event){var event = event || window.event;var params=null;}','Testing if dumpJsEvents generate correct code');
                }

                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim('<script type="text/javascript">
var myobject = new Template(\'<table id="myobject_table"  width="90"   height="90"  border="0"  cellpadding="0" cellspacing="0"     style=" border: 0px solid ; "   ><tr><td style=" font-family: Verdana; font-size: 10px; text-align: center; "><span style=" font-family: Verdana; font-size: 10px; text-align: center; "><td align="center">{@fld}</td></span></td></tr></table>\',0,0,90,90,\'\');
myobject.addChild(new HTMLClock(),\'fld\');
dynapi.document.addChild(myobject);
</script>');
                        $this->assertEquals(trim($c),$compare);
                }

                function test_ControlStyle($styles=array())
                {
                        $styles["csDesignEncoding"]="ISO-8859-1";
                        $styles["csAcceptsControls"]="0";
                        $styles["csSlowRedraw"]="1";

                        parent::test_ControlStyle($styles);
                }

                function test_Font()
                {
                        $this->assertEquals(is_object($this->object->Font),true);
                        $font_string=$this->object->Font->FontString;
                        $this->assertEquals(trim($font_string),trim('font-family: Verdana; font-size: 10px; text-align: center;'));
                }

        }
      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_clock.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "ClockTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
