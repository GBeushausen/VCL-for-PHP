<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_dataset.inc.php";
        use_unit("interbase.inc.php");


        class IBDataSetTest extends DataSetTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new IBDataSet();
                        $this->object->Name="myobject";
                }

                function test_Edit()
                {
                        if($this->object->className()!="IBDataSet")
                                $this->assertEquals(true,false);

                }

                function test_Insert()
                {
                        if($this->object->className()!="IBDataSet")
                                $this->assertEquals(true,false);

                }

                function test_Cancel()
                {
                        if($this->object->className()!="IBDataSet")
                                $this->assertEquals(true,false);

                }
                function test_Delete()
                {
                        if($this->object->className()!="IBDataSet")
                                $this->assertEquals(true,false);

                }

                function test_Last()
                {
                        if($this->object->className()!="IBDataSet")
                                $this->assertEquals(true,false);

                }

                function test_Open()
                {
                        if($this->object->className()!="IBDataSet")
                                $this->assertEquals(true,false);
                }

                function test_Post()
                {
                        if($this->object->className()!="IBDataSet")
                                $this->assertEquals(true,false);
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

        if (basename($script)=='test_ibdataset.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "IBDataSetTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }
?>
