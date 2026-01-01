<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_customlistview.inc.php";
        require_once "test_customconnection.inc.php";
        require_once "../mysqlcfg.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("comctrls.inc.php");
        use_unit("dbgrids.inc.php");
        use_unit("mysql.inc.php");
        use_unit("grids.inc.php");
        use_unit("dbtables.inc.php");



        class DBGridTest extends CustomListViewTest
        {
                function setup()
                {
                        $this->object=new DBGrid();
                        $this->object->Name="myobject";
                }
                function test_dumpChildren()
                {
                        if($this->object->className() =="DBGrid")
                                $this->checkDumpChildren();
                        else
                        {
                                $this->assertEquals(true,false);
                        }
                }

                function test_dumpContents()
                {
                        $obj=new Component();
                        $this->object->owner=$obj;
                        $c=$this->object->show(true);
                        //print_r($c);
                        $c=$this->fixString($c);
                        $c=str_replace(array(" ","\r","\n"),"",$c);
                        $compare=str_replace(array(" ","\r","\n"),"",'<input type="hidden" id="myobject_state" name="myobject_state" value="" />
<script type="text/javascript">
            // table model
            var myobject_tableModel = new qx.ui.table.SimpleTableModel();
        var myobject    = new qx.ui.table.Table(myobject_tableModel);
        myobject.setBorder(qx.renderer.border.BorderPresets.getInstance().shadow);
        myobject.setBackgroundColor("white");
        myobject.setLeft(0);
        myobject.setTop(0);
        myobject.setWidth(400);
        myobject.setHeight(200);
        myobject.getSelectionModel().setSelectionMode(qx.ui.table.SelectionModel.MULTIPLE_INTERVAL_SELECTION);
                function myobject_rpcdatachanged(event)
                {

                        var obj;
                        obj=this;
                        data=event.getData();
                        modifiedRow=data.firstRow;

                        row=this.getRowData(modifiedRow);
                        orow=this.originalData[modifiedRow];

                        qx.Settings.setCustomOfClass("qx.io.Json", "enableDebug", true);

                        var rpc = new qx.io.remote.Rpc();
                        rpc.setTimeout(10000);
                        var mycall = null;
                        rpc.setUrl("/test_dbgrid.inc.php");
                        rpc.setServiceName(".myobject");
                        rpc.setCrossDomain(false);

                        mycall = rpc.callAsync
                        (
                                function(result, ex, id)
                                {
                                    mycall = null;
                                    event=new Object();
                                    event.result=result;
                                    event.ex=ex;
                                    event.id=id;

                                    if (result>=0)
                                    {
                                        if (obj)
                                        {
                                            row=obj.getRowData(result);
                                            if (row)
                                            {
                                                obj.originalData[result]=row.slice();
                                            }
                                        }
                                    }
                                    send.setEnabled(true);
                                    abort.setEnabled(false);
                                }
                        , "updateRow", this.ColumnNames, row, orow, modifiedRow
                        );
                }
        myobject_tableModel.addEventListener("dataChanged", myobject_rpcdatachanged, myobject_tableModel);
</script>');
                        $this->assertEquals(trim($c),$compare);
                }



                function test_addOtherChildren()
                {
                        if ($this->object->className()!="DBGrid")
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
                        $this->object->jsOnSelectionChanged="myevent";
                        $this->assertEquals($this->object->jsOnSelectionChanged,"myevent");
                }


                function test_Items()
                {
                         //at start Items must be null
                        $this->assertEquals($this->object->Items,array());

                         //Create a ItemList to link to a several main menues and check if everything is ok
                        $Items=array(
                                 0=>"Item1",1=>"Item2",2=>"Item3");

                        //Read/write items check
                        $CustomListView1= new CustomListView();
                        $CustomListView2= new CustomListView();
                        $CustomListView1->Items=$Items;
                        $CustomListView2->Items=$Items;
                        $this->assertEquals($CustomListView1->Items,$CustomListView2->Items,"a");
                        $this->assertEquals($Items,$CustomListView1->Items,"b");
                }

                function test_Columns()
                {
                         //at start Columns must be null
                        $this->assertEquals($this->object->Columns,array());

                        //Working with columns checked in test_addColumn

                }
                function test_SelectionType()
                {
                        $this->assertEquals($this->object->SelectionType,$this->object->defaultSelectionType());
                        $this->object->SelectionType=selOneInterval;
                        $this->assertEquals($this->object->SelectionType,selOneInterval);
                }
                function test_SortAscending()
                {
                        $this->assertEquals($this->object->SortAscending,$this->object->defaultSortAscending());
                        $this->object->SortAscending=false;
                        $this->assertEquals($this->object->SortAscending,false);
                }
                function test_SortColumnIndex()
                {
                        $this->assertEquals($this->object->SortColumnIndex,$this->object->defaultSortColumnIndex());
                        $this->object->SortColumnIndex=false;
                        $this->assertEquals($this->object->SortColumnIndex,false);
                }
                function test_ReadOnly()
                {
                        $this->assertEquals($this->object->ReadOnly,0);
                        $this->assertEquals($this->object->defaultReadOnly(),$this->object->ReadOnly);
                        $this->object->ReadOnly=1;
                        $this->assertEquals($this->object->ReadOnly,1);
                }
               function test_DataSource()
               {
                        $myObj=new Object();
                        $this->assertEquals($this->object->DataSource,null);
                        $this->object->DataSource=$myObj;
                        $this->assertEquals($this->object->DataSource, $myObj);
               }
                function test_jsOnDataChanged()
                {
                        $this->assertEquals($this->object->defaultjsOnDataChanged(),$this->object->jsOnDataChanged,"b");
                        $this->object->jsOnDataChanged=1;
                        $this->assertEquals($this->object->jsOnDataChanged,1);
                }

                function test_jsOnRowSaved()
                {
                        $this->assertEquals($this->object->defaultjsOnOnRowSaved(),$this->object->jsOnRowSaved);
                        $this->object->jsOnRowSaved=1;
                        $this->assertEquals($this->object->jsOnRowSaved,1);
                }
                function test_jsOnRowChanged()
                {
                        $this->assertEquals($this->object->defaultjsOnOnRowChanged(),$this->object->jsOnRowChanged);
                        $this->object->jsOnRowChanged=1;
                        $this->assertEquals($this->object->jsOnRowChanged,1);
                }
                function test_FixedColumns()
                {
                        $this->assertEquals($this->object->FixedColumns,0);
                        $this->assertEquals($this->object->defaultFixedColumns(),$this->object->FixedColumns);
                        $this->object->FixedColumns=1;
                        $this->assertEquals($this->object->FixedColumns,1);
                }
                function test_dumpForAjax()
                {
                        //dumpForAjax just calls commonScript
                        $this->test_CommonScript();
                }
                function test_commonScript()
                {
                        ob_start();
                        $this->object->commonScript();
                        $actual=ob_get_contents();
                        ob_end_clean();
                        //print_r($actual);
                        $expected=trim('myobject.setBorder(qx.renderer.border.BorderPresets.getInstance().shadow);
        myobject.setBackgroundColor("white");
        myobject.setLeft(0);
        myobject.setTop(0);
        myobject.setWidth(400);
        myobject.setHeight(200);
        myobject.getSelectionModel().setSelectionMode(qx.ui.table.SelectionModel.MULTIPLE_INTERVAL_SELECTION);');

                        $actual=$this->fixString($actual);
                        $this->assertEquals($expected,$actual);
                }

                function test_dumpRPC()
                {
                          $this->assertEquals($this->object->ControlState,0);
                          $this->assertEquals($this->object->jsondatachanged,null);
                }
                function test_updateControl()
                {
                        $obj=new dbgrid();
                        $this->object->updateControl($obj);
                        $this->assertEquals($this->object->updateControl($obj),$this->object->updateControl($obj));
                        $this->assertEquals($this->object->dataSource,null);
                        $this->object->ControlState='csDesigning';
                        $this->assertEquals($this->object->ControlState,'csDesigning');
                }
                function test_createTableModel()
                {
                        $this->object->name="myobject";
                        $this->assertEquals($this->object->name,"myobject");
                }
                function test_updateRow()
                {

                        $dset=new MySQLTable();
                        $db=new MySQLDataBase();
                        $dsrc=new DataSource();
                        $dsrc->DataSet=$dset;
                        $this->object->DataSource=$dsrc;

                        $db->Host=GetMySQLHost();
                        $db->Username=GetMySQLUser();
                        $db->UserPassword=GetMySQLPassword();
                        $db->DatabaseName=GetMySQLDataBaseName();
                        $db->Connected=true;
                        $dset->Database=$db;
                        $dset->TableName=GetMySQLTableName();
                        $dset->Open();

                        /*$params=array();
                        $params[0]="fieldname";
                        $params[1]="fieldvalue";
                        $params[2]="origvalues";
                        $params[3]="dbgridrow";
                        $error="ERROR..";
                        $this->object->updateRow($params,$error);*/
                }


}

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_dbgrid.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "DBGridTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
