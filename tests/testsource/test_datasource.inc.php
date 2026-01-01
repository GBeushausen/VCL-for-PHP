<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_component.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("actnlist.inc.php");

        class DataSourceTest extends ComponentTest
        {
                function setup()
                {
                        $this->object=new DataSource();
                        $this->object->Name="myobject";
                }
                function test_readDataSet()
                {
                        //Tested in test_DataSet
                }
                function test_writeDataSet()
                {
                        //Tested in test_DataSet
                }
                function test_DataSet()
                {
                        $this->assertEquals($this->object->getDataSet(),null);
                        $dset=new DataSet();
                        $this->object->DataSet=$dset;
                        $this->assertEquals($this->object->DataSet,$dset);

                }



         }
      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_datasource.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "DataSourceTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
