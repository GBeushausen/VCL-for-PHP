<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_focuscontrol.inc.php";
        use_unit("controls.inc.php");
        use_unit("stdctrls.inc.php");
        use_unit("classes.inc.php");

        class CustomRadioGroupTest extends FocusControlTest
        {

                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;
                        $this->object=new CustomRadioGroup();
                        $this->object->name="myobject";
                }


                function test_Columns()
                {
                  $this->defaultTest('Columns',1,10);
                }

                function test_preinit()
                {
                        $this->object->AddItem("Item1");
                        $this->object->AddItem("Item2");
                        $_SERVER['REQUEST_METHOD']='POST';
                        $_POST['myobject']=2;
                        $this->object->preinit();
                        $this->assertEquals($this->object->ItemIndex,"2");
                }

                function test_init()
                {
                        $this->aowner->insertComponent($this->object);

                        $this->assertEquals($this->aowner->executed,false,'Init test 1');

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
                        ob_start();
                        $this->object->OnClick="DummyEvent";
                        $this->object->dumpJavascript();
                        $c=ob_get_contents();
                        ob_clean();
                        $c=$this->cleanString($c);
                        $compare="functionDummyEventWrapper(event,hiddenfield,submitvalue,wrappedfunc){varevent=event||window.event;submit1=true;submit2=true;if(typeof(wrappedfunc)=='function')submit1=wrappedfunc(event);hiddenfield.value=submitvalue;form=hiddenfield.form;if((form)&&(form.onsubmit)&&(typeof(form.onsubmit)=='function'))submit2=form.onsubmit();if((submit1)&&(submit2))form.submit();returnfalse;}functionRadioGroupClick(elem,index){if(!elem.disabled){if(typeof(elem.length)=='undefined'){elem.checked=true;return(typeof(elem.onclick)=='function')?elem.onclick():false;}else{if(index>=0&&index<elem.length){elem[index].checked=true;return(typeof(elem[index].onclick)=='function')?elem[index].onclick():false;}}}returnfalse;}";
                        $this->assertEquals($c,$compare);
                }

                function test_ControlStyle($styles=array())
                   {
                        $styles["csRenderOwner"]=1;
                        $styles["csRenderAlso"]="StyleSheet";
                        parent::test_ControlStyle($styles);
                   }
                   function test_OnClick()
                   {
                        $this->assertEquals($this->object->OnClick,$this->object->defaultOnClick(),"a");
                        $this->object->OnClick="Event";
                        $this->assertEquals($this->object->OnClick,"Event","b");
                   }
                 function test_OnSubmit()
                {
                        $this->assertEquals($this->object->OnSubmit, null);
                        $this->assertEquals($this->object->OnSubmit,$this->object->defaultOnSubmit());
                        $this->object->OnSubmit="Submitevent";
                        $this->assertEquals($this->object->OnSubmit, "Submitevent");
                }
                function test_DataField()
               {
                        $this->assertEquals($this->object->DataField,"");
                        $this->assertEquals($this->object->DataField,$this->object->defaultDataField());
                        $this->object->DataField="myDataField";
                        $this->assertEquals($this->object->DataField,"myDataField");
               }
               function test_DataSource()
               {
                        $myObj=new DataSource();
                        $this->assertEquals($this->object->DataSource,null);
                        $this->assertEquals($this->object->DataSource,$this->object->defaultDataSource());
                        $this->object->DataSource=$myObj;
                        $this->assertEquals($this->object->DataSource, $myObj);
               }
               function test_ItemIndex()
                {
                        $this->assertEquals($this->object->ItemIndex,-1);
                        $this->assertEquals($this->object->defaultItemIndex(),$this->object->ItemIndex);
                        $this->object->ItemIndex=1;
                        $this->assertEquals($this->object->ItemIndex,1);
                }
                function test_Items()
                {
                        $this->assertEquals($this->object->Items,array());
                        $Items=array(1=>"item1",2=>"item2",3=>"item3");
                        $this->object->Items=$Items;
                        $this->assertEquals($this->object->Items,$Items);
                }

                function test_Orientation()
                {
                        $this->assertEquals($this->object->Orientation,orVertical);
                        $this->assertEquals($this->object->Orientation,$this->object->defaultOrientation());
                        $this->object->Orientation="orHorizontal";
                        $this->assertEquals($this->object->Orientation,"orHorizontal");
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
                function test_readCount()
                {
                        //tested in test_Count
                }

                function test_Count()
                {
                        $this->object->Clear();
                        $this->assertEquals($this->object->Count,0);
                        $this->object->AddItem("item2");
                        $this->assertEquals($this->object->Count,1);
                }
                function test_AddItem()
                {
                        $this->setup();
                        $this->object->AddItem("item1");
                        $Items=$this->object->Items;
                        $expected=array(1=>"item1");
                        $this->assertEquals($this->object->Items,array("item1"));
                        $this->object->Clear();
                        $this->assertEquals($this->object->Items,array());
                }
                function test_Clear()
                {
                        $this->test_AddItem("item 1");

                }
                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim('<tableid="myobject_table"cellpadding="0"title=""cellspacing="0"width="185"style="font-family:Verdana;font-size:10px;height:87px;width:185px;table-layout:fixed"></table>');
                        $this->assertEquals($this->cleanString($c),$this->cleanString($compare));
                }


               /* function test_getColumns(){}
                function test_setColumns(){}
                function test_Columns()
                {
                        $this->assertEquals($this->object->Columns,$this->object->defaultColumns());
                        $this->object->Columns=1;
                        $this->assertEquals($this->object->Columns,1);
                }*/

 }
                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_customradiogroup.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "CustomRadioGroupTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>
