<?php
        require_once "test_control.inc.php";
        require_once("vcl/vcl.inc.php");
        use_unit("PEAR/peardatagrid.inc.php");
        use_unit("controls.inc.php");
        use_unit("PEAR/Structures/DataGrid.php");


        class PearDataGridTest extends ControlTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new PearDataGrid();
                        $this->object->Name="myobject";
                }

                function test_dumpHeaderCode()
                {
                        $this->setup();

                        ob_start();
                        $this->object->dumpHeaderCode();
                        $c=ob_get_contents();
                        $c=$this->cleanString($c);
                        $compare=$this->cleanString("<styletype=\"text/css\"><!--table.datagrid{border-left:solid1px#330099;border-top:solid1px#330099;border-bottom:solid1px#330099;font-family:Verdana,Geneva,Arial,Helvetica,sans-serif;font-size:10px;border-collapse:collapse;width:100%;margin-left:1em;}table.datagridth{text-align:center;border-right:solid1px#330099;border-bottom:solid1px#330099;background:#330099;padding:2px;color:white;padding-left:1em;padding-right:1em;}table.datagridtha{color:white;text-decoration:none;}table.datagridtha:hover{color:#EEEEEE;}table.datagridtd{text-align:right;border-right:solid1px#330099;padding:2px;}table.datagridtr.odd{background:#F4F4F4;}div.datagrid_paging{font-weight:bold;font-family:Verdana,Geneva,Arial,Helvetica,sans-serif;font-size:11px;}div.datagrid_paginga{color:#330099;}--></style>");
                        $this->assertEquals($c,$compare);
                }

                function test_dumpContents()
                {
                        //print_r($c);
                        $this->object->DSN="mysql://root:root@192.168.0.128/oscommerce";
                        $this->object->SQL="select products_id, products_model, products_date_added from products";
                        $c=$this->object->show(true);
                        $c=$this->fixString($c);
                        $compare=trim('Missing DSN or SQL');
                        $this->assertEquals(trim($c),"");
                }

                function test_ShowPaginator()
                {
                        $this->assertEquals($this->object->ShowPaginator,"1");
                        $this->assertEquals($this->object->defaultShowPaginator(),$this->object->ShowPaginator);
                        $this->object->ShowPaginator=0;
                        $this->assertEquals($this->object->ShowPaginator,0);
                }
                function test_DSN()
                {
                        $this->assertEquals($this->object->DSN,"");
                        $this->assertEquals($this->object->defaultDSN(),$this->object->DSN);
                        $this->object->DSN="mysql://root:root@192.168.0.128/oscommerce";
                        $this->assertEquals($this->object->DSN,"mysql://root:root@192.168.0.128/oscommerce");
                }
                function test_RecordsPerPage()
                {
                        $this->assertEquals($this->object->RecordsPerPage,10);
                        $this->assertEquals($this->object->defaultRecordsPerPage(),$this->object->RecordsPerPage);
                        $this->object->RecordsPerPage=50;
                        $this->assertEquals($this->object->RecordsPerPage,50);
                }
                function test_SQL()
                {
                        $Items=array();
                        $this->assertEquals($this->object->SQL,$Items);
                        $this->assertEquals($this->object->defaultSQL(),$this->object->SQL);
                        $this->object->SQL="select products_id, products_model, products_date_added from products";
                        $this->assertEquals($this->object->SQL,"select products_id, products_model, products_date_added from products");
                }

