<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_qwidget.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("comctrls.inc.php");

        class CustomPageControlTest extends QWidgetTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new CustomPageControl();
                        $this->object->Name="myobject";
                }
                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim('<input type="hidden" id="myobject_state" name="myobject_state" value="" />
<script type="text/javascript">
  var myobject = new qx.ui.pageview.tabview.TabView;
  myobject.setLeft(0);
  myobject.setTop(0);
  myobject.setWidth(300);
  myobject.setHeight(400);
  myobject.setPlaceBarOnTop(true);
  myobject.setVisibility(true);
</script>');
                        $this->assertEquals(trim($c),$compare);
                }
                function test_ControlStyle($styles=array())
                {
                        $styles["csAcceptsControls"]=1;
                        parent::test_ControlStyle($styles);
                }

                function test_addOtherChildren()
                {
                        if ($this->object->className()!="CustomPageControl")
                                $this->assertEquals(true,false);
                }

                function test_getActiveLayer()
                {
                        //Checked with test_ActiveLayer
                }

                function test_setActiveLayer()
                {
                        //Checked with test_ActiveLayer
                }

                function test_ActiveLayer()
                {
                        $Tabs=array();
                        $Tabs[]='Tab 1';
                        $Tabs[]='Tab 2';
                        $Tabs[]='Tab 3';
                        $this->object->Tabs=$Tabs;
                        $this->object->TabIndex=2;
                        $c=$this->object->getActiveLayer();
                        $this->assertEquals($c,"Tab 3","a");
                        //needs more testing
                        $this->object->TabIndex=0;
                        $c=$this->object->getActiveLayer();
                        $this->assertEquals($c,"Tab 1","b");
                }

                function test_commonScript()
                {
                        ob_start();
                        $this->object->commonScript();
                        $actual=ob_get_contents();
                        ob_end_clean();
                        //print_r($actual);
                        $expected=trim('myobject.setLeft(0);
  myobject.setTop(0);
  myobject.setWidth(300);
  myobject.setHeight(400);
  myobject.setPlaceBarOnTop(true);');

                        $actual=$this->fixString($actual);
                        $this->assertEquals($expected,$actual);


                }

                function test_dumpForAjax()
                {
                        //dumpForAjax just calls commonScript
                        $this->test_commonScript();
                }

                function test_init()
                {

                        $_POST['myobject_state']=2;
                        $this->object->init();
                        $this->assertEquals($this->object->TabIndex,1);
                }

                
         }
      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_custompagecontrol.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "CustomPageControlTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }
?>
