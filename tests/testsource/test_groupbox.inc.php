<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_qwidget.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");
        use_unit("menus.inc.php");

        class GroupBoxTest extends QWidgetTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new GroupBox();
                        $this->object->Name="myobject";
                }

                function test_PopupMenu()
                {
                  $p=new PopupMenu();
                  $this->defaultTest('PopupMenu',null,$p);
                }

                function test_ControlStyle($styles=array())
                {
                        $styles["csAcceptsControls"]=1;
                        parent::test_ControlStyle($styles);
                }

                 function test_dumpContents()
                {
                        $actual=$this->object->Show(true);
                        print_r($actual);
                        $expected='<input type="hidden" id="myobject_state" name="myobject_state" value="" />
<script type="text/javascript">
        var myobject    = new qx.ui.groupbox.GroupBox("");
        myobject.setLeft(0);
        myobject.setTop(0);
        myobject.setWidth(200);
        myobject.setHeight(200);
</script>';
                        $actual=$this->fixString($actual);
                        $this->assertEquals($actual,$expected);
                }

                function test_addOtherChildren()
                {
                        if ($this->object->className()!="GroupBox")
                                $this->assertEquals(true,false);
                }


                function test_getParentFont(){}
                function test_setParentFont(){}

        }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_groupbox.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "GroupBoxTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>
