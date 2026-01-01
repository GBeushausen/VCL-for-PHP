<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_qwidget.inc.php";
        use_unit("extctrls.inc.php");



        class CustomButtonViewTest extends QWidgetTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new CustomButtonView();
                        $this->object->Name="myobject";
                }

                function test_dumpContents()
                {
                        //Fill items  and check whole control
                        $this->jsOnClick="myonclickevent";
                        $Items=array(
                                array( "Caption" => "Button1","ImageIndex" => 0, "Tag" => 0),
                                array( "Caption" => "Button2","ImageIndex" => -1,"Tag" => 1));
                        $this->object->Items=$Items;
                        $ImageList=new ImageList();
                        $ImageList->Images=array(0=>"image1", 1=>"image2", 2=>"image3");
                        $this->object->ImageList=$ImageList;
                        $c=$this->object->show(true);
                        //print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim('<input type="hidden" id="myobject_state" name="myobject_state" value="" />
<script type="text/javascript">
  var i = new qx.ui.basic.Inline("myobject");
  i.setHeight("auto");
  i.setWidth("auto");
  var myobject = new qx.ui.pageview.buttonview.ButtonView;
  myobject.setLeft(0);
  myobject.setTop(0);
  myobject.setWidth(63);
  myobject.setHeight(335);
  myobject.setBarPosition("left");

  <!-- Define Buttons - Start -->
    var myobject_0 = new qx.ui.pageview.buttonview.Button("Button1", "image1");
    myobject_0.tag=0;
    myobject_0.addEventListener("click", function dummy(){});
    var myobject_1 = new qx.ui.pageview.buttonview.Button("Button2");
    myobject_1.tag=1;
    myobject_1.addEventListener("click", function dummy(){});
  <!-- Define Buttons - Start -->

  myobject.getBar().add(myobject_0,myobject_1);
  myobject_0.setChecked(true);
</script>');
                        $this->assertEquals(trim($c),$compare);

                        //Reset
                        $this->jsOnClick=null;
                        $Items=array();
                        $ImageList=new ImageList();
                }

                function test_ImageList()
                {
                        $this->assertEquals($this->object->ImageList,null,"ImageList test 1");
                        //at start Images must be null
                        $this->assertEquals($this->object->ImageList,$this->object->defaultImageList(),"ImageList test 2");

                        //get/set check
                        $ImageList=new ImageList();
                        $ImageList->Images=array(0=>"image1", 1=>"image2", 2=>"image3");
                        $CustomButtonView1= new CustomButtonView();
                        $CustomButtonView2= new CustomButtonView();
                        $CustomButtonView1->ImageList=$ImageList;
                        $CustomButtonView2->ImageList=$ImageList;
                        $this->assertEquals($CustomButtonView1->ImageList,$CustomButtonView2->ImageList,"ImageList test 3");
                        $this->assertEquals($ImageList,$CustomButtonView1->ImageList,"ImageList test 4");
                }

                function test_Position()
                {
                        $this->assertEquals($this->object->Position,"btLeft","position test 1");
                        $this->assertEquals($this->object->Position,$this->object->defaultPosition(),"position test 2");
                        $this->object->Position=1;
                        $this->assertEquals($this->object->Position,1,"position test 3");
                }

                function test_addOtherChildren()
                {
                  //This method is not implemented in this component, so override it
                }

        }
      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_custombuttonview.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "CustomButtonViewTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
