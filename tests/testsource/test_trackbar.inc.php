<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_customlistbox.inc.php";
        use_unit("controls.inc.php");
        use_unit("comctrls.inc.php");

        class TrackBarTest extends ControlTest
        {

                function setup()
                {
                        parent::Setup();

                        $this->object=new TrackBar();
//                        $this->object->owner=$this->stowner;
                        $this->object->Name="myobject";
                }

                function test_Position()
                {
                        $this->assertEquals($this->object->Position,0);
                        $this->assertEquals($this->object->Position,$this->object->defaultPosition());
                        $this->object->Position=1;
                        $this->assertEquals($this->object->Position,1);
                }

                function test_MaxPosition()
                {
                        $this->assertEquals($this->object->MaxPosition,10);
                        $this->assertEquals($this->object->MaxPosition,$this->object->defaultMaxPosition());
                        $this->object->MaxPosition=1;
                        $this->assertEquals($this->object->MaxPosition,1);
                }

                function test_dumpJsEvents()
                {
                        $this->stowner->insertComponent($this->object);
                        ob_start();
                        $this->object->jsOnChange='DummyEvent';
                        $this->object->dumpJSEvents();
                        $c=ob_get_contents();
                        ob_end_clean();
//                        print_r($c);
                        $expected="function DummyEvent(event)
{

var event = event || window.event;
var params=null;

}";
                        $this->assertEquals($this->cleanString($c),$this->cleanString($expected));
                        $this->stowner->removeComponent($this->object);
                }

                function test_dumpContents()
                {
                        $c=$this->object->show(true);
//                        print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim('<div id="myobject_Container" style="position:absolute;top:0px;left:0px;width:px;height:30px; background-image: url(\'/vcl-bin/jssliderbar/ruller.gif\'); background-repeat: repeat">
  <input type="hidden" id="myobject_Position" value="0" />
    <div id="myobject_Head" style="position:absolute;top:5px;left:0px;width:9px;height:17px;cursor:pointer;cursor:hand" onmousedown="return myobject_Handler(event);"><img src="/vcl-bin/jssliderbar/head.gif" style="height:17px;width:9px;border:0"/></div>
  </div>');
                        $this->assertEquals(trim($c),$compare);
                }

                function test_dumpHeaderCode()
                {
                        ob_start();
                        $this->object->dumpHeaderCode();
                        $c=ob_get_contents();
                        ob_clean();
                        //print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim('<script type="text/javascript" src="/vcl-bin/jssliderbar/ds_jssliderbar.js"></script>
<script type="text/javascript">
<!--
function myobject_Handler(event)
{
  var event = event || window.event;
  dsSliderMouseDown(event, \'myobject\', 10, null);
}
-->
</script>');
                        $this->assertEquals(trim($c),$compare);
                }


                function test_dumpJavascript()
                {
                        ob_start();
                        $this->object->dumpHeaderCode();
                        $c=ob_get_contents();
                        ob_clean();
                        //print_r($c);
                        $expected='<script type="text/javascript">
<!--
function myobject_Handler(event)
{
  var event = event || window.event;
  dsSliderMouseDown(event, \'myobject\', 10, null);
}
-->

</script>';
                        $this->assertEquals($this->cleanString($c),$this->cleanString($expected));
                }
        }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_trackbar.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "TrackBarTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>