function test_jsOnActivate                () { $this->assertEquals($this->object->jsOnActivate                ,null); $this->assertEquals($this->object->defaultjsOnActivate                (),$this->object->jsOnActivate                ); $this->object->jsOnActivate                ="heeee"; $this->assertEquals($this->object->jsOnActivate                ,'heeee'); }
function test_jsOnDeactivate              () { $this->assertEquals($this->object->jsOnDeactivate              ,null); $this->assertEquals($this->object->defaultjsOnDeactivate              (),$this->object->jsOnDeactivate              ); $this->object->jsOnDeactivate              ="heeee"; $this->assertEquals($this->object->jsOnDeactivate              ,'heeee'); }
function test_jsOnBeforeCopy              () { $this->assertEquals($this->object->jsOnBeforeCopy              ,null); $this->assertEquals($this->object->defaultjsOnBeforeCopy              (),$this->object->jsOnBeforeCopy              ); $this->object->jsOnBeforeCopy              ="heeee"; $this->assertEquals($this->object->jsOnBeforeCopy              ,'heeee'); }
function test_jsOnBeforeCut               () { $this->assertEquals($this->object->jsOnBeforeCut               ,null); $this->assertEquals($this->object->defaultjsOnBeforeCut               (),$this->object->jsOnBeforeCut               ); $this->object->jsOnBeforeCut               ="heeee"; $this->assertEquals($this->object->jsOnBeforeCut               ,'heeee'); }
function test_jsOnBeforeDeactivate        () { $this->assertEquals($this->object->jsOnBeforeDeactivate        ,null); $this->assertEquals($this->object->defaultjsOnBeforeDeactivate        (),$this->object->jsOnBeforeDeactivate        ); $this->object->jsOnBeforeDeactivate        ="heeee"; $this->assertEquals($this->object->jsOnBeforeDeactivate        ,'heeee'); }
function test_jsOnBeforeEditfocus         () { $this->assertEquals($this->object->jsOnBeforeEditfocus         ,null); $this->assertEquals($this->object->defaultjsOnBeforeEditfocus         (),$this->object->jsOnBeforeEditfocus         ); $this->object->jsOnBeforeEditfocus         ="heeee"; $this->assertEquals($this->object->jsOnBeforeEditfocus         ,'heeee'); }
function test_jsOnBeforePaste             () { $this->assertEquals($this->object->jsOnBeforePaste             ,null); $this->assertEquals($this->object->defaultjsOnBeforePaste             (),$this->object->jsOnBeforePaste             ); $this->object->jsOnBeforePaste             ="heeee"; $this->assertEquals($this->object->jsOnBeforePaste             ,'heeee'); }
function test_jsOnBlur                    () { $this->assertEquals($this->object->jsOnBlur                    ,null); $this->assertEquals($this->object->defaultjsOnBlur                    (),$this->object->jsOnBlur                    ); $this->object->jsOnBlur                    ="heeee"; $this->assertEquals($this->object->jsOnBlur                    ,'heeee'); }
function test_jsOnChange() { $this->assertEquals($this->object->jsOnChange,null); $this->assertEquals($this->object->defaultjsOnChange(),$this->object->jsOnChange             ); $this->object->jsOnChange             ="heeee"; $this->assertEquals($this->object->jsOnChange             ,'heeee'); }

