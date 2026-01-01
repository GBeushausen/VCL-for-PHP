<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_focuscontrol.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");
        use_unit("checklst.inc.php");

        class CustomCheckListBoxTest extends FocusControlTest
        {
                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;
                        $this->object=new CustomCheckListBox();
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
                        $this->assertEquals($this->object->Checked,array(true,true,true),'Init test 3');


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
                        $this->stowner->insertComponent($this->object);
                        $this->object->jsOnActivate='AnotherEvent';
                        $this->object->dumpJavascript();
                        $c=ob_get_contents();
                        ob_end_clean();
                        //print_r($c);
                        $c=$this->fixString($c);
                        $real='function AnotherEvent(event)
{
var event = event || window.event;
var params=null;

}

function CheckListBoxClick(name, index)
{
  var event = event || window.event;
  var obj=document.getElementById(name);
  if (obj) {
    if (!obj.disabled) {
      obj.checked = !obj.checked;
      return obj.onclick();
    }
  }
  return false;
}';
                        $this->stowner->removeComponent($this->object);
                        $this->assertEquals($this->cleanString($c),$this->cleanString($real),'Testing if dump javascript generate the right code');
                }


                function test_dumpContents()
                {
                        $this->object->Clear();
                        $this->object->AddItem("item1");
                        $this->object->AddItem("item2");

                        $c=$this->object->show(true);
                        $chars=array("\n","\r","\t"," ");
                        $c=$this->cleanString($c);
//                        print_r( $c);
                        $compare=$this->cleanString('<DIVstyle="OVERFLOW-Y:auto;OVERFLOW-X:hidden;WIDTH:185px;HEIGHT:89px;border:solid1px#CCCCCC;"><tablecellpadding="0"cellspacing="0"title=""style="font-family:Verdana;font-size:10px;table-layout:fixed;"><tr><tdwidth="20"><inputID="myobject_0"type="checkbox"name="myobject[0]"value="1"tabindex="0"/></td></td><tdwidth="165\height="44.5"style="overflow:hidden;white-space:nowrap;"><spanid=myobject_0style="white-space:nowrap;"style="font-family:Verdana;font-size:10px;">item1</span></tr><tr><tdwidth="20"><inputID="myobject_1"type="checkbox"name="myobject[1]"value="1"tabindex="0"/></td></td><tdwidth="165\height="44.5"style="overflow:hidden;white-space:nowrap;"><spanid=myobject_1style="white-space:nowrap;"style="font-family:Verdana;font-size:10px;">item2</span></tr></table></DIV>');
                        $this->assertEquals($c,$compare);
                }

                function test_ControlStyle($styles=array())
                {
                        $styles["csRenderOwner"]=1;
                        $styles["csRenderAlso"]="StyleSheet";
                        parent::test_ControlStyle($styles);
                }

                function test_AddItem()
                {
                        $this->object->AddItem("item1","1");
                        $this->object->AddItem("item2","2");
                        $this->object->AddItem("item3","3");
                        $Items=$this->object->Items;
                        $expected=array(1=>"item1",2=>"item2",3=>"item3");
                        $this->assertEquals($Items,$expected,"AddItem test 1");
                        $this->object->Clear();
                        $this->assertEquals($this->object->Items,array(),"AddItem test 2");
                }
                function test_Clear()
                {
                        $this->test_AddItem();
                }

                function test_readCount()
                {
                        //tested in test_Count
                }

                function test_Count()
                {
                        $this->object->Clear();
                        $this->assertEquals($this->object->Count,0,"Count test 1");
                        $this->object->AddItem("item1","1");
                        $this->object->AddItem("item2","2");
                        $this->object->AddItem("item3","3");
                        $this->assertEquals($this->object->Count,3,"Count test 3");
                }

                function test_readBorderWidth()
                {
                        //tested in test_BorderWidth
                }
                function test_writeBorderWidth()
                {
                        //tested in test_BorderWidth
                }
                function test_BorderWidth()
                {
                        $this->assertEquals($this->object->BorderWidth,(string)1,"Border Width 1");
                        $this->assertEquals($this->object->BorderWidth,(string)$this->object->defaultBorderWidth(),"Border Widht 2");
                        $this->object->BorderWidth="2";
                        $this->assertEquals($this->object->BorderWidth,(string)2,"Border Width 3");
                }

                function test_readBorderColor()
                {
                        //tested in test_BorerColor
                }
                function test_writeBorderColor()
                {
                        //tested in test_BorderColor
                }

                function test_BorderColor()
                {
                        $this->assertEquals($this->object->BorderColor,"#CCCCCC","Border Color 1");
                        $this->assertEquals($this->object->BorderColor,(string)$this->object->defaultBorderColor(),"Border Color 2");
                        $this->object->BorderWidth="#AAAAAA";
                        $this->assertEquals($this->object->BorderWidth,"#AAAAAA","Border Color 3");
                }

                function test_readBorderStyle()
                {
                        //tested in test_BorderStyle
                }
                function test_writeBorderStyle()
                {
                          //tested in test_BorderStyle
                }
                function test_BorderStyle()
                {
                        $this->assertEquals($this->object->BorderStyle,bsSingle,"Border Style 1");
                        $this->assertEquals($this->object->BorderStyle,(string)$this->object->defaultBorderStyle(),"Border Style 2");
                        $this->object->BorderStyle="bsNone";
                        $this->assertEquals($this->object->BorderStyle,"bsNone","Border Style 3");
                }
                function test_readOnClick()
                {
                         //tested in test_OnClick
                }
                function test_writeOnClick()
                {
                         //tested in test_OnClick
                }
                function test_OnClick()
                {

                        $this->object->Init();
                        $this->assertEquals($this->object->OnClick,$this->object->defaultOnClick(),"a");
                        $this->object->OnClick="Event";
                        $this->assertEquals($this->object->OnClick,"Event","b");
                }


                function test_readOnSubmit()
                {
                         //tested in test_OnClick
                }
                function test_writeOnSubmit()
                {
                         //tested in test_OnClick
                }
                function test_OnSubmit()
                {
                        $this->assertEquals($this->object->OnSubmit,$this->object->defaultOnSubmit(),"a");
                        $this->object->OnSubmit="Event";
                        $this->assertEquals($this->object->OnSubmit,"Event","b");
                }


                function test_readItems()
                {
                         //tested in test_OnClick
                }
                function test_writeItems()
                {
                         //tested in test_OnClick
                }
                function test_Items()
                {
                        $this->assertEquals($this->object->Items,array());
                        $Items=array(1=>"item1",2=>"item2",3=>"item3");
                        $this->object->Items=$Items;
                        $this->assertEquals($this->object->Items,$Items);
                }

                function test_getChecked()
                {
                         //tested in test_Checked
                }
                function test_setChecked()
                {
                         //tested in test_Checked
                }
                function test_Checked()
                {
                        $this->assertEquals($this->object->Checked,array(),"Checked test 0");
                        $this->assertEquals($this->object->Checked,$this->object->defaultChecked(),"Checked test 1");
                        $myarray=array(0=>1,1=>0);
                        $this->object->Checked=$myarray;
                        $this->assertEquals($this->object->Checked,$myarray,"Checked test 2");
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
                        $this->assertEquals($this->object->TabOrder,0,"Taborder Test 1");
                        $this->assertEquals($this->object->TabOrder,$this->object->defaultTabOrder(),"Taborder Test 2");
                        $this->object->TabOrder=1;
                        $this->assertEquals($this->object->TabOrder,1,"Taborder Test 3");
                }
                function test_readTabStop()
                {
                         //tested in test_TabStop
                }
                function test_writeTabStop()
                {
                         //tested in test_TabStop
                }
                function test_TabStop()
                {
                        $this->assertEquals($this->object->TabStop,1,"TabStop Test 1");
                        $this->assertEquals($this->object->TabStop,$this->object->defaultTabStop(),"TabStop Test 2");
                        $this->object->TabStop=0;
                        $this->assertEquals($this->object->TabStop,0,"TabStop Test 3");
                }

                function test_ItemAtPos()
                {
                        $this->object->Clear();
                        $this->object->AddItem("item1");
                        $this->object->AddItem("item2");
                        $this->assertEquals($this->object->ItemAtPos(0),"item1","a");
                        $this->assertEquals($this->object->ItemAtPos(1),"item2","b");
                        $this->assertEquals($this->object->ItemAtPos(2),null,"c");


                }
                function test_SelectAll()
                {
                        $this->object->Clear();
                        $this->object->AddItem("item1");
                        $this->object->AddItem("item2");

                        $this->assertEquals(count($this->object->Checked),0,"a");
                        $a=array();
                        $a=$this->object->Checked;
                        $a[0]=1;
                        $this->object->Checked=$a;
                        $this->assertEquals(count($this->object->Checked),1,"b");
                        $Items=$this->object->Items;
                        $Item=$Items[$this->object->Checked[0]];

                        $this->assertEquals($Item,"item2");
                }

                //checked in test_Proerty()
                function test_getHeader(){}
                //checked in test_Proerty()
                function test_setHeader(){}

                function test_Header()
                {
                        $this->object->AddItem("item1");
                        $this->object->AddItem("item2");
                        $this->object->AddItem("item3");
                        $header=array(0=>true,2=>false);
                        $this->assertEquals($this->object->Header,$this->object->defaultHeader());
                        $this->object->Header=$header;
                        $this->assertEquals($this->object->Header,$header);
                }

                function test_getColumns(){}
                function test_setColumns(){}
                function test_Columns()
                {
                        $this->assertEquals($this->object->Columns,$this->object->defaultColumns());
                        $this->object->Columns=1;
                        $this->assertEquals($this->object->Columns,1);
                }
                function test_getHeaderBackgroundColor(){}
                function test_setHeaderBackgroundColor(){}
                function test_HeaderBackGroundColor()
                {
                        $a=$this->object->HeaderBackGroundColor;
                        $this->assertEquals($this->object->HeaderBackGroundColor,$this->object->defaultHeaderBackGroundColor(),"a");
                        $this->object->HeaderBackGroundColor="#FFFFFF";
                        $this->assertEquals($this->object->HeaderBackGroundColor,"#FFFFFF");
                }
                function test_getHeaderColor(){}
                function test_setHeaderColor(){}
                function test_HeaderColor()
                {
                        $this->assertEquals($this->object->HeaderColor,$this->object->defaultHeaderColor());
                        $this->object->HeaderColor="#FFFFFF";
                        $this->assertEquals($this->object->HeaderColor,"#FFFFFF");
                }
        }
      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_customchecklistbox.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "CustomCheckListboxTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
