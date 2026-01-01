<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_custommysqlquery.inc.php";
        use_unit("mysql.inc.php");


        class MySQLQueryTest extends CustomMySQLQueryTest
        {
                function setup()
                {

                        $this->object=new MySQLQuery();
                        $this->object->Name="myobject";
                }

                function test_getParams()
                {
                        //Checked in CustomMysqlquery
                }
                function test_setParams()
                {
                        //Checked in CustomMysqlquery
                }
                function test_getDatabase()
                {
                       //Checked in DataSet
                }
                function test_setDatabase()
                {
                        //Checked in CustomMysqlquery
                }
          }

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

        if (basename($script)=='test_mysqlquery.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "MySQLQueryTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }
?>
