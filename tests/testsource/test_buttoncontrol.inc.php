<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_focuscontrol.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");

        class ButtonControlTest extends FocusControlTest
        {
                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;
                        $this->object=new ButtonControl();
                        $this->object->Name="myobject";
                }

                function test_init()
                {
                        $this->aowner->insertComponent($this->object);

                        $this->assertEquals($this->aowner->executed,false,'Init test 1');

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
                        $this->assertEquals($this->aowner->executed,true,'Init test 6');
                        $this->aowner->removeComponent($this->object);
                }

                function test_dumpJavascript()
                {
                        $this->setup();
                        ob_start();
                        $this->object->jsOnSelect="DummyEvent";
                        $this->object->OnClick="AnotherDummyEvent";
                        $this->object->dumpJavascript();

                        $c=ob_get_contents();
                        ob_clean();
                        //print_r($c);
                        $expected='functionAnotherDummyEventWrapper(event,hiddenfield,submitvalue,wrappedfunc){varevent=event||window.event;submit1=true;submit2=true;if(typeof(wrappedfunc)==\'function\')submit1=wrappedfunc(event);hiddenfield.value=submitvalue;form=hiddenfield.form;if((form)&&(form.onsubmit)&&(typeof(form.onsubmit)==\'function\'))submit2=form.onsubmit();if((submit1)&&(submit2))form.submit();returnfalse;}';
                        $this->assertEquals($this->cleanString($c),$this->cleanString($expected));

                }

                function test_dumpContents()
                {
                        if($this->object->className()!="ButtonControl")
                                $this->assertEquals(false,true);
                }

                function test_OnClick()
                {
                        $this->assertEquals($this->object->OnClick,null);
                        $this->assertEquals($this->object->OnClick,$this->object->defaultOnClick());
                        $this->object->OnClick='onClickEvent';
                        $this->assertEquals($this->object->OnClick,'onClickEvent');
                }

               function test_jsOnSelect()
               {
                        $this->assertEquals($this->object->jsOnSelect,null);
                        $this->assertEquals($this->object->jsOnSelect,$this->object->defaultjsOnSelect());
                        $this->object->jsOnSelect='dummyEvent';
                        $this->assertEquals($this->object->jsOnSelect,'dummyEvent');
               }

               function test_Checked()
               {
                        $this->assertEquals($this->object->Checked,0);
                        $this->assertEquals($this->object->Checked,$this->object->defaultChecked());
                        $this->object->Checked=1;
                        $this->assertEquals($this->object->Checked,1);
               }

               function test_DataSource()
               {
                        $myObj=new DataSource();
                        $this->assertEquals($this->object->DataSource,null);
                        $this->assertEquals($this->object->DataSource,$this->object->defaultDataSource());
                        $this->object->DataSource=$myObj;
                        $this->assertEquals($this->object->DataSource, $myObj);
               }

                function test_DataField()
               {
                        $this->assertEquals($this->object->DataField,"");
                        $this->assertEquals($this->object->DataField,$this->object->defaultDataField());
                        $this->object->DataField="myDataField";
                        $this->assertEquals($this->object->DataField,"myDataField");
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

               function test_ControlStyle($styles=array())
               {
                        $styles["csRenderOwner"]=1;
                        $styles["csRenderAlso"]="StyleSheet";
                        parent::test_ControlStyle($styles);
               }

               function test_dumpContentsButtonControl()
               {
                        //This will be more heavily checked in suclasses tests of dumpContents
                        $this->object->jsOnClick="jsonclickevent";
                        $this->object->OnClick="onclickevent";
                        ob_start();
                        $this->object->dumpContentsButtonControl("button",$this->object->Name);
                        $contents=ob_get_contents();
                        ob_end_clean();
                        $expected='<input type="button" id="myobject" name="myobject" value=""  onclick="return onclickeventWrapper(event, null, \'myobject_onclickevent\', jsonclickevent)"  style=" font-family: Verdana; font-size: 10px;  height:25px;width:75px;"   tabindex="0"    />';
                        $this->assertEquals($expected,$contents);
               }

               function test_readOnSubmit()
               {
               }
               function test_writeOnSubmit()
               {
               }
         }

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_buttoncontrol.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "ButtonControlTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
