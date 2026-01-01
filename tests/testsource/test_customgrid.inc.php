<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_customcontrol.inc.php";
        require_once "../../grids.inc.php";
        use_unit("stdctrls.inc.php");

        class CustomGridTest extends CustomControlTest
        {
                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;
                        $this->object=new CustomGrid();
                        $this->object->Name="myobject";
                }
                function test_Columns()
                {
                         //at start Columns must be null
                        $this->assertEquals($this->object->Columns,array());
                        $a=array("column1","column2");
                        $this->object->columns=$a;
                        $this->assertEquals($this->object->Columns,$a);
                }

                function test_dumpContents()
                {
                        if($this->object->className()!="CustomGrid")
                                $this->assertEquals(true,false);
                }


         }
      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_customgrid.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "CustomGridTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
