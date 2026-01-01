<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_qwidget.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("comctrls.inc.php");

        class ColorSelectorTest extends QWidgetTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new ColorSelector();
                        $this->object->Name="myobject";
                }

                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim('<input type="hidden" id="myobject_state" name="myobject_state" value="" />
<script type="text/javascript">
  var myobject = new qx.ui.component.ColorSelector();
        myobject.setLeft(0);
        myobject.setTop(0);
        myobject.setWidth(557);
        myobject.setHeight(314);
  myobject.setBackgroundColor(\'#EBE9ED\');
  myobject.setVisibility(true);
</script>');
                        $this->assertEquals(trim($c),$compare);
                }

                function test_addOtherChildren()
                {
                        if($this->object->className($this)!="ColorSelector")
                                $this->assertEquals(true,false);

                }

}

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_colorselector.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "ColorSelectorTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
