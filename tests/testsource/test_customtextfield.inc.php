<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_qwidget.inc.php";
        use_unit ("stdctrls.inc.php");
        use_unit ("comctrls.inc.php");


        class CustomTextFieldTemp extends CustomTextField
        {
                function AdjustText_test() {  $this->AdjustText(); }
                function CalculateEditorRect_test() { return $this->CalculateEditorRect(); }
        }

        class CustomTextFieldTest extends QWidgetTest
        {
                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;
                        $this->object=new CustomTextField();
                        $this->object->Name="myobject";
                }

        function test_FilterInput()
        {
                $_POST['myobject_value']='testvalue<script>alert();</script>';
                $this->object->FilterInput=false;
                $this->object->preinit();
                $this->object->FilterInput=true;
                $this->assertEquals($this->object->text,'testvalue<script>alert();</script>');
                $_POST['myobject_value']='';
        }

                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim('<input type="hidden" id="myobject_state" name="myobject_state" value="" />
<script type="text/javascript">
</script><input type="hidden" id="myobject_value" name="myobject_value" value="" /><script type="text/javascript">
  var myobject = new qx.ui.form.TextField();
  myobject.setLeft(0);
  myobject.setTop(0);
  myobject.setWidth(120);
  myobject.setHeight(21);
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
                        if ($this->object->className()!="CustomTextField")
                                $this->assertEquals(true,false);
                }

                function test_preinit()
                {
                        $_SERVER['REQUEST_METHOD']='POST';
                        $this->object->preinit();
                        $this->assertEquals($this->object->Text,"");
                        $_POST['myobject_value']="this is my text";
                        $this->object->preinit();
                        $this->assertEquals($this->object->Text,"this is my text","preinit 1");
                }

                function test_init()
                {
                        $this->aowner->insertComponent($this->object);

                        $this->assertEquals($this->aowner->executed,false,'Init test 1');

                        $this->object->owner->UseAjax=false;

                        $_POST['myobject_state']='active';
                        $this->aowner->executed=false;
                        $this->object->OnClick="DummyEvent";
                        $this->object->init();
                        $this->assertEquals($this->aowner->executed,true,'Init test 2');

                        $this->aowner->removeComponent($this->object);

                }

                function test_BorderStyle()
                {
                        $this->defaultTest('BorderStyle',bsSingle,bsNone);
                }

                function test_DataField()
                {
                        $this->defaultTest('DataField','','fieldname');
                }

                function test_DataSource()
                {
                        $myObj=new DataSource();

                        $this->defaultTest('DataSource',null,$myObj);
                }

                function test_MaxLength()
                {
                        $this->defaultTest('MaxLength',0,10000);
                }

                function test_ReadOnly()
                {
                        $this->defaultTest('ReadOnly',0,true);
                }

                function test_OnClick()
                {
                        $this->assertEquals($this->object->OnClick,null);
                        $this->assertEquals($this->object->defaultOnClick(),$this->object->OnClick);
                        $this->object->OnClick="myevent";
                        $this->assertEquals($this->object->OnClick,'myevent');

                        $this->test_init();
                }

                function test_Text()
                {
                        $this->defaultTest('Text','','hello');
                }

                function test_AdjustText()
                {
                        $obj=new CustomTextFieldTemp();
                        $obj->Text="this is my text";
                        $obj->AdjustText_test();
                        $this->assertEquals($obj->Text,"this is my text");
                        $obj->Charcase=ecUpperCase;
                        $obj->AdjustText_test();
                        $this->assertEquals($obj->Text,"THIS IS MY TEXT");

                }
                function test_CaculateEditorRect()
                {
                        $obj=new CustomTextFieldTemp();
                        $rect=$obj->CalculateEditorRect_test();
                        $this->assertEquals($rect,array(0,0,$obj->Width, $obj->Height));
                }
        }

        if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
           else $script=$_GET['script'];

        if (basename($script)=='test_customtextfield.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "CustomTextFieldTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }
?>
