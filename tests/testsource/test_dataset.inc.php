<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_component.inc.php";
        require_once "test_customconnection.inc.php";
        use_unit("controls.inc.php");
        use_unit("extctrls.inc.php");
        use_unit("db.inc.php");
        use_unit("mysql.inc.php");


        class DataSetTest extends ComponentTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new DataSet();
                        $this->object->Name="myobject";
                }

                function test_getLimitStart()
                {
                        //Tested in test_LimitStart
                }
                function test_setLimitStart()
                {
                        //Tested in within test_LimitStart
                }
                function test_LimitStart()
                {
                        $this->assertEquals($this->object->LimitStart,"0");
                        $this->assertEquals($this->object->LimitStart,$this->object->defaultLimitStart());
                        $this->object->LimitStart=0;
                        $this->assertEquals($this->object->LimitStart,0);
                }
                function test_getLimitCount()
                {
                        //Tested in test_LimitCount
                }
                function test_setLimitCount()
                {
                        //Tested in test_LimitCount
                }
                function test_LimitCount()
                {
                        $this->assertEquals($this->object->LimitCount,"10");
                        $this->assertEquals($this->object->LimitCount,$this->object->defaultLimitCount());
                        $this->object->LimitCount=0;
                        $this->assertEquals($this->object->LimitCount,0);
                }
                function test_InternalClose()
                {
                        //No need to test
                }
                function test_InternalHandleException()
                {
                        //No need to test
                }
                function test_InternalInitFieldDefs()
                {
                        //No need to test
                }
                function test_InternalOpen()
                {
                        //No need to test
                }
                function test_IsCursorOpen()
                {
                        //No need to test
                }
                function test_readFields()
                {
                        $a=array();
                        $this->assertEquals($this->object->readFields(),$a);
                }
                function test_readFieldCount()
                {
                        $this->assertEquals($this->object->readFieldCount(),0);
                        $this->object->readFieldCount(0);
                        $this->assertEquals($this->object->readFieldCount(),0);
                }
                function test_readDataSetField()
                {
                        //Tested in test_DataSetField
                }
                function test_writeDataSetField()
                {
                        //Tested in test_DataSetField
                }
                function test_DataSetField()
                {
                        $this->assertEquals($this->object->DataSetField,null);
                        $this->assertEquals($this->object->DataSetField,$this->object->defaultDataSetField());
                        $this->object->DataSetField=0;
                        $this->assertEquals($this->object->DataSetField,0);
                }
                function test_readState()
                {
                        //Tested in test_State
                }
                function test_writeState()
                {
                        //Tested in test_State
                }
                function test_State()
                {
                        $this->assertEquals($this->object->State,1);
                        $this->assertEquals($this->object->State,$this->object->defaultState());
                        $this->object->State=0;
                        $this->assertEquals($this->object->State,0);
                }
                function test_readMasterFields()
                {
                        //Tested in test_MasterFields
                }
                function test_writeMasterFields()
                {
                        //Tested in test_MasterFields
                }
                function test_MasterFields()
                {
                        $a=array();
                        $this->assertEquals($this->object->MasterFields,$a);
                        $this->assertEquals($this->object->MasterFields,$this->object->defaultMasterFields());
                        $this->object->MasterFields=0;
                        $this->assertEquals($this->object->MasterFields,0);
                }
                function test_readMasterSource()
                {
                        //Tested in test_MasterSource
                }
                function test_writeMasterSource()
                {
                        //Tested in test_MasterSource
                }
                function test_MasterSource()
                {
                        $this->assertEquals($this->object->MasterSource,null);
                        $this->object->MasterSource=0;
                        $this->assertEquals($this->object->MasterSource,0);
                }
                function test_readRecNo()
                {
                        //Tested in test_RecNo
                }
                function test_writeRecNo()
                {
                        //Tested in test_RecNo
                }
                function test_RecNo()
                {
                        $this->assertEquals($this->object->RecNo,0);
                        $this->assertEquals($this->object->RecNo,$this->object->defaultRecNo());
                        $this->object->RecNo=0;
                        $this->assertEquals($this->object->RecNo,0);
                }
                function test_readRecKey()
                {
                        //Tested in test_RecKey
                }
                function test_writeRecKey()
                {
                        //Tested in test_RecKey
                }
                function test_RecKey()
                {
                        $a=array();
                        $this->assertEquals($this->object->RecKey,$a);
                        $this->object->RecKey="";
                        $this->assertEquals($this->object->RecKey,$this->object->defaultRecKey(),"a");
                        $this->object->RecKey=0;
                        $this->assertEquals($this->object->RecKey,0);
                }
                function test_readModified()
                {
                        //Tested in test_Modified
                }
                function test_writeModified()
                {
                        //Tested in test_Modified
                }
                function test_Modified()
                {
                        $this->assertEquals($this->object->Modified,false);
                        $this->assertEquals($this->object->Modified,$this->object->defaultModified(),"a");
                        $this->object->Modified=0;
                        $this->assertEquals($this->object->Modified,0);
                }
                function test_readCanModify()
                {
                        //Tested in test_CanModify
                }
                function test_writeCanModify()
                {
                        //Tested in test_CanModify
                }
                function test_CanModify()
                {
                        $this->assertEquals($this->object->CanModify,true);
                        $this->assertEquals($this->object->CanModify,$this->object->defaultCanModify(),"a");
                        $this->object->CanModify=0;
                        $this->assertEquals($this->object->CanModify,0);
                }
                function test_readOnBeforeOpen()
                {
                        //Tested in test_OnBeforeOpen
                }
                function test_writeOnBeforeOpen()
                {
                        //Tested in test_OnBeforeOpen
                }
                function test_OnBeforeOpen()
                {
                        $this->assertEquals($this->object->OnBeforeOpen,null);
                        $this->assertEquals($this->object->OnBeforeOpen,$this->object->defaultOnBeforeOpen(),"a");
                        $this->object->OnBeforeOpen=0;
                        $this->assertEquals($this->object->OnBeforeOpen,0);
                }
                function test_readOnAfterOpen()
                {
                        //Tested in test_OnAfterOpen
                }
                function test_writeOnAfterOpen()
                {
                        //Tested in test_OnAfterOpen
                }
                function test_OnAfterOpen()
                {
                        $this->assertEquals($this->object->OnAfterOpen,null);
                        $this->assertEquals($this->object->OnAfterOpen,$this->object->defaultOnAfterOpen(),"a");
                        $this->object->OnAfterOpen=0;
                        $this->assertEquals($this->object->OnAfterOpen,0);
                }
                function test_readOnBeforeClose()
                {
                        //Tested in test_OnBeforeClose
                }
                function test_writeOnBeforeClose()
                {
                        //Tested in test_OnBeforeClose
                }
                function test_OnBeforeClose()
                {
                        $this->assertEquals($this->object->OnBeforeClose,null);
                        $this->assertEquals($this->object->OnBeforeClose,$this->object->defaultOnBeforeClose(),"a");
                        $this->object->OnBeforeClose=0;
                        $this->assertEquals($this->object->OnBeforeClose,0);
                }
                function test_readOnAfterClose()
                {
                        //Tested in test_OnAfterClose
                }
                function test_writeOnAfterClose()
                {
                        //Tested in test_OnAfterClose
                }
                function test_OnAfterClose()
                {
                        $this->assertEquals($this->object->OnAfterClose,null);
                        $this->assertEquals($this->object->OnAfterClose,$this->object->defaultOnAfterClose(),"a");
                        $this->object->OnAfterClose=0;
                        $this->assertEquals($this->object->OnAfterClose,0);
                }
                function test_readOnBeforeInsert()
                {
                        //Tested in test_OnBeforeInsert
                }
                function test_writeOnBeforeInsert()
                {
                        //Tested in test_OnBeforeInsert
                }
                function test_OnBeforeInsert()
                {
                        $this->assertEquals($this->object->OnBeforeInsert,null);
                        $this->assertEquals($this->object->OnBeforeInsert,$this->object->defaultOnBeforeInsert(),"a");
                        $this->object->OnBeforeInsert=0;
                        $this->assertEquals($this->object->OnBeforeInsert,0);
                }
                function test_readOnAfterInsert()
                {
                        //Tested in test_OnAfterInsert
                }
                function test_writeOnAfterInsert()
                {
                        //Tested in test_OnAfterInsert
                }
                function test_OnAfterInsert()
                {
                        $this->assertEquals($this->object->OnAfterInsert,null);
                        $this->assertEquals($this->object->OnAfterInsert,$this->object->defaultOnAfterInsert(),"a");
                        $this->object->OnAfterInsert=0;
                        $this->assertEquals($this->object->OnAfterInsert,0);
                }
                function test_readOnBeforeEdit()
                {
                        //Tested in test_OnBeforeEdit
                }
                function test_writeOnBeforeEdit()
                {
                        //Tested in test_OnBeforeEdit
                }
                function test_OnBeforeEdit()
                {
                        $this->assertEquals($this->object->OnBeforeEdit,null);
                        $this->assertEquals($this->object->OnBeforeEdit,$this->object->defaultOnBeforeEdit(),"a");
                        $this->object->OnBeforeEdit=0;
                        $this->assertEquals($this->object->OnBeforeEdit,0);
                }
                function test_readOnAfterEdit()
                {
                        //Tested in test_OnAfterEdit
                }
                function test_writeOnAfterEdit()
                {
                        //Tested in test_OnAfterEdit
                }
                function test_OnAfterEdit()
                {
                        $this->assertEquals($this->object->OnAfterEdit,null);
                        $this->assertEquals($this->object->OnAfterEdit,$this->object->defaultOnAfterEdit(),"a");
                        $this->object->OnAfterEdit=0;
                        $this->assertEquals($this->object->OnAfterEdit,0);
                }
                function test_readOnBeforePost()
                {
                        //Tested in test_OnBeforePost
                }
                function test_writeOnBeforePost()
                {
                        //Tested in test_OnBeforePost
                }
                function test_OnBeforePost()
                {
                        $this->assertEquals($this->object->OnBeforePost,null);
                        $this->assertEquals($this->object->OnBeforePost,$this->object->defaultOnBeforePost(),"a");
                        $this->object->OnBeforePost=0;
                        $this->assertEquals($this->object->OnBeforePost,0);
                }
                function test_readOnAfterPost()
                {
                        //Tested in test_OnAfterPost
                }
                function test_writeOnAfterPost()
                {
                        //Tested in test_OnAfterPost
                }
                function test_OnAfterPost()
                {
                        $this->assertEquals($this->object->OnAfterPost,null);
                        $this->assertEquals($this->object->OnAfterPost,$this->object->defaultOnAfterPost(),"a");
                        $this->object->OnAfterPost=0;
                        $this->assertEquals($this->object->OnAfterPost,0);
                }
                function test_readOnBeforeCancel()
                {
                        //Tested in test_OnBeforeCancel
                }
                function test_writeOnBeforeCancel()
                {
                        //Tested in test_OnBeforeCancel
                }
                function test_OnBeforeCancel()
                {
                        $this->assertEquals($this->object->OnBeforeCancel,null);
                        $this->assertEquals($this->object->OnBeforeCancel,$this->object->defaultOnBeforeCancel(),"a");
                        $this->object->OnBeforeCancel=0;
                        $this->assertEquals($this->object->OnBeforeCancel,0);
                }
                function test_readOnAfterCancel()
                {
                        //Tested in test_OnAfterCancel
                }
                function test_writeOnAfterCancel()
                {
                        //Tested in test_OnAfterCancel
                }
                function test_OnAfterCancel()
                {
                        $this->assertEquals($this->object->OnAfterCancel,null);
                        $this->assertEquals($this->object->OnAfterCancel,$this->object->defaultOnAfterCancel(),"a");
                        $this->object->OnAfterCancel=0;
                        $this->assertEquals($this->object->OnAfterCancel,0);
                }
                function test_readOnBeforeDelete()
                {
                        //Tested in test_OnBeforeDelete
                }
                function test_writeOnBeforeDelete()
                {
                        //Tested in test_OnBeforeDelete
                }
                function test_OnBeforeDelete()
                {
                        $this->assertEquals($this->object->OnBeforeDelete,null);
                        $this->assertEquals($this->object->OnBeforeDelete,$this->object->defaultOnBeforeDelete(),"a");
                        $this->object->OnBeforeDelete=0;
                        $this->assertEquals($this->object->OnBeforeDelete,0);
                }
                function test_readOnAfterDelete()
                {
                        //Tested in test_OnAfterDelete
                }
                function test_writeOnAfterDelete()
                {
                        //Tested in test_OnAfterDelete
                }
                function test_OnAfterDelete()
                {
                        $this->assertEquals($this->object->OnAfterDelete,null);
                        $this->assertEquals($this->object->OnAfterDelete,$this->object->defaultOnAfterDelete(),"a");
                        $this->object->OnAfterDelete=0;
                        $this->assertEquals($this->object->OnAfterDelete,0);
                }
                function test_readOnCalcFields()
                {
                        //Tested in test_OnCalcFields
                }
                function test_writeOnCalcFields()
                {
                        //Tested in test_OnCalcFields
                }
                function test_OnCalcFields()
                {
                        $this->assertEquals($this->object->OnCalcFields,null);
                        $this->assertEquals($this->object->OnCalcFields,$this->object->defaultOnCalcFields(),"a");
                        $this->object->OnCalcFields=0;
                        $this->assertEquals($this->object->OnCalcFields,0);
                }
                function test_readOnOnCalcFields()
                {
                        //Tested in test_OnDeleteError
                }
                function test_writeOnDeleteError()
                {
                        //Tested in test_OnDeleteError
                }
                function test_OnDeleteError()
                {
                        $this->assertEquals($this->object->OnDeleteError,null);
                        $this->assertEquals($this->object->OnDeleteError,$this->object->defaultOnDeleteError(),"a");
                        $this->object->OnDeleteError=0;
                        $this->assertEquals($this->object->OnDeleteError,0);
                }
                function test_readFilter()
                {
                        //Tested in test_Filter
                }
                function test_writeFilter()
                {
                        //Tested in test_Filter
                }
                function test_Filter()
                {
                        $this->assertEquals($this->object->Filter,"");
                        $this->assertEquals($this->object->Filter,$this->object->defaultFilter());
                }
                function test_readOnFilterRecord()
                {
                        //Tested in test_OnFilterRecord
                }
                function test_writeOnFilterRecord()
                {
                        //Tested in test_OnFilterRecord
                }
                function test_OnFilterRecord()
                {
                        $this->assertEquals($this->object->OnFilterRecord,null);
                        $this->assertEquals($this->object->OnFilterRecord,$this->object->defaultOnFilterRecord(),"a");
                        $this->object->OnFilterRecord=0;
                        $this->assertEquals($this->object->OnFilterRecord,0);
                }
                function test_readOnNewRecord()
                {
                        //Tested in test_OnNewRecord
                }
                function test_writeOnNewRecord()
                {
                        //Tested in test_OnNewRecord
                }
                function test_OnNewRecord()
                {
                        $this->assertEquals($this->object->OnNewRecord,null);
                        $this->assertEquals($this->object->OnNewRecord,$this->object->defaultOnNewRecord(),"a");
                        $this->object->OnNewRecord=0;
                        $this->assertEquals($this->object->OnNewRecord,0);
                }
                function test_readOnPostError()
                {
                        //Tested in test_OnPostError
                }
                function test_writeOnPostError()
                {
                        //Tested in test_OnPostError
                }
                function test_OnPostError()
                {
                        $this->assertEquals($this->object->OnPostError,null);
                        $this->assertEquals($this->object->OnPostError,$this->object->defaultOnPostError(),"a");
                        $this->object->OnPostError=0;
                        $this->assertEquals($this->object->OnPostError,0);
                }
                function test_readBOF()
                {
                        $this->assertEquals($this->object->readBOF(),false);
                }
                function test_readEOF()
                {
                        $this->assertEquals($this->object->readEOF(),false);
                }
                function test_readActive()
                {
                        //Tested in test_Active
                }
                function test_writeActive()
                {
                        //Tested in test_Active
                }
                function test_Active()
                {
                        $this->assertEquals($this->object->readActive(),0);
                        $this->object->Active=0;
                        $this->assertEquals($this->object->Active,0);
                }
                function test_Close()
                {
                        $this->object->Close();
                        $this->assertEquals($this->object->Active,0);
                }
                function test_Edit()
                {
                        $this->object->Open();
                        $this->object->Edit();
                        $this->assertEquals($this->object->State,dsEdit);
                }
                function test_Insert()
                {
                        $this->object->Open();
                        $this->object->Insert();
                        $this->assertEquals($this->object->State,dsInsert);
                }



                function test_CheckOperation()
                {
                        //No need to check
                }

                function test_DataEvent()
                {
                        //No need to check now
                }

                function test_CheckActive()
                {
                        $this->assertEquals($this->object->State,$this->object->defaultState());
                        $this->object->State=dsEdit;
                        $this->assertEquals($this->object->State,dsEdit);
                }

                function test_CheckCanModify()
                {
                        $this->assertEquals((boolean)$this->object->State,$this->object->defaultCanModify());
                        $this->object->writeCanModify(true);
                        $this->assertEquals($this->object->readCanModify(),true,"b");

                }

                function test_ControlsDisabled()
                {
                        $this->assertEquals($this->object->ControlsDisabled(),false);
                        //_DisabledControls is a privat member var, nothing more to check
                        //$this->object->DisableControls();
                        //$this->assertEquals($this->object->ControlsDisabled(),1);
                }

                /* Need to resolve _DisableState problem*/
                function test_DisableControls()
                {

                }
                function test_EnableControls()
                {
                }

                function test_ClearBuffers()
                {
                        $this->object->ClearBuffers();
                        $this->assertEquals($this->object->fieldbuffer,array());
                        $this->assertEquals($this->object->RecordCount,0);
                }
                function test_BeginInsertAppend()
                {
                        //nothing to test.
                }
                function test_EndInsertAppend()
                {
                        //nothing to test.
                }

                function test_Append()
                {
                        databaseError('Append cannot be tested at the Dataset level');
                }
                function test_Cancel()
                {
                        $this->object->Open();
                        $this->object->State=dsInsert;
                        $this->object->Cancel();
                        $this->assertEquals($this->object->State,dsBrowse);

                }
                function test_UpdateRecord()
                {
                        //NOthing to test
                }
                function test_CheckBrowseMode()
                {
                        //NOthing to test
                }

                function test_Delete()
                {
                        $this->object->Open();
                        $this->object->Insert();
                        $this->object->Delete();
                        $this->assertEquals($this->object->fieldbuffer,array());
                        $this->assertEquals($this->object->State,dsBrowse);
                }
                function test_CheckParentState()
                {
                        /*$this->object->DataSetField=new DataSet();
                        $this->object->CheckParentState();
                        $this->assertEquals($this->object->State,dsBrowse);*/
                }
                function test_readRecordCount()
                {
                        $this->assertEquals($this->object->RecordCount,0);
                }
                function test_First()
                {
                        //Nothing to check
                }
                function test_DoInternalOpen()
                {
                        //internal is private nothing to check
                }
                function test_OpenCursor()
                {
                        //internal is private nothing to check
                }
                function test_OpenCursorComplete()
                {
                        //internal is private nothing to check
                }
                function test_CloseCursor()
                {
                        //internal is private nothing to check
                }
                function test_InternalFirst()
                {
                        //internal is private nothing to check
                }
                function test_InternalLast()
                {
                        //internal is private nothing to check
                }
                function test_InitRecord()
                {
                        //Nothing to check
                }
                function test_InternalInitRecord()
                {
                        //internal is private nothing to check
                }
                function test_InternalAddRecord()
                {
                        //internal is private nothing to check
                }
                function test_InternalDelete()
                {
                        //internal is private nothing to check
                }
                function test_InternalPost()
                {
                        //internal is private nothing to check
                }
                function test_InternalCancel()
                {
                        //internal is private nothing to check
                }
                function test_InternalEdit()
                {
                        //internal is private nothing to check
                }
                function test_InternalInsert()
                {
                        //internal is private nothing to check
                }
                function test_InternalRefresh()
                {
                        //internal is private nothing to check
                }
                function test_Last()
                {
                        $this->object->Open();
                        $this->assertEquals($this->object->Last(),null);
                }
                function test_Refresh()
                {
                        //Checked in test_Active
                }
                function test_Next()
                {
                        //Can´t be rested _rs error
                        //$this->object->Open();
                        //$this->assertEquals($this->object->Next(),null);
                }
                function test_Open()
                {
                        $this->object->Open();
                        //Can´t be checked _rs error when trying to access active
                        //$this->assertEquals($this->object->Active,true);
                }
                function test_Post()
                {
                        $this->object->Open();
                        $this->object->Insert();
                        $this->object->Post();
                        $this->assertEquals($this->object->fieldbuffer,array());
                        $this->assertEquals($this->object->State,dsBrowse);
                }
                function test_MoveBy()
                {
                        //Nothing to check
                }
                function test_Prior()
                {
                        //Nothing to check
                }

        }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_dataset.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "DataSetTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>





















