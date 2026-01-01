<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_ibdataset.inc.php";
        require_once "../interbasecfg.inc.php";
        use_unit("interbase.inc.php");


        class CustomIBTableTest extends IBDataSetTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new CustomIBTable();
                        $this->object->Name="myobject";
                }

                function OpenDataset()
                {
                        $db=new IBDataBase();
                        $db->Host=GetIBHost();
                        $db->Username=GetIBUser();
                        $db->UserPassword=GetIBPassword();
                        $db->DatabaseName=GetIBDataBaseName();
                        $db->Connected=true;
                        $db->Execute("select * from " . GetIBTableName());
                        $this->object->Database=$db;
                        $this->object->TableName=GetIBTableName;
                        $this->object->SQL="select * from " . GetIBTableName();
                        $this->object->Open();
                }

                function test_Edit()
                {
                        //SQL wirte needs to be reviewed first
                        $this->assertEquals(true,false);
                        /*$this->OpenDataset();
                        $this->object->Edit();
                        $this->assertEquals($dset->State,dsEdit);*/

                }

                function test_Insert()
                {
                        //SQL wirte needs to be reviewed first
                        $this->assertEquals(true,false);

                }

                function test_Cancel()
                {
                        //SQL wirte needs to be reviewed first
                        $this->assertEquals(true,false);

                }
                function test_Delete()
                {
                        //SQL wirte needs to be reviewed first
                        $this->assertEquals(true,false);

                }

                function test_Last()
                {
                        //SQL wirte needs to be reviewed first
                        $this->assertEquals(true,false);

                }

                function test_Open()
                {
                        //SQL wirte needs to be reviewed first
                        $this->assertEquals(true,false);
                }

                function test_Post()
                {
                        //SQL wirte needs to be reviewed first
                        $this->assertEquals(true,false);
                }

                function test_readTableName()
                {
                        //CHecked in test_Property
                }
                function test_writeTableName()
                {
                        //CHecked in test_Property
                }
                function test_TableName()
                {
                        $this->assertEquals($this->object->TableName,$this->object->defaultTableName());
                        $this->object->TableName="MyName" ;
                        $this->assertEquals($this->object->TableName,"MyName");
                }

                function test_readFieldProperties()
                {
                        /*$dset=new CustomIBSQLTable();
                        $db=new MySQLDataBase();

                        $db->Host="192.168.0.129";
                        $db->Username="root";
                        $db->UserPassword="root";
                        $db->DatabaseName="miguel_db";
                        $db->Connected=true;
                        $dset->Database=$db;
                        $dset->TableName="tablaprueba";
                        $dset->Open();

                        $result=$this->object->readFieldProperties("prueba_db","nombre");*/
                        //Must resolve the ::Active problem
                        $this->assertEquals(true,false,"Must resolve the ::Active problem");
                }
                function test_buildQuery()
                {
                        $this->object->TableName=$TableName;
                        $result=$this->object->buildQuery();
                        $this->assertEquals($result,"select * from tablaprueba   ");
                }
                function test_readAssociativeFieldValues()
                {
                        //Must resolve the ::Active problem
                        $this->assertEquals(true,false,"Must resolve the ::Active problem");
                }
                function test_readKeyFields()
                {
                        //checked in test_property
                }
                function test_readOrderField()
                {
                        //checked in test_property
                }
                function test_writeOrderField()
                {
                        //checked in test_property
                }
                function test_readOrder()
                {
                        //checked in test_property
                }
                function test_writeOrder()
                {
                        //checked in test_property
                }

                function test_KeyFields()
                {
                        $db=new IBDataBase();
                        $db->Host=GetIBHost();
                        $db->Username=GetIBUser();
                        $db->UserPassword=GetIBPassword();
                        $db->DatabaseName=GetIBDataBaseName();
                        $db->Connected=true;
                        $db->Execute("select * from " . GetIBTableName());
                        $this->object->Database=$db;
                        $this->object->TableName=GetIBTableName();

                        $c=$this->object->KeyFields;
                        $this->assertEquals(array("NOMBRE"),$c);
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

        if (basename($script)=='test_customibtable.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "CustomIBTableTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }
?>
