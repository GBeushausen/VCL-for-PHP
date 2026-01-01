<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_customtable.inc.php";
        use_unit("interbase.inc.php");


        class CustomQueryTest extends CustomTableTest
        {
                function setup()
                {

                        $this->object=new CustomQuery();
                        $this->object->Name="myobject";
                }

                function test_readSQL()
                {
                        //checked in test_Property
                }
                function test_writeSQL()
                {
                        //checked in test_Property
                }
                function test_SQL()
                {
                        $this->assertEquals($this->object->SQL,$this->object->defaultSQL(),"a");
                        $this->object->SQL="select * from tablaprueba";
                        $this->assertEquals($this->object->SQL,"select * from tablaprueba","b");
                }

                function test_Prepare()
                {
                        //SQL write needs to be reviewd first
                        $this->assertEquals(true,false);
                }
                function test_readParams()
                {
                        //Checked in test_Property()
                }
                function test_writeParams()
                {
                        //Checked in test_Property()
                }
                function test_Params()
                {
                        $this->assertEquals($this->object->Params,array(),"a");
                        $this->object->Params="select * from tablaprueba";
                        $this->assertEquals($this->object->Params,"select * from tablaprueba","b");
                }

        }

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

        if (basename($script)=='test_customquery.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "CustomQueryTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }
?>
