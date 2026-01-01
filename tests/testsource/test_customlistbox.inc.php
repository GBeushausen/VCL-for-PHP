<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_focuscontrol.inc.php";
        use_unit("controls.inc.php");
        use_unit("stdctrls.inc.php");
        use_unit("classes.inc.php");

        class CustomListBoxTest extends FocusControlTest
        {

                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;
                        $this->object=new CustomListBox();
                        $this->object->name="myobject";
                }

                function test_preinit()
                {
                        $this->setup();
                        $_POST['myobject']=array("0","2");
                        $this->object->MultiSelect=1;
                        $this->object->preinit();
                        $this->assertEquals($this->object->readSelected(0), true,"Check Test 1");
                        $this->assertEquals($this->object->readSelected(1), false,"Check Test 2");
                        $this->assertEquals($this->object->readSelected(2), true,"Check Test 3");

                        $_POST['myobject']=array("1");
                        $this->object->AddItem("item1");
                        $this->object->MultiSelect=0;
                        $this->object->preinit();
                        $this->assertEquals($this->object->readSelected(0), true,"Check Test 4");

                        $this->setup();

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
                        $this->object->OnSubmit=null;

                        $_POST['myobjectSubmitEvent']='myobject_DummyEvent';
                        $this->aowner->executed=false;
                        $this->object->OnClick="DummyEvent";
                        $this->object->init();
                        $this->assertEquals($this->aowner->executed,true,'Init test 3');
                        $this->object->OnClick=null;

                        $_POST['myobjectSubmitEvent']='myobject_DummyEvent';
                        $this->aowner->executed=false;
                        $this->object->OnDblClick="DummyEvent";
                        $this->object->init();
                        $this->assertEquals($this->aowner->executed,true,'Init test 4');
                        $this->object->OnDblClick=null;

                        $_POST['myobjectSubmitEvent']='myobject_DummyEvent';
                        $this->aowner->executed=false;
                        $this->object->OnChange="DummyEvent";
                        $this->object->init();
                        $this->assertEquals($this->aowner->executed,true,'Init test 5');

                        $this->aowner->removeComponent($this->object);
                }

                function test_dumpContents()
                {
                        $this->setup();
                        $c=$this->object->show(true);
                        $c=$this->fixString($c);
                        $compare=trim('<select name="myobject" id="myobject" size="4" style=" font-family: Verdana; font-size: 10px;  height:87px;width:185px;"   tabindex="0"   ></select>');
                        $this->assertEquals(trim($c),$compare);
                }


                function test_ControlStyle($styles=array())
                {
                        $styles["csRenderOwner"]=1;
                        $styles["csRenderAlso"]="StyleSheet";
                        parent::test_ControlStyle($styles);
                }
                function test_OnClick()
                {
                        $this->assertEquals($this->object->OnClick, null);
                        $this->assertEquals($this->object->defaultOnClick(),$this->object->OnClick);
                        $this->object->OnClick= 'onclickEvent';
                        $this->assertEquals($this->object->OnClick, 'onclickEvent');
                }
                 function test_OnDblClick()
                {
                        $this->assertEquals($this->object->OnDblClick, null);
                        $this->assertEquals($this->object->OnDblClick,$this->object->defaultOnDblClick());
                        $this->object->OnDblClick="Dblevent";
                        $this->assertEquals($this->object->OnDblClick, "Dblevent");
                }
                 function test_OnSubmit()
                {
                        $this->assertEquals($this->object->OnSubmit, null);
                        $this->assertEquals($this->object->OnSubmit,$this->object->defaultOnSubmit());
                        $this->object->OnSubmit="Submitevent";
                        $this->assertEquals($this->object->OnSubmit, "Submitevent");
                }
                function test_OnChange()
                {
                        $this->assertEquals($this->object->OnChange, null);
                        $this->assertEquals($this->object->OnChange,$this->object->defaultOnChange());
                        $this->object->OnChange="Changeevent";
                        $this->assertEquals($this->object->OnChange, "Changeevent");
                }

                function test_BorderStyle()
                {
                        $this->assertEquals($this->object->BorderStyle,bsSingle,"c");
                        $this->assertEquals($this->object->BorderStyle,(string)$this->object->defaultBorderStyle(),"a");
                        $this->object->BorderStyle="bsNone";
                        $this->assertEquals($this->object->BorderStyle,"bsNone","b");
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

                function test_Items()
                {
                        $this->assertEquals($this->object->Items,array());
                        $Items=array(1=>"item1",2=>"item2",3=>"item3");
                        $this->object->Items=$Items;
                        $this->assertEquals(count($this->object->Items),3);
                        $this->assertEquals($this->object->Items,$Items);
                }

                function test_TabStop()
                {
                        $this->assertEquals($this->object->TabStop,1);
                        $this->assertEquals($this->object->defaultTabStop(),$this->object->TabStop);
                        $this->object->TabStop=0;
                        $this->assertEquals($this->object->TabStop,0);
                }
                function test_AddItem()
                {
                        $this->object->AddItem("Item1");
                        $this->object->AddItem("Item2");
                        $this->assertEquals(count($this->object->Items),2);
                        $this->assertEquals($this->object->Items,array("Item1","Item2"));
                        $Items=$this->object->Items;
                        $expected=array("item1","item2");
                        $this->object->Clear();
                        $this->assertEquals($this->object->Items,array());
                }
                function test_Clear()
                {
                        $this->test_AddItem();
                }
                function test_readTabOrder()
                {
                         //tested in test_TabOrder
                }
                function test_writeTabOrder()
                {
                         //tested in test_TabOrder
                }
               function test_TabOrder()
                {
                        $this->assertEquals($this->object->TabOrder,0);
                        $this->assertEquals($this->object->defaultTabOrder(),$this->object->TabOrder);
                        $this->object->TabOrder=1;
                        $this->assertEquals($this->object->TabOrder,1);
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

                function test_Size()
                {
                        $this->assertEquals($this->object->Size,4);
                        $this->assertEquals($this->object->defaultSize(),$this->object->Size);
                        $this->object->Size=1;
                        $this->assertEquals($this->object->Size,1);
                }

               function test_Sorted()
                {
                        $this->assertEquals($this->object->Sorted,0);
                        $this->assertEquals($this->object->defaultSorted(),$this->object->Sorted);
                        $this->object->Sorted=1;
                        $this->assertEquals($this->object->Sorted,1);
                }
                function test_readItemIndex()
                {
                         //tested in test_ItemIndex
                }
                function test_writeItemIndex()
                {
                         //tested in test_ItemIndex
                }

               function test_ItemIndex()
                {
                        $this->assertEquals($this->object->ItemIndex,-1);
                        $this->assertEquals($this->object->defaultItemIndex(),$this->object->ItemIndex);
                        $this->object->ItemIndex=1;
                        $this->assertEquals($this->object->ItemIndex,1);
                }
                function test_getMultiSelect()
                {
                         //tested in test_MultiSelect
                }
                function test_setMultiSelect()
                {
                         //tested in test_MultiSelect
                }

               function test_MultiSelect()
               {
                        $this->assertEquals($this->object->MultiSelect,0);
                        $this->assertEquals($this->object->defaultMultiSelect(),$this->object->MultiSelect);
                        $this->object->MultiSelect=1;
                        $this->assertEquals($this->object->MultiSelect,1);
               }

                function test_ClearSelection()
                {
                        $this->setup();
                        $this->object->AddItem("item1",null,false);
                        $this->object->AddItem("item1",null,false);
                        $this->object->MultiSelect=1;
                        $this->object->SelectAll();
                        $this->assertEquals($this->object->readSelCount(),2,"ClearSelection test 1");
                        $this->object->ClearSelection();
                        $this->assertEquals($this->object->readSelCount(),0,"ClearSelection test 2");
                }
                function test_SelectAll()
                {
                        $this->test_ClearSelection();
                }
                function test_SelCount()
                {
                        $this->test_ClearSelection();
                }
                function test_Selected()
                {
                        $index=array();
                        $index[1]="a";
                        $index2[1]="a";
                        $this->assertEquals($this->object->readSelected($index),$this->object->readSelected($index2));
                }

       }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_customlistbox.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "CustomListBoxTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>
