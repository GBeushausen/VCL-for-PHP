<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_customedit.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("comctrls.inc.php");
        use_unit("extctrls.inc.php");

        class CustomMemoTest extends CustomEditTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new CustomMemo();
                        $this->object->name="myobject";
                }

                function test_updateLinesCase()
                {
                      //This is tested on the charcase property test
                }

                function test_preinit()
                {

                        $_POST['myobject']="this is the text I´m uploading";
                        $this->object->preinit();
                        $this->assertEquals($this->object->Text,"this is the text I´m uploading","Check Test 1");
                        //we check conversion of specialchars
                        $this->object->AsSpecialChars=true;
                        $_POST['myobject']="this is the text I´m uploading";
                        $this->object->preinit();
                        $this->assertEquals($this->object->Text, "this is the text I´m uploading","Check Test 2");
                }

                function test_dumpJavascript()
                {
                        $this->object->MaxLength=20;
                        ob_start();
                        $this->object->dumpJavascript();
                        $c=ob_get_contents();
                        ob_clean();
                        //print_r($c);
                        $expected="function checkMaxLength(obj, event, onKeyUpFunc){
  var maxlength = obj.getAttribute ? parseInt(obj.getAttribute(\"maxlength\")) : \"\";
  if (obj.getAttribute && obj.value.length > maxlength)
    obj.value = obj.value.substring(0, maxlength);
  if (onKeyUpFunc != null)
    onKeyUpFunc(event);
}";
                        $this->assertEquals($this->cleanString($c),$this->cleanString($expected));

                }

                function test_jsOnSelect()
                {
                        $this->assertEquals($this->object->jsOnSelect,null);
                        $this->assertEquals($this->object->jsOnSelect,$this->object->defaultjsOnSelect());
                        $this->object->jsOnSelect='dummyEvent';
                        $this->assertEquals($this->object->jsOnSelect,'dummyEvent');
                }
                function test_Add()
                {
                        $line="my first line";
                        $line2="my second line";
                        $this->object->Add($line);
                        $this->object->Add($line2);
                        $text=$this->object->Text;
                        $this->assertEquals($line."\n".$line2,$text,"a");
                        $this->object->Clear();
                        $this->assertEquals($this->object->Text,"","b");

                }

                function test_Clear()
                {
                        $this->test_Add();
                }
                function test_readLineSeparator()
                {
                        //tested in test_LineSeparator
                }
                function test_writeLineSeparator()
                {
                          //tested in test_LineSeparator
                }
                 function test_LineSeparator()
                {
                        $this->assertEquals($this->object->readLineSeparator(),"\n");
                        $this->object->LineSeparator="||";
                        $this->assertEquals($this->object->LineSeparator,"||");
                }
                 function test_LinesAsHTML()
                {
                        $this->setup();
                        $line="my first line";
                        $line2="my second line";
                        $this->object->Add($line);
                        $this->object->Add($line2);
                        $html=$this->object->LinesAsHTML();
                        $this->assertEquals($html,"my first line<br />\nmy second line");
                }
                function test_Lines()
                {
                        $this->assertEquals($this->object->Lines, array(),"a");
                        $this->assertEquals($this->object->Lines,$this->object->defaultLines(),"b");
                        $array=array(0=>"test1",1=>"test2");
                        $this->object->Lines=$array;
                        $this->assertEquals($text=$this->object->Lines,$array,"c");
                }

                function test_WordWrap()
                {
                        $this->assertEquals($this->object->WordWrap,1);
                        $this->assertEquals($this->object->WordWrap,$this->object->defaultWordWrap());
                        $this->object->WordWrap=false;
                        $this->assertEquals($this->object->WordWrap,false);
                }

                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        //print_r($c);
                        $compare=trim('<textarea id="myobject" name="myobject" style=" font-family: Verdana; font-size: 10px;  height:88px;width:185px;"    tabindex="0"    wrap="virtual"></textarea>');
                        $this->assertEquals(trim($c),$compare);

                         //Testing SourceForge #1719982
                        $parent=new PageControl();
                        $parent->Name="PageControl";
                        $parent->Tabs=array("Tab1","Tab2");
                        $parent->Visible=true;
                        $parent->ActiveLayer="Tab1";
                        $this->object->Parent=$parent;
                        $this->object->Lines=array("this","is","a","test");
                        $this->object->Visible=true;
                        $this->object->Layer="Tab1";
                        $c=$parent->show(true);
                        $c=$this->cleanString($c);
                        $compare=$this->cleanString('<input type="hidden" id="PageControl_state" name="PageControl_state" value="" /> <script type="text/javascript"> var PageControl = new qx.ui.pageview.tabview.TabView; PageControl.setLeft(0); PageControl.setTop(0); PageControl.setWidth(300); PageControl.setHeight(400); PageControl.setPlaceBarOnTop(true); var tabPageControl_1 = new qx.ui.pageview.tabview.Button("Tab1"); var tabPageControl_2 = new qx.ui.pageview.tabview.Button("Tab2"); tabPageControl_1.setChecked(true); PageControl.getBar().add(tabPageControl_1,tabPageControl_2); var pagePageControl_1 = new qx.ui.pageview.tabview.Page(tabPageControl_1); var pagePageControl_2 = new qx.ui.pageview.tabview.Page(tabPageControl_2); PageControl.getPane().add(pagePageControl_1,pagePageControl_2); var container = new qx.ui.basic.Atom("<div id=\"myobject_outer\">\n<textarea id=\"myobject\" name=\"myobject\" style=\" font-family: Verdana; font-size: 10px; height:88px;width:185px;\" tabindex=\"0\" wrap=\"virtual\">this\nis\na\ntest</textarea></div>\n"); container.setLeft(-11); container.setTop(-31); container.setWidth(185); container.setHeight(89); pagePageControl_1.add(container); container.addEventListener("appear", function(e) { }, container); PageControl.setVisibility(true); </script> ');
                        $this->assertEquals($c,$compare);
                }

                function test_AddLine()
                {
                        $this->test_Add();
                }

                //Tested in test_Property()
                function test_getAsSpecialChars(){}
                function test_setAsSpecialChars(){}
                function test_AsSpecialChars()
                {
                        $this->assertEquals($this->object->AsSpecialChars,$this->object->defaultAsSpecialChars());
                        $this->object->AsSpecialChars=1;
                        $this->assertEquals($this->object->AsSpecialChars,1);
                }
        }

        if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_custommemo.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "CustomMemoTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
