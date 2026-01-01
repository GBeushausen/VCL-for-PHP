<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_mysqldataset.inc.php";
        use_unit("mysql.inc.php");
        use_unit("tests/mysqlcfg.inc.php");


        class CustomMySQLTableTest extends MySQLDataSetTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new CustomMySQLTable();
                        $this->object->Name="myobject";
                }

                function OpenDataset()
                {
                        $this->db=new MySQLDataBase();
                        $db=$this->db;

                        $db->Host=GetMySQLHost();
                        $db->Username=GetMySQLUser();
                        $db->UserPassword=GetMySQLPassword();
                        $db->DatabaseName=GetMySQLDataBaseName();
                        $db->Connected=true;
                        $this->object->Database=$db;
                        $this->object->TableName=GetMySQLTableName();
                        $this->object->Open();
                }

                function closeDataset()
                {
                      $this->object->Close();
                }

                function test_Edit()
                {
                        $this->OpenDataset();
                        $this->object->Edit();
                        $this->assertEquals($this->object->State,dsEdit);
                        $this->CloseDataset();
                }

                function test_Insert()
                {
                        $this->OpenDataset();
                        $this->object->Insert();
                        $this->assertEquals($this->object->State,dsInsert);
                        $this->CloseDataset();
                }

                function test_Append()
                {
                        $this->OpenDataset();
                        $this->object->Append();
                        $this->assertEquals($this->object->State,dsInsert);
                        $this->CloseDataset();
                }

                function test_Cancel()
                {
                        $this->OpenDataset();
                        $this->object->Insert();
                        $this->assertEquals($this->object->State,dsInsert);
                        $this->object->Cancel();
                        $this->assertEquals($this->object->State,dsBrowse);
                        $this->CloseDataset();
                }
                function test_Delete()
                {
                        $this->OpenDataset();
                        $this->object->Insert();
                        $this->object->id=1;
                        $this->object->nombre="pepe";
                        $this->object->direccion="test";
                        $this->object->Post();
                        $this->assertEquals($this->object->State,dsBrowse);
                        $this->object->refresh();
                        $this->assertEquals($this->object->RecordCount,1,'There must be just 1 record');
                        $this->object->Edit();
                        $this->object->id=1;
                        $this->object->Delete();
                        $this->object->refresh();
                        $this->assertEquals($this->object->RecordCount,0,'There must be 0 records');
                        $this->CloseDataset();
                }

                function test_Open()
                {
                        $this->OpenDataset();
                }

                function test_Post()
                {
                        $this->test_Delete();
                }

                function test_TableName()
                {
                        $this->assertEquals($this->object->TableName,$this->object->defaultTableName());
                        $this->object->TableName="MyName" ;
                        $this->assertEquals($this->object->TableName,"MyName");
                }

                function test_readFieldProperties()
                {
                        $this->OpenDataset();
                        $result=$this->object->readFieldProperties("prueba_db","nombre");
                        $this->assertEquals($result,array(),"Initially, no properties for this field");
                        $props=$this->object->Database->Dictionaryproperties;
                        $props['prueba_db']['nombre']['displaywidth']=100;
                        $props['prueba_db']['nombre']['displaylabel']="Name";
                        $this->object->Database->Dictionaryproperties=$props;
                        $result=$this->object->readFieldProperties("prueba_db","nombre");
                        $this->assertEquals($result,array(),"Initially, no properties for this field");
                        $this->closeDataset();

                }
                function test_buildQuery()
                {
                        $this->object->TableName="tablaprueba";
                        $result=$this->object->buildQuery();
                        $this->assertEquals($result,"select * from tablaprueba   ");
                }
                function test_readAssociativeFieldValues()
                {
                        $this->OpenDataset();
                        //Must resolve the ::Active problem
                        $array=array();
                        $array['id']="";
                        $array['nombre']="";
                        $array['direccion']="";
                        $this->assertEquals($this->object->readAssociativeFieldValues(),$array,"");
                        $this->closeDataset();
                }

                function test_KeyFields()
                {
                        require_once("..\mysqlcfg.inc.php");
                        $db=new MySQLDataBase();

                        $db->Host=GetMySQLHost();
                        $db->Username=GetMySQLUser();
                        $db->UserPassword=GetMySQLPassword();
                        $db->DatabaseName=GetMySQLDataBaseName();
                        $db->Connected=true;
                        $db->Execute("select * from tablaprueba");
                        $this->object->Database=$db;
                        $this->object->TableName="tablaprueba";
                        $c=$this->object->KeyFields;
                        $this->assertEquals(array("nombre"),$c);
                }
                function test_OrderField()
                {
                        $this->assertEquals($this->object->OrderField,$this->object->defaultOrderField());
                        $this->object->OrderField="Nombre" ;
                        $this->assertEquals($this->object->OrderField,"Nombre");
                }
                function test_Order()
                {
                        $this->assertEquals($this->object->Order,$this->object->defaultOrder());
                        $this->object->OrderField="Descendant" ;
                        $this->assertEquals($this->object->OrderField,"Descendant");

                }
        }

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

        if (basename($script)=='test_custommysqltable.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "CustomMySQLTableTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }
?>