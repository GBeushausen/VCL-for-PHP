<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_dataset.inc.php";
        use_unit("interbase.inc.php");


        class CustomTableTest extends DataSetTest
        {
                function setup()
                {

                        $this->object=new CustomTable();
                        $this->object->Name="myobject";
                }

                function test_Edit()
                {
                        //Needs to fix Active
                        $this->assertEquals(true,false);

                }

                function test_Insert()
                {
                        //Needs to fix Active
                        $this->assertEquals(true,false);

                }

                function test_Cancel()
                {
                        //Needs to fix Active
                        $this->assertEquals(true,false);

                }
                function test_Delete()
                {
                        //Needs to fix Active
                        $this->assertEquals(true,false);

                }

                function test_Last()
                {
                        //Needs to fix Active
                        $this->assertEquals(true,false);

                }

                function test_Open()
                {
                        //Needs to fix Active
                        $this->assertEquals(true,false);
                }

                function test_Post()
                {
                        //Needs to fix Active
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
                        /*$dset=new CustomSQLTable();
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
                        $this->object->TableName="tablaprueba";
                        $result=$this->object->buildQuery();
                        $this->assertEquals($result,"select * from tablaprueba   ");
                }
                function test_readAssociativeFieldValues()
                {
                        //Must resolve the ::Active problem
                        $this->assertEquals(true,false,"Must resolve the ::Active problem");
                }
                function test_readFields()
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
                        //Need to fix Active property first.
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

                                function test_readDatabase()
                {

                //Checked in DataSource
                }
                function test_writeDatabase()
                {
                        //Checked in DataSource
                }
                function test_DataBase()
                {
                        $this->assertEquals($this->object->DataBase,null);
                        $DataBase=new DataBase();
                        $this->object->DataBase=$DataBase ;
                        $this->assertEquals($this->object->DataBase,$DataBase);
                }
                function test_CheckDatabase()
                {
                        //Nothing to check.
                }
        }

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

        if (basename($script)=='test_customtable.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "CustomTableTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }
?>
