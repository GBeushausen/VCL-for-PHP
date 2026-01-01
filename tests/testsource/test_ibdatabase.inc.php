<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_customconnection.inc.php";
        require_once "../interbasecfg.inc.php";
        use_unit("interbase.inc.php");


        class IBDatabaseTest extends CustomConnectionTest
        {
                function setup()
                {

                        $this->object=new IBDatabase();
                        $this->object->Name="myobject";
                }

                function test_DoConnect()
                {
                        $this->object->DoDisconnect();
                        $this->assertEquals((boolean)$this->object->Connected,false,"a");
                        $this->object->Host=GetIBHost();
                        $this->object->Username=GetIBUser();
                        $this->object->UserPassword=GetIBPassword();
                        $this->object->DatabaseName=GetIBDataBaseName();
                        $this->object->DoConnect();
                        $this->assertEquals((boolean)$this->object->Connected,true,"b");
                }

                function ClearDataBase()
                {
                        $this->test_DoConnect();
                        $this->object->execute("delete from " . GetIBTableName());
                }
                function InsertData()
                {
                        $this->test_DoConnect();
                        $this->object->execute("insert into ".GetIBTableName()." (NOMBRE,DIRECCION) values('antonio','virgen del pilar 5')");
                        $this->object->execute("insert into ".GetIBTableName()." (NOMBRE,DIRECCION) values('pepe','ronda sur, 5')");
                        $this->object->execute("insert into ".GetIBTableName()." (NOMBRE,DIRECCION) values('juanjo','espardenyer, 5')");
                }

                function test_MetaFields()
                {
                        $this->test_DoConnect();
                        $columns=array("ID                                                                 " =>"",
                                       "NOMBRE                                                             " =>"",
                                       "DIRECCION                                                          " =>"" );
                        $c=$this->object->MetaFields(GetIBTableName());
                        $this->assertEquals($columns,$c);

                }
                function test_BeginTrans()
                {
                        //this func is not implemented
                        if($this->object->className()!="IBDatabase")
                                $this->assertEquals(true,false);
                }
                function test_CompleteTrans()
                {
                        //this func is not implemented
                        if($this->object->className()!="IBDatabase")
                                $this->assertEquals(true,false);
                }


                function test_DoDisconnect()
                {
                        //Checked in test_DoConnect()
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
                        //A dictionary is a table in the db that defines how to build it
                        $this->assertequals($this->object->Dictionary,"");
                        $this->object->Dictionary="my dictionary string";
                        $this->assertequals($this->object->Dictionary,"my dictionary string");
                }

                function test_UserName()
                {
                        //A UserName is a table in the db that defines how to build it
                        $this->assertequals($this->object->UserName,"");
                        $this->object->UserName="myUserName";
                        $this->assertequals($this->object->UserName,"myUserName");
                }
                function test_DatabaseName()
                {
                        //A DatabaseName is a table in the db that defines how to build it
                        $this->assertequals($this->object->DatabaseName,"");
                        $this->object->DatabaseName="myDatabaseName";
                        $this->assertequals($this->object->DatabaseName,"myDatabaseName");
                }
                function test_Host()
                {
                        //A Host is a table in the db that defines how to build it
                        $this->assertequals($this->object->Host,"");
                        $this->object->Host="myHost";
                        $this->assertequals($this->object->Host,"myHost");
                }
                function test_UserPassword()
                {
                        //A UserPassword is a table in the db that defines how to build it
                        $this->assertequals($this->object->UserPassword,"");
                        $this->object->UserPassword="myUserPassword";
                        $this->assertequals($this->object->UserPassword,"myUserPassword");
                }
                function test_Dialect()
                {
                        //A Dialect is a table in the db that defines how to build it
                        $this->assertequals($this->object->Dialect,3);
                        $this->object->Dialect=1;
                        $this->assertequals($this->object->Dialect,1);
                }





                function test_PrepareSP() {/*Not implemented*/ }
                function getNumRows($rs)
                {
                        $numrows=0;
                        while ($row=ibase_fetch_row($rs))
                                ++$numrows;
                        return $numrows;
                }

                function test_execute()
                {
                        $this->test_DoConnect();
                        $this->ClearDataBase();
                        $this->InsertData();
                        $rs=$this->object->execute("select NOMBRE from ".GetIBTableName()." where NOMBRE='pepe'");
                        $this->assert($rs!=false);
                        $this->assertequals($this->getNumRows($rs),1);
                        $rs=$this->object->execute("select NOMBRE from ".GetIBTableName()." where NOMBRE='pee'");
                        $this->assert($rs!=false);
                        $this->assertequals($this->getNumRows($rs),0);
                        $rs=$this->object->execute("select NOMBRE from ".GetIBTableName());
                        $this->assert($rs!=false);
                        $this->assertequals($this->getNumRows($rs),3);
                        $this->object->execute("insert into ".GetIBTableName()." (NOMBRE,DIRECCION) values('antonio','virgen del pilar 5')");
                        $rs=$this->object->execute("select NOMBRE from ".GetIBTableName());
                        $this->assert($rs!=false);
                        $this->assertequals($this->getNumRows($rs),4);

                }

                function test_executelimit()
                {
                        $this->test_DoConnect();
                        $this->ClearDataBase();
                        $this->InsertData();
                        $rs=$this->object->executelimit("select * from ".GetIBTableName(),2,0);
                        $this->assert($rs!=false);
                        $this->assertequals($this->getNumRows($rs),2);
                        $rs=$this->object->executelimit("select * from ".GetIBTableName(),2,2);
                        $this->assert($rs!=false);
                        $this->assertequals($this->getNumRows($rs),1);

                }


                function test_extractIndexes()
                {
                        $this->test_DoConnect();
                        $Indexes=$this->object->extractIndexes(GetIBTableName(),true);
                        $expected=array
                        (
                                "IDX_".GetIBTableName()."                                                   " => array
                                (
                                        "unique" =>0,
                                        "columns" => array
                                        (
                                                0 => "NOMBRE                                                             "
                                        )

                                )
                        );

                        $this->assertequals($expected,$Indexes);
                }

                function test_databases()
                {
                        //not tested
                }


                function test_SendConnectEvent()
                {
                        //not need to check
                }

/*                function test_tables()
                {
                        $this->test_DoConnect();
                        $tablearray=$this->object->tables();
                }*/
                /*function test_DictionaryProperties()
                {
                        //A Dialect is a table in the db that defines how to build it
                        $this->assertequals($this->object->Dialect,"");
                        $this->object->Dialect="myDialect";
                        $this->assertequals($this->object->Dialect,"myDialect");
                }

                function test_createDictionaryTable()
                {
                        $this->test_DoConnect();
                        $this->object->CreateDictionaryTable();
                }
                function test_readFieldDictionaryProperties()
                {
                }*/
        }

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

        if (basename($script)=='test_ibdatabase.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "IBDatabaseTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }
?>