function test_jsOnClick                   () { $this->assertEquals($this->object->jsOnClick                   ,null); $this->assertEquals($this->object->defaultjsOnClick                   (),$this->object->jsOnClick                   ); $this->object->jsOnClick                   ="heeee"; $this->assertEquals($this->object->jsOnClick                   ,'heeee'); }
function test_jsOnContextMenu             () { $this->assertEquals($this->object->jsOnContextMenu             ,null); $this->assertEquals($this->object->defaultjsOnContextMenu             (),$this->object->jsOnContextMenu             ); $this->object->jsOnContextMenu             ="heeee"; $this->assertEquals($this->object->jsOnContextMenu             ,'heeee'); }
function test_jsOnControlSelect           () { $this->assertEquals($this->object->jsOnControlSelect           ,null); $this->assertEquals($this->object->defaultjsOnControlSelect           (),$this->object->jsOnControlSelect           ); $this->object->jsOnControlSelect           ="heeee"; $this->assertEquals($this->object->jsOnControlSelect           ,'heeee'); }
function test_jsOnCopy                    () { $this->assertEquals($this->object->jsOnCopy                    ,null); $this->assertEquals($this->object->defaultjsOnCopy                    (),$this->object->jsOnCopy                    ); $this->object->jsOnCopy                    ="heeee"; $this->assertEquals($this->object->jsOnCopy                    ,'heeee'); }
function test_jsOnCut                     () { $this->assertEquals($this->object->jsOnCut                     ,null); $this->assertEquals($this->object->defaultjsOnCut                     (),$this->object->jsOnCut                     ); $this->object->jsOnCut                     ="heeee"; $this->assertEquals($this->object->jsOnCut                     ,'heeee'); }
function test_jsOnDblClick                () { $this->assertEquals($this->object->jsOnDblClick                ,null); $this->assertEquals($this->object->defaultjsOnDblClick                (),$this->object->jsOnDblClick                ); $this->object->jsOnDblClick                ="heeee"; $this->assertEquals($this->object->jsOnDblClick                ,'heeee'); }
function test_jsOnDrag                    () { $this->assertEquals($this->object->jsOnDrag                    ,null); $this->assertEquals($this->object->defaultjsOnDrag                    (),$this->object->jsOnDrag                    ); $this->object->jsOnDrag                    ="heeee"; $this->assertEquals($this->object->jsOnDrag                    ,'heeee'); }
function test_jsOnDragEnter               () { $this->assertEquals($this->object->jsOnDragEnter               ,null); $this->assertEquals($this->object->defaultjsOnDragEnter               (),$this->object->jsOnDragEnter               ); $this->object->jsOnDragEnter               ="heeee"; $this->assertEquals($this->object->jsOnDragEnter               ,'heeee'); }
function test_jsOnDragLeave               () { $this->assertEquals($this->object->jsOnDragLeave               ,null); $this->assertEquals($this->object->defaultjsOnDragLeave               (),$this->object->jsOnDragLeave               ); $this->object->jsOnDragLeave               ="heeee"; $this->assertEquals($this->object->jsOnDragLeave               ,'heeee'); }
function test_jsOnDragOver                () { $this->assertEquals($this->object->jsOnDragOver                ,null); $this->assertEquals($this->object->defaultjsOnDragOver                (),$this->object->jsOnDragOver                ); $this->object->jsOnDragOver                ="heeee"; $this->assertEquals($this->object->jsOnDragOver                ,'heeee'); }
function test_jsOnDragStart               () { $this->assertEquals($this->object->jsOnDragStart               ,null); $this->assertEquals($this->object->defaultjsOnDragStart               (),$this->object->jsOnDragStart               ); $this->object->jsOnDragStart               ="heeee"; $this->assertEquals($this->object->jsOnDragStart               ,'heeee'); }
function test_jsOnDrop                    () { $this->assertEquals($this->object->jsOnDrop                    ,null); $this->assertEquals($this->object->defaultjsOnDrop                    (),$this->object->jsOnDrop                    ); $this->object->jsOnDrop                    ="heeee"; $this->assertEquals($this->object->jsOnDrop                    ,'heeee'); }
function test_jsOnFilterChange            () { $this->assertEquals($this->object->jsOnFilterChange            ,null); $this->assertEquals($this->object->defaultjsOnFilterChange            (),$this->object->jsOnFilterChange            ); $this->object->jsOnFilterChange            ="heeee"; $this->assertEquals($this->object->jsOnFilterChange            ,'heeee'); }
function test_jsOnFocus                   () { $this->assertEquals($this->object->jsOnFocus                   ,null); $this->assertEquals($this->object->defaultjsOnFocus                   (),$this->object->jsOnFocus                   ); $this->object->jsOnFocus                   ="heeee"; $this->assertEquals($this->object->jsOnFocus                   ,'heeee'); }
function test_jsOnHelp                    () { $this->assertEquals($this->object->jsOnHelp                    ,null); $this->assertEquals($this->object->defaultjsOnHelp                    (),$this->object->jsOnHelp                    ); $this->object->jsOnHelp                    ="heeee"; $this->assertEquals($this->object->jsOnHelp                    ,'heeee'); }
function test_jsOnKeyDown                 () { $this->assertEquals($this->object->jsOnKeyDown                 ,null); $this->assertEquals($this->object->defaultjsOnKeyDown                 (),$this->object->jsOnKeyDown                 ); $this->object->jsOnKeyDown                 ="heeee"; $this->assertEquals($this->object->jsOnKeyDown                 ,'heeee'); }
function test_jsOnKeyPress                () { $this->assertEquals($this->object->jsOnKeyPress                ,null); $this->assertEquals($this->object->defaultjsOnKeyPress                (),$this->object->jsOnKeyPress                ); $this->object->jsOnKeyPress                ="heeee"; $this->assertEquals($this->object->jsOnKeyPress                ,'heeee'); }
function test_jsOnKeyUp                   () { $this->assertEquals($this->object->jsOnKeyUp                   ,null); $this->assertEquals($this->object->defaultjsOnKeyUp                   (),$this->object->jsOnKeyUp                   ); $this->object->jsOnKeyUp                   ="heeee"; $this->assertEquals($this->object->jsOnKeyUp                   ,'heeee'); }
function test_jsOnLoseCapture             () { $this->assertEquals($this->object->jsOnLoseCapture             ,null); $this->assertEquals($this->object->defaultjsOnLoseCapture             (),$this->object->jsOnLoseCapture             ); $this->object->jsOnLoseCapture             ="heeee"; $this->assertEquals($this->object->jsOnLoseCapture             ,'heeee'); }
function test_jsOnMouseDown               () { $this->assertEquals($this->object->jsOnMouseDown               ,null); $this->assertEquals($this->object->defaultjsOnMouseDown               (),$this->object->jsOnMouseDown               ); $this->object->jsOnMouseDown               ="heeee"; $this->assertEquals($this->object->jsOnMouseDown               ,'heeee'); }
function test_jsOnMouseUp                 () { $this->assertEquals($this->object->jsOnMouseUp                 ,null); $this->assertEquals($this->object->defaultjsOnMouseUp                 (),$this->object->jsOnMouseUp                 ); $this->object->jsOnMouseUp                 ="heeee"; $this->assertEquals($this->object->jsOnMouseUp                 ,'heeee'); }
function test_jsOnMouseEnter              () { $this->assertEquals($this->object->jsOnMouseEnter              ,null); $this->assertEquals($this->object->defaultjsOnMouseEnter              (),$this->object->jsOnMouseEnter              ); $this->object->jsOnMouseEnter              ="heeee"; $this->assertEquals($this->object->jsOnMouseEnter              ,'heeee'); }
function test_jsOnMouseLeave              () { $this->assertEquals($this->object->jsOnMouseLeave              ,null); $this->assertEquals($this->object->defaultjsOnMouseLeave              (),$this->object->jsOnMouseLeave              ); $this->object->jsOnMouseLeave              ="heeee"; $this->assertEquals($this->object->jsOnMouseLeave              ,'heeee'); }
function test_jsOnMouseMove               () { $this->assertEquals($this->object->jsOnMouseMove               ,null); $this->assertEquals($this->object->defaultjsOnMouseMove               (),$this->object->jsOnMouseMove               ); $this->object->jsOnMouseMove               ="heeee"; $this->assertEquals($this->object->jsOnMouseMove               ,'heeee'); }
function test_jsOnMouseOut                () { $this->assertEquals($this->object->jsOnMouseOut                ,null); $this->assertEquals($this->object->defaultjsOnMouseOut                (),$this->object->jsOnMouseOut                ); $this->object->jsOnMouseOut                ="heeee"; $this->assertEquals($this->object->jsOnMouseOut                ,'heeee'); }
function test_jsOnMouseOver               () { $this->assertEquals($this->object->jsOnMouseOver               ,null); $this->assertEquals($this->object->defaultjsOnMouseOver               (),$this->object->jsOnMouseOver               ); $this->object->jsOnMouseOver               ="heeee"; $this->assertEquals($this->object->jsOnMouseOver               ,'heeee'); }
function test_jsOnPaste                   () { $this->assertEquals($this->object->jsOnPaste                   ,null); $this->assertEquals($this->object->defaultjsOnPaste                   (),$this->object->jsOnPaste                   ); $this->object->jsOnPaste                   ="heeee"; $this->assertEquals($this->object->jsOnPaste                   ,'heeee'); }
function test_jsOnPropertyChange          () { $this->assertEquals($this->object->jsOnPropertyChange          ,null); $this->assertEquals($this->object->defaultjsOnPropertyChange          (),$this->object->jsOnPropertyChange          ); $this->object->jsOnPropertyChange          ="heeee"; $this->assertEquals($this->object->jsOnPropertyChange          ,'heeee'); }
function test_jsOnReadyStateChange        () { $this->assertEquals($this->object->jsOnReadyStateChange        ,null); $this->assertEquals($this->object->defaultjsOnReadyStateChange        (),$this->object->jsOnReadyStateChange        ); $this->object->jsOnReadyStateChange        ="heeee"; $this->assertEquals($this->object->jsOnReadyStateChange        ,'heeee'); }
function test_jsOnResize                  () { $this->assertEquals($this->object->jsOnResize                  ,null); $this->assertEquals($this->object->defaultjsOnResize                  (),$this->object->jsOnResize                  ); $this->object->jsOnResize                  ="heeee"; $this->assertEquals($this->object->jsOnResize                  ,'heeee'); }
function test_jsOnResizeEnd               () { $this->assertEquals($this->object->jsOnResizeEnd               ,null); $this->assertEquals($this->object->defaultjsOnResizeEnd               (),$this->object->jsOnResizeEnd               ); $this->object->jsOnResizeEnd               ="heeee"; $this->assertEquals($this->object->jsOnResizeEnd               ,'heeee'); }
function test_jsOnResizeStart             () { $this->assertEquals($this->object->jsOnResizeStart             ,null); $this->assertEquals($this->object->defaultjsOnResizeStart             (),$this->object->jsOnResizeStart             ); $this->object->jsOnResizeStart             ="heeee"; $this->assertEquals($this->object->jsOnResizeStart             ,'heeee'); }
function test_jsOnSelectStart             () { $this->assertEquals($this->object->jsOnSelectStart             ,null); $this->assertEquals($this->object->defaultjsOnSelectStart             (),$this->object->jsOnSelectStart             ); $this->object->jsOnSelectStart             ="heeee"; $this->assertEquals($this->object->jsOnSelectStart             ,'heeee'); }


        }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_peardatagrid.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "PearDataGridTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>




















