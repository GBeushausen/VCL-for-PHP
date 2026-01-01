<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_qwidget.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("comctrls.inc.php");

        class CustomListViewTest extends QWidgetTest
        {
                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;
                        $this->object=new CustomListView(/*$this->stowner*/);
                        $this->object->Name="myobject";
                }

                function test_unserialize()
                {
                  //Properties are public, so cannot be serialized
                  //this method should be tested on a descendant
                }



                function test_init()
                {
                        $this->aowner->insertComponent($this->object);

                        $this->assertEquals($this->aowner->executed,false,'Init test 1');

                        $_POST['myobjectSelectedItems']=array(1,3);
                        $this->aowner->executed=false;
                        $this->object->OnSubmit="DummyEvent";
                        $this->object->init();
                        $this->assertEquals($this->aowner->executed,true,'Init test 2');
                        $this->aowner->removeComponent($this->object);


                }

                function test_loaded()
                {
                        $_POST['myobjectSortColumnIndex']=2;
                        $_POST['myobjectSortAscending']=0;
                        $_POST['myobjectSelectedItems']="0,2";

                        //Make sure all items are not selected
                        $this->object->AddItem("Item1");
                        $this->object->AddItem("Item3");
                        $this->object->AddItem("Item2");
                        $Items=$this->object->Items;
                        $this->assertEquals($Items[0]->Selected  |
                                $Items[1]->Selected |
                                $Items[2]->Selected,0,"CustomListView test 1");

                        $this->object->loaded();
                        $this->assertEquals($this->object->SortColumnIndex,2,"CustomListView test 2");
                        $this->assertEquals($this->object->SortAscending,0,"CustomListView test 3");

                        $this->assertEquals($this->object->Items[0]->Selected,true,"CustomListView test 4");
                        $this->assertEquals($this->object->Items[1]->Selected,false,"CustomListView test 5");
                        $this->assertEquals($this->object->Items[2]->Selected,true,"CustomListView test 6");

               }
                function test_updateControl()
                {
                  ob_start();
                  $this->object->updatecontrol();
                  $c=ob_get_contents();
                  ob_end_clean();
                  $c=$this->cleanString($c);
                  $this->assertEquals('',$c);
                }

                function test_dumpForAjax()
                {
                  ob_start();
                  $this->object->dumpForAjax();
                  $c=ob_get_contents();
                  ob_end_clean();
                  $c=$this->cleanString($c);
                  $this->assertEquals('',$c);
                }
                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        $c=$this->fixString($c);
                        $c=str_replace(array(" ","\r","\n"),"",$c);
                        $compare=str_replace(array(" ","\r","\n"),"",'<input type="hidden" name="myobjectSelectedItems" id="myobjectSelectedItems" value="" />
<input type="hidden" name="myobjectSortColumnIndex" id="myobjectSortColumnIndex" value="-1" />
<input type="hidden" name="myobjectSortAscending" id="myobjectSortAscending" value="1" />
<input type="hidden" id="myobject_state" name="myobject_state" value="" />
<script type="text/javascript">
  var myobject_formonsubmit = document.forms[0].onsubmit;
  document.forms[0].onsubmit = myobjectUpdateProps;
  var columnData = ["Index"];
  var rowData = [];

  var tableModel = new qx.ui.table.SimpleTableModel();
  tableModel.setColumns(columnData);
  tableModel.setData(rowData);
  var myobject = new qx.ui.table.Table(tableModel);
  var myobject_colmodel = myobject.getTableColumnModel();
  myobject.columnIndexOfIndices = 0;
  myobject_colmodel.setColumnVisible(0, false);
  myobject.getSelectionModel().setSelectionMode(qx.ui.table.SelectionModel.SINGLE_SELECTION);
  var selecteditemindices = [];
  var found;
  var ii;
  for (var i = 0; i < rowData.length; i++) {
    found = false;
    ii = 0;
    while (!found && ii < selecteditemindices.length) {
      found = (selecteditemindices[ii] == rowData[i][0]);
      ii++;
    }
    if (found) {
      myobject.getSelectionModel().addSelectionInterval(i, i);
    }
  }
  myobject.setBorder(qx.renderer.border.BorderPresets.getInstance().shadow);
  myobject.setBackgroundColor("white");
  myobject.setLeft(0);
  myobject.setTop(0);
  myobject.setWidth(557);
  myobject.setHeight(314);
</script>');
                        $this->assertEquals(trim($c),$compare);
                }

                function test_dumpJavascript()
                {
                        ob_start();
                        $this->object->dumpJavaScript();
                        $c=ob_get_contents();
                        ob_clean();
                        $c=$this->cleanString($c);
                        $compare=$this->cleanString("functionmyobjectUpdateProps(){vartableModel=myobject.getTableModel();document.forms[0].myobjectSortColumnIndex.value=tableModel.getSortColumnIndex();document.forms[0].myobjectSortAscending.value=tableModel.isSortAscending();updateSelectedListViewItems(myobject,document.forms[0].myobjectSelectedItems);if(typeof(myobject_formonsubmit)=='function')myobject_formonsubmit();returntrue;}functionupdateSelectedListViewItems(lv,hiddenfield){varselectedRowData=[];vari=-1;vartableModel=lv.getTableModel();lv.getSelectionModel().iterateSelection(function(index){i=tableModel.getValue(lv.columnIndexOfIndices,index);selectedRowData.push(i);});hiddenfield.value=selectedRowData.toString();}");
                        $this->assertEquals($c,$compare);
                }
                function test_addOtherChildren()
                {
                        if ($this->object->className()!="CustomListView")
                                $this->assertEquals(true,false);
                }

                function test_addColumn()
                {
                        $this->object->addColumn("column1");
                        $this->object->addColumn("column2");
                        $Column1=new ListColumn();
                        $Column2=new ListColumn();
                        $Column1->Caption="column1";
                        $Column1->Editable=true;
                        $Column1->Width=-1;
                        $Column1->CellRenderType="creEdit";
                        $Column2->Caption="column2";
                        $Column2->Editable=true;
                        $Column2->Width=-1;
                        $Column2->CellRenderType="creEdit";

                        $expected=array(0 => $Column1,1=>$Column2);
                        $this->assertEquals($this->object->Columns,$expected);
                        $this->object->Columns=$expected;
                        $this->assertEquals($this->object->Columns,$expected);

                }

                function test_OnSubmit()
                {
                        $this->assertEquals($this->object->OnSubmit, null);
                        $this->assertEquals($this->object->OnSubmit,$this->object->defaultOnSubmit());
                        $this->object->OnSubmit="Submitevent";
                        $this->assertEquals($this->object->OnSubmit, "Submitevent");
                }
                function test_jsOnSelectionChanged()
                {
                        $this->assertEquals($this->object->jsOnSelectionChanged,null);
                        $this->assertEquals($this->object->jsOnSelectionChanged,$this->object->defaultjsOnSelectionChanged());
                        $this->object->jsOnSelectionChanged="myevent";
                        $this->assertEquals($this->object->jsOnSelectionChanged,"myevent");
                }


                function test_Items()
                {
                        $this->setup();
                        //at start Items must be null
                        $this->assertEquals($this->object->Items,array());

                        //Read/write items check
                        $CustomListView1= new CustomListView();
                        $CustomListView2= new CustomListView();
                        $CustomListView1->AddItem("Item1");
                        $CustomListView1->AddItem("Item2");
                        $Items=$CustomListView1->Items;
                        $this->assertEquals(count($Items),2,"Item check 1");
                        $this->assertEquals($Items[0]->Caption=="Item1",true,"Item check 2");
                        $this->assertEquals($Items[1]->Caption=="Item2",true,"Item check 3");
                }

                function test_Columns()
                {
                         //at start Columns must be null
                        $this->assertEquals($this->object->Columns,array());

                        $this->test_AddColumn();

                }
                function test_SelectionType()
                {
                        $this->assertEquals($this->object->SelectionType,selSingle);
                        $this->assertEquals($this->object->SelectionType,$this->object->defaultSelectionType());
                        $this->object->SelectionType=selOneInterval;
                        $this->assertEquals($this->object->SelectionType,selOneInterval);
                }
                function test_SortAscending()
                {
                        $this->assertEquals($this->object->SortAscending,1);
                        $this->assertEquals($this->object->SortAscending,$this->object->defaultSortAscending());
                        $this->object->SortAscending=false;
                        $this->assertEquals($this->object->SortAscending,false);
                }
                function test_SortColumnIndex()
                {
                        $this->assertEquals($this->object->SortColumnIndex,-1);
                        $this->assertEquals($this->object->SortColumnIndex,$this->object->defaultSortColumnIndex());
                        $this->object->SortColumnIndex=false;
                        $this->assertEquals($this->object->SortColumnIndex,false);
                }
                function test_addItem()
                {
                        $subitems=array();
                        $subitems[1]="uno";
                        $this->object->AddItem("uno");
                        $this->object->AddItem("dos");
                        $this->object->AddItem("tres");
                        $num=count($this->object->Items);
                        $this->assertEquals($num,3,"AddItem test 1");
                        $this->test_Items();
                }

                function test_deleteItem()
                {
                        $this->object->AddItem("uno");
                        $this->object->AddItem("dos");
                        $this->object->AddItem("tres");
                        $this->object->deleteItem("uno");
                        $num=count($this->object->Items);
                        $this->assertEquals($num,2);
                }
                function test_deleteSelected()
                {
                        $this->object->AddItem("uno",array(),true);
                        $this->object->AddItem("dos");
                        $this->object->AddItem("tres");
                        $num=count($this->object->Items);
                        $this->assertEquals($num,3);
                        $this->object->deleteSelected();
                        $num=count($this->object->Items);
                        $this->assertEquals($num,2);
                }
                function test_clearSelected()
                {
                        $this->object->AddItem("uno",array(),true);
                        $this->object->AddItem("dos",array(),true);
                        $this->object->AddItem("tres");
                        $this->object->clearSelected();
                        $this->assertEquals($this->object->clearSelected(),null);
                }
                function test_selectAll()
                {
                        $this->object->AddItem("uno",array(),false);
                        $this->object->AddItem("dos",array(),false);
                        $this->object->AddItem("tres");
                        $this->object->selectAll();
                        $Items=$this->object->Items;
                        $this->assertEquals($Items[0]->Selected &
                                $Items[0]->Selected &
                                $Items[0]->Selected,1);

                }
                function test_convertPureArrayToColumns()
                {
                        $columnsarray=array("column1","column2");
                        $col1=new ListColumn();
                        $col1->Caption="column1";
                        $col2=new ListColumn();
                        $col2->Caption="column2";
                        $result=$this->object->convertPureArrayToColumns($columnsarray);
                        $this->assertEquals(array($col1,$col2),$result);
                }
                function test_convertPureArrayToItems()
                {
                        $itemsarray=array();
                        $itemsarray['item1']=array("micaption","subitem2");
                        $result=$this->object->convertPureArrayToItems($itemsarray);
                        $Item=new ListItem();
                        $Item->Caption="micaption";
                        $Item->SubItems=array("subitem2");
                        $this->assertEquals(array($Item),$result);
                }

}

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_customlistview.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "CustomListViewTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
