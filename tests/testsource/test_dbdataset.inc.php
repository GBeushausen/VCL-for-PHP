<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_dataset.inc.php";
        use_unit("db.inc.php");


        class DBDataSetTest extends DataSetTest
        {
                function setup()
                {

                        $this->object=new DBDataSet();
                        $this->object->Name="myobject";
                }
                function test_Edit()
                {
                        //Can´t be testest at this level. BuildQuery not yet defined
                        if($this->object->className()!="IBDataSet")
                                $this->assertEquals(true,false);

                }

                function test_Insert()
                {
                        //Can´t be testest at this level. BuildQuery not yet defined
                        if($this->object->className()!="IBDataSet")
                                $this->assertEquals(true,false);

                }

                function test_Cancel()
                {
                        //Can´t be testest at this level. BuildQuery not yet defined
                        if($this->object->className()!="IBDataSet")
                                $this->assertEquals(true,false);

                }
                function test_Delete()
                {
                        //Can´t be testest at this level. BuildQuery not yet defined
                        if($this->object->className()!="IBDataSet")
                                $this->assertEquals(true,false);

                }

                function test_Last()
                {
                        //Can´t be testest at this level. BuildQuery not yet defined
                        if($this->object->className()!="IBDataSet")
                                $this->assertEquals(true,false);

                }

                function test_Open()
                {
                        //Can´t be testest at this level. BuildQuery not yet defined
                        if($this->object->className()!="IBDataSet")
                                $this->assertEquals(true,false);

                }
                function test_Post()
                {
                        //Can´t be testest at this level. BuildQuery not yet defined
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

                function test_readFields()
                {

                        //Needs to resolve Actgive property in order to access _rs
                }
                function test_readEOF()
                {
                        //Needs to resolve Actgive property in order to access _rs

                }
        }

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

        if (basename($script)=='test_dbdataset.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "DBDataSetTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }
?>
