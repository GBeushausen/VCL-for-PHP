<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_focuscontrol.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");


        class CustomEditTest extends FocusControlTest
        {
                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;
                        $this->object=new CustomEdit();
                        $this->object->Name="myobject";
                }

                function test_preinit()
                {
                        $_SERVER['REQUEST_METHOD']='POST';
                        $this->object->preinit();
                        $this->assertEquals($this->object->Text,"");
                        $_POST['myobject']="this is my text";
                        $this->object->preinit();
                        $this->assertEquals($this->object->Text,"this is my text","preinit 1");

                }

                function test_init()
                {
                        $this->aowner->insertComponent($this->object);

                        $this->assertEquals($this->aowner->executed,false,'Init test 1');

                        //Test if OnExecute is called when should not be called
                        //because Actions is empty
                        $_POST['myobject']=array(true,true,true);
                        $_POST['myobjectSubmitEvent']='myobject_DummyEvent';
                        $this->aowner->executed=false;
                        $this->object->OnSubmit="DummyEvent";
                        $this->object->init();
                        $this->assertEquals($this->aowner->executed,true,'Init test 2');


                        $_POST['myobjectSubmitEvent']='myobject_DummyEvent';
                        $this->aowner->executed=false;
                        $this->object->OnClick="DummyEvent";
                        $this->object->init();
                        $this->assertEquals($this->aowner->executed,true,'Init test 3');

                        $_POST['myobjectSubmitEvent']='myobject_DummyEvent';
                        $this->aowner->executed=false;
                        $this->object->OnDblClick="DummyEvent";
                        $this->object->init();
                        $this->assertEquals($this->aowner->executed,true,'Init test 4');

                        $this->aowner->removeComponent($this->object);
                }


                function test_BorderStyle()
                {
                        $this->assertEquals($this->object->BorderStyle, bsSingle);
                        $this->assertEquals($this->object->BorderStyle,$this->object->defaultBorderStyle());
                        $this->object->BorderStyle=bsNone;
                        $this->assertEquals($this->object->BorderStyle, bsNone);
                }

                function test_ControlStyle($styles=array())
                {
                        $styles["csRenderOwner"]=1;
                        $styles["csRenderAlso"]="StyleSheet";
                        parent::test_ControlStyle($styles);
                }

                function test_CharCase()
                {
                        $this->assertEquals($this->object->CharCase, ecNormal);
                        $this->assertEquals($this->object->CharCase,$this->object->defaultCharCase());
                        $this->object->CharCase=ecUpperCase;
                        $this->object->Text= "Text to Uppercase";
                        $my_text=$this->object->Text;
                        $this->assertEquals($my_text, "TEXT TO UPPERCASE");
                        $this->assertEquals($this->object->CharCase, ecUpperCase);
                        $this->object->CharCase=ecLowerCase;
                        $this->assertEquals($this->object->CharCase, ecLowerCase);
                        $this->assertEquals($this->object->Text, "text to uppercase");

                        // $this->assertEquals(true,false,"Waiting for function revision");
                }

                function test_OnClick()
                {
                        $this->object->init();
                }

                 function test_OnDblClick()
                {
                        $this->object->init();
                }

                function test_OnSubmit()
                {
                        $this->object->init();
                }

                function test_IsPassword()
                {
                        $this->assertEquals($this->object->IsPassword,0);
                        $this->assertEquals($this->object->defaultIsPassword(),$this->object->IsPassword);
                        $this->object->IsPassword=1;
                        $this->assertEquals($this->object->IsPassword,1);
                }

                function test_ReadOnly()
                {
                        $this->assertEquals($this->object->ReadOnly,0);
                        $this->assertEquals($this->object->defaultReadOnly(),$this->object->ReadOnly);
                        $this->object->ReadOnly=1;
                        $this->assertEquals($this->object->ReadOnly,1);
                }

                 function test_TabOrder()
                {
                        $this->assertEquals($this->object->TabOrder,0);
                        $this->assertEquals($this->object->defaultTabOrder(),$this->object->TabOrder);
                        $this->object->TabOrder=1;
                        $this->assertEquals($this->object->TabOrder,1);
                }

                 function test_TabStop()
                {
                        $this->assertEquals($this->object->TabStop,1);
                        $this->assertEquals($this->object->defaultTabStop(),$this->object->TabStop);
                        $this->object->TabStop=0;
                        $this->assertEquals($this->object->TabStop,0);
                }

                 function test_Text()
                {
                        $this->assertEquals($this->object->Text,'');
                        $this->assertEquals($this->object->defaultText(),$this->object->Text);
                        $this->object->Text='new text.=/@!&^hallo';
                        $this->assertEquals($this->object->Text,'new text.=/@!&^hallo');
                }

                 function test_MaxLength()
                {
                        $this->assertEquals($this->object->MaxLength,0);
                        $this->assertEquals($this->object->defaultMaxLength(),$this->object->MaxLength);
                        $this->object->MaxLength=20;
                        $this->assertEquals($this->object->MaxLength,20);
                        $this->object->MaxLength=20000;
                        $this->assertEquals($this->object->MaxLength,20000);
                }



                function test_DataSource()
                {
                        $obj=new Object();
                        $this->assertEquals($this->object->DataSource,null);
                        $this->assertEquals($this->object->DataSource,$this->object->defaultDataSource());
                        $this->object->DataSource=$obj;
                        $this->assertEquals($this->object->DataSource,$obj);
                }

                function test_DataField()
                {
                        $this->assertEquals($this->object->DataField,'');
                        $this->assertEquals($this->object->DataField,$this->object->defaultDataField());
                        $this->object->DataField='hola, radiola';
                        $this->assertEquals($this->object->DataField,'hola, radiola');
                }

               function test_jsOnSelect()
               {
                        $this->assertEquals($this->object->jsOnSelect,null);
                        $this->assertEquals($this->object->jsOnSelect,$this->object->defaultjsOnSelect());
                        $this->object->jsOnSelect='dummyEvent';
                        $this->assertEquals($this->object->jsOnSelect,'dummyEvent');

               }

                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        $c=$this->fixString($c);
                        $compare=trim('<input type="text" id="myobject" name="myobject" value="" style=" font-family: Verdana; font-size: 10px;  height:20px;width:121px;"    tabindex="0"    />');
                        $this->assertEquals(trim($c),$compare);
                }

                function test_getFilterInput(){}
                function test_setFilterInput(){}
                function test_FilterInput()
                {
                        $this->assertEquals($this->object->FilterInput,1);
                        $this->assertEquals($this->object->defaultFilterInput(),$this->object->FilterInput);
                        $this->object->FilterInput=0;
                        $this->assertEquals($this->object->FilterInput,0);
                }

        }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_customedit.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "CustomEditTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>
