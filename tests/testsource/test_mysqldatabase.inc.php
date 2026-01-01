<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_customconnection.inc.php";
        require_once "../mysqlcfg.inc.php";
        use_unit("mysql.inc.php");


        class MySQLDataBaseTest extends CustomConnectionTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new MySQLDataBase();
                        $this->object->Name="myobject";
                }

                /*************** HELPERS *************************/
                function GetConnected()
                {
                        $this->object->Host=GetMySQLHost();
                        $this->object->Username=GetMySQLUser();
                        $this->object->UserPassword=GetMySQLPassword();
                        $this->object->DatabaseName=GetMySQLDataBaseName();
                        $this->object->Connected=true;

                }

                function ClearTable($table)
                {
                        $this->test_DoConnect();
                        $this->object->execute("delete from " .$table);
                }
                function InsertDatabaseData()
                {
                        $this->test_DoConnect();
                        $this->object->execute("insert into ".GetMySQLTableName()." (nombre,direccion) values('juanico','virgen del pilar 5')");
                }

                function InsertDicData()
                {
                        $this->test_DoConnect();
                        $this->object->execute("insert into ".GetMySQLTableName()." (nombre,direccion) values('juanico','virgen del pilar 5')");
                }

                /**************************************************/

                function test_DoConnect()
                {
                        $this->object->Connected=false;
                        $this->assertEquals((boolean)$this->object->Connected,false,"a");
                        $this->object->Host=GetMySQLHost();
                        $this->object->Username=GetMySQLUser();
                        $this->object->UserPassword=GetMySQLPassword();
                        $this->object->DatabaseName=GetMySQLDataBaseName();
                        $this->object->Connected=true;
                        $this->assertEquals((boolean)$this->object->Connected,true,"b");
                }



                function test_Connected()
                {
                        $this->test_DoConnect();
                }

                function test_MetaFields()
                {
                        $this->test_DoConnect();
                        $columns=array("id" =>"",
                                       "nombre" =>"",
                                       "direccion" =>"");
                        $c=$this->object->MetaFields(GetMySQLTableName());
                        $this->assertEquals($columns,$c);
                }
                function test_BeginTrans()
                {
                        //this func is not implemented
                        if($this->object->className()!="MySQLDatabase")
                                $this->assertEquals(true,false);
                }
                function test_CompleteTrans()
                {
                        //this func is not implemented
                        if($this->object->className()!="MySQLDatabase")
                                $this->assertEquals(true,false);
                }


                function test_DoDisconnect()
                {
                        $this->test_DoConnect();
                }

                function test_getDebug() { /* Checked in test_Property */ }
                function test_setDebug() { /* Checked in test_Property */ }
                function test_getDictionary() { /* Checked in test_Property */ }
                function test_setDictionary() { /* Checked in test_Property */ }
                function test_getDatabaseName() { /* Checked in test_Property */ }
                function test_setDatabaseName() { /* Checked in test_Property */ }
                function test_getHost() { /* Checked in test_Property */ }
                function test_setHost() { /* Checked in test_Property */ }
                function test_getUserName() { /* Checked in test_Property */ }
                function test_setUserName() { /* Checked in test_Property */ }
                function test_getUserPassword() { /* Checked in test_Property */ }
                function test_setUserPassword() { /* Checked in test_Property */ }
                function test_getDialect() { /* Checked in test_Property */ }
                function test_setDialect() { /* Checked in test_Property */ }
                function test_readDictionaryProperties() { /* Checked in test_Property */ }
                function test_writeDictionaryProperties() { /* Checked in test_Property */ }

                function test_Debug()
                {
                        $this->assertequals($this->object->Debug,$this->object->defaultDebug());
                        $this->object->Debug=true;
                        $this->assertequals($this->object->Debug,true);
                }


                function test_Dictionary()
                {
                        $this->assertequals($this->object->Dictionary,"");
                        $this->object->Dictionary="mydicttable";
                        $this->assertequals($this->object->Dictionary,"mydicttable");
                }

                function test_UserName()
                {
                        $this->assertequals($this->object->UserName,"");
                        $this->object->UserName="myUserName";
                        $this->assertequals($this->object->UserName,"myUserName");
                }
                function test_DatabaseName()
                {
                        $this->assertequals($this->object->DatabaseName,"");
                        $this->object->DatabaseName="myDatabaseName";
                        $this->assertequals($this->object->DatabaseName,"myDatabaseName");
                }
                function test_Host()
                {
                        $this->assertequals($this->object->Host,"");
                        $this->object->Host="myHost";
                        $this->assertequals($this->object->Host,"myHost");
                }
                function test_UserPassword()
                {
                        $this->assertequals($this->object->UserPassword,"");
                        $this->object->UserPassword="myUserPassword";
                        $this->assertequals($this->object->UserPassword,"myUserPassword");
                }
                function test_Dialect()
                {
                        $this->assertequals($this->object->Dialect,3);
                        $this->object->Dialect=1;
                        $this->assertequals($this->object->Dialect,1);
                }


                function test_PrepareSP() {/*Not implemented*/ }
                function test_execute()
                {
                        $this->test_DoConnect();
                        $this->ClearTable(GetMySQLTableName());
                        $this->InsertDatabaseData();
                        $rs=$this->object->execute("select Nombre from ".GetMySQLTableName()." where nombre='pepe'");
                        $this->assert($rs!=false);
                        $this->assertequals(mysql_num_rows($rs),0);
                        $rs=$this->object->execute("select Nombre from ".GetMySQLTableName()." where nombre='pee'");
                        $this->assert($rs!=false);
                        $this->assertequals(mysql_num_rows($rs),0);
                        $rs=$this->object->execute("select Nombre from ".GetMySQLTableName());
                        $this->assert($rs!=false);
                        $this->assertequals(mysql_num_rows($rs),1);
                        $this->object->execute("insert into ".GetMySQLTableName()." (nombre,direccion) values('antonio','virgen del pilar 5')");
                        $rs=$this->object->execute("select Nombre from ".GetMySQLTableName());
                        $this->assert($rs!=false);
                        $this->assertequals(mysql_num_rows($rs),2);

                }

                function test_executelimit()
                {
                        $this->test_DoConnect();
                        $this->ClearTable(GetMySQLTableName());
                        $this->InsertDatabaseData();
                        $rs=$this->object->executelimit("select * from ".GetMySQLTableName(),2,0);
                        $this->assert($rs!=false);
                        $this->assertequals(mysql_num_rows($rs),1);
                        $rs=$this->object->executelimit("select * from ".GetMySQLTableName(),2,2);
                        $this->assert($rs!=false);
                        $this->assertequals(mysql_num_rows($rs),0);

                }


                function test_extractIndexes()
                {
                        $this->test_DoConnect();
                        $Indexes=$this->object->extractIndexes(GetMySQLTableName(),true);
                        $expected=array("nombre" => array
                                        (
                                            "unique" =>false,
                                            "columns" => array
                                            (
                                                    0 => "nombre"
                                            )
                                        )
                                    );
                        $this->assertequals($expected,$Indexes,"a");
                }

                function test_databases()
                {
                        $this->assertEquals($this->object->databases(),false);
                }

                function test_DBDate()
                {
                        $this->assertEquals($this->object->DBDate("10 September 2000"),"%2000-%09-%10");
                }

                function test_Param()
                {
                        $this->assertEquals($this->object->Param("myparam"),"'myparam'");
                }

                function test_QuoteStr()
                {
                        $this->test_Param();
                }


                function test_tables()
                {
                        $this->GetConnected();
                        $tablearray=$this->object->tables();
                        $this->assertEquals(array("tablaprueba"),$tablearray);

                }
                function test_DictionaryProperties()
                {
                        //There's a bug having connected and connection used to check
                        //if a we´re connected to database but they are not kept synched
                        $this->GetConnected();
                        $this->object->Dictionary="tabladict";
                        $this->object->createDictionaryTable();

                }

                function test_createDictionaryTable()
                {
                }
                function test_readFieldDictionaryProperties()
                {
                }
        }

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

        if (basename($script)=='test_mysqldatabase.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "MySQLDataBaseTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }
?>