<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_custommysqltable.inc.php";
        use_unit("mysql.inc.php");
        use_unit("tests/mysqlcfg.inc.php");


        class CustomMySQLQueryTest extends CustomMySQLTableTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new CustomMySQLQuery();
                        $this->object->Name="myobject";
                }

                function test_SQL()
                {
                        $this->assertEquals($this->object->SQL,$this->object->defaultSQL(),"a");
                        $this->object->SQL="select * from tablaprueba";
                        $this->assertEquals($this->object->SQL,"select * from tablaprueba","b");
                }

                function test_Prepare()
                {
                    $db=new MySQLDataBase();
                    $this->object->Database=$db;

                      //Just call the method
                      $this->object->SQL="select * from tablaprueba";
                      $this->object->prepare();
                      $this->object->Database=null;
                }

                function test_Params()
                {
                    $this->defaultTest('Params',array(),array('init'=>1));
                }

                function test_buildQuery()
                {
                    $this->object->sql="select * from tablaprueba";
                    $this->object->orderfield = "id";
                    $this->object->filter="id>=10";
                    $this->assertEquals(trim($this->object->buildQuery()),trim("select * from tablaprueba  where id>=10  order by id asc"),"b");
                }

                function OpenDataset()
                {
                        $db=new MySQLDataBase();

                        $db->Host=GetMySQLHost();
                        $db->Username=GetMySQLUser();
                        $db->UserPassword=GetMySQLPassword();
                        $db->DatabaseName=GetMySQLDataBaseName();
                        $db->Connected=true;
                        $this->object->Database=$db;
                        $this->object->TableName=GetMySQLTableName();
                        $this->object->SQL="select * from ".GetMySQLTableName();
                        $this->object->Open();
                }

        }

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

        if (basename($script)=='test_custommysqlquery.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "CustomMySQLQueryTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }
?>
