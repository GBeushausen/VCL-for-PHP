<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_customtextfield.inc.php";
        use_unit ("stdctrls.inc.php");


        class CustomLabeledEditTest extends CustomTextFieldTest
        {
                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;
                        $this->object=new CustomLabeledEdit();
                        $this->object->Name="myobject";
                }

        function test_preinit()
        {
                $_POST['myobject_value']='testvalue<script>alert();</script>';
                $this->object->preinit();
                $this->assertEquals($this->object->text,'testvaluealert();');
        }

        function test_FilterInput()
        {
                $_POST['myobject_value']='testvalue<script>alert();</script>';
                $this->object->FilterInput=false;
                $this->object->preinit();
                $this->object->FilterInput=true;
                $this->assertEquals($this->object->text,'testvalue<script>alert();</script>');
        }

                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        //print_r($c);
                        $c=$this->cleanString($c);
                        $compare=$this->cleanString('<input type="hidden" id="myobject_state" name="myobject_state" value="" />
<script type="text/javascript">
</script><input type="hidden" id="myobject_value" name="myobject_value" value="" /><script type="text/javascript">
  var myobject_Lbl = new qx.ui.basic.Atom("myobject");
  myobject_Lbl.setLeft(0);
  myobject_Lbl.setTop(0);
  myobject_Lbl.setWidth(121);
  myobject_Lbl.setHorizontalChildrenAlign("left");
  myobject_Lbl.setVisibility(true);
  if (isdefined(window,\'inline_div\'))    inline_div.add(myobject_Lbl);
  var myobject = new qx.ui.form.TextField();
  myobject.setLeft(0);
  myobject.setTop(17);
  myobject.setWidth(121);
  myobject.setHeight(17);
  myobject.setMaxLength(0);
  myobject.setValue("");
  myobject.setReadOnly(false);
  myobject.setBorder(new qx.renderer.border.Border(1, \'solid\'));
  myobject.addEventListener(\'keyup\', function(e) { var hidvalue=findObj("myobject_value"); hidvalue.value=myobject.getComputedValue(e); });
  myobject.setEnabled(true);
  myobject.setFont("10px \'Verdana\' ");
  myobject.setVisibility(true);
</script>');
                        $this->assertEquals(trim($c),$compare);
                }


                function test_addOtherChildren()
                {

                                $expected=trim('.setLeft(10);
  .setTop(10);
  parentname.add();
  myobject.setLeft(10);
  myobject.setTop(27);');
                                ob_start();
                                $this->object->addOtherChildren("parentname",10,10);
                                $actual=ob_get_contents();
                                ob_end_clean();
                                //print_r($actual);
                                $actual=$this->fixString($actual);
                                $this->assertEquals($actual,$expected);

                }

/*                function test_LabelPosition()
                {
                }

                function test_LabelSpacing()
                {
                }

                function test_EditLabel()
                {
                }*/
        }

        if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
           else $script=$_GET['script'];

        if (basename($script)=='test_customlabelededit.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "CustomLabeledEditTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }
?>
