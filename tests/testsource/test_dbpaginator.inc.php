<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_customcontrol.inc.php";
        require_once "test_customconnection.inc.php";
        require_once "test_component.inc.php";
        require_once "../mysqlcfg.inc.php";
        use_unit("controls.inc.php");
        use_unit("dbctrls.inc.php");
        use_unit("mysql.inc.php");
        use_unit("extctrls.inc.php");
        use_unit("db.inc.php");

        class DBPaginatorTest extends CustomControlTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new DBPaginator();
                        $this->object->name="myobject";
                }
                function test_DoConnect()
                {
                        $this->object=new MySQLDataBase();
                        $this->assertEquals((boolean)$this->object->Connected,false);
                        $this->object->Host=GetMySQLHost();
                        $this->object->Username=GetMySQLUser();
                        $this->object->UserPassword=GetMySQLPassword();
                        $this->object->DatabaseName=GetMySQLDataBaseName();
                        $this->object->DoConnect();
                        $this->assertEquals((boolean)$this->object->Connected,true);
                }

                function test_ControlStyle($styles=array())
                {
                        $styles["csRenderOwner"]=1;
                        $styles["csRenderAlso"]="StyleSheet";
                        parent::test_ControlStyle($styles);
                }
                function test_dumpChildren()
                {
                        if($this->object->className() !="DBPaginator")
                                $this->assertEquals(true,false);
                }
                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        //print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim('<table id="myobject_table" height="30" width="300" cellpadding="0" cellspacing="0"  style=" font-family: Verdana; font-size: 10px;  " >
<tr valign="middle">
  <td align="left"><a href="test_dbpaginator.inc.php?myobject=first">First</a></td>
  <td align="left"><a href="test_dbpaginator.inc.php?myobject=prev">Prev</a></td>
  <td align="right"><a href="test_dbpaginator.inc.php?myobject=next">Next</a></td>
  <td align="right"><a href="test_dbpaginator.inc.php?myobject=last">Last</a></td>
</tr>
</table>');
                        $this->assertEquals(trim($c),$compare);
                }
               function test_OnClick()
                {
                        $this->assertEquals($this->object->OnClick,null);
                        $this->assertEquals($this->object->OnClick,$this->object->defaultOnClick());
                        $this->object->OnClick='onClickEvent';
                        $this->assertEquals($this->object->OnClick,'onClickEvent');
                }

               function test_DataSource()
               {
                        $myObj=new Object();
                        $this->assertEquals($this->object->DataSource,null);
                        $this->object->DataSource=$myObj;
                        $this->assertEquals($this->object->DataSource, $myObj);
               }
                function test_Orientation()
                {
                        $this->assertEquals($this->object->Orientation,$this->object->defaultOrientation());
                        $this->object->Orientation="pbVertical";
                        $this->assertEquals($this->object->Orientation,"pbVertical");
                }

                function test_CaptionFirst()
                {
                         //tested in test_Caption
                }
                function test_CaptionPrevious()
                {
                         //tested in test_Caption
                }
                function test_CaptionNext()
                {
                         //tested in test_Caption
                }
                function test_CaptionLast()
                {
                         //tested in test_Caption
                }

                function test_Caption()
                {
                        $this->assertEquals($this->object->Caption,'');
                        $this->assertEquals($this->object->Caption,$this->object->defaultCaption());
                        $this->object->Caption="holaaaa";
                        $this->assertEquals($this->object->Caption,'holaaaa');

                        //TODO: Check this, properties changing other properties on the IDE
                        $this->object->Name="Label1";
                        $this->assertEquals($this->object->Caption,'holaaaa');
                }
               function test_ShowFirst()
                {
                        $this->assertEquals($this->object->ShowFirst,1);
                        $this->assertEquals($this->object->ShowFirst,$this->object->defaultShowFirst());
                        $this->object->ShowFirst='onClickEvent';
                        $this->assertEquals($this->object->ShowFirst,'onClickEvent');
                }
               function test_ShowLast()
                {
                        $this->assertEquals($this->object->ShowLast,1);
                        $this->assertEquals($this->object->ShowLast,$this->object->defaultShowLast());
                        $this->object->ShowLast='onClickEvent';
                        $this->assertEquals($this->object->ShowLast,'onClickEvent');
                }
               function test_ShowNext()
                {
                        $this->assertEquals($this->object->ShowNext,1);
                        $this->assertEquals($this->object->ShowNext,$this->object->defaultShowNext());
                        $this->object->ShowNext='onClickEvent';
                        $this->assertEquals($this->object->ShowNext,'onClickEvent');
                }
               function test_ShowPrevious()
                {
                        $this->assertEquals($this->object->ShowPrevious,1);
                        $this->assertEquals($this->object->ShowPrevious,$this->object->defaultShowPrevious());
                        $this->object->ShowPrevious='onClickEvent';
                        $this->assertEquals($this->object->ShowPrevious,'onClickEvent');
                }
               function test_ShownRecordsCount()
                {
                        $this->assertEquals($this->object->ShownRecordsCount,10);
                        $this->assertEquals($this->object->ShownRecordsCount,$this->object->defaultShownRecordsCount());
                        $this->object->ShownRecordsCount='onClickEvent';
                        $this->assertEquals($this->object->ShownRecordsCount,'onClickEvent');
                }
               function test_PageNumberFormat()
                {
                        $this->assertEquals($this->object->PageNumberFormat,"%d");
                        $this->assertEquals($this->object->PageNumberFormat,$this->object->defaultPageNumberFormat());
                        $this->object->PageNumberFormat='onClickEvent';
                        $this->assertEquals($this->object->PageNumberFormat,'onClickEvent');
                }

                function test_linkClick()
                {
                        $ds=new datasource();
                        $dset=new dataset();
                        $dset->Open();
                        $this->object->datasource=$ds;
                        $this->object->datasource->dataset=$dset;
                        $action="first";
                        $this->object->linkClick($action);
                }
         }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_dbpaginator.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "DBPaginatorTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>
