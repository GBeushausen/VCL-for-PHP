<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_panel.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");
        use_unit("dbctrls.inc.php");


        class DBRepeaterTest extends PanelTest
        {
                function setup()
                {
                        $this->object=new DBRepeater();
                        $this->object->Name="myobject";                        
                }
               function test_Limit()
               {
                        $this->assertEquals($this->object->Limit,0);
                        $this->assertEquals($this->object->Limit,$this->object->defaultLimit());
                        $this->object->Limit='dummyEvent';
                        $this->assertEquals($this->object->Limit,'dummyEvent');
               }
                function test_getLimit()
                {
                        //Tested within test_Limit()
                }
                function test_setLimit()
                {
                        //Tested within test_Limit()
                }

                function test_dumpChildren()
                {
                        if($this->object->className() =="Button")
                                $this->assertEquals(true,false);
                }
                function test_getKind()
                {
                        //Tested within test_Kind()
                }
                function test_setKind()
                {
                        //Tested within test_Kind()
                }
                function test_getRestartDataset()
                {
                        //Tested within test_RestartDataSet()
                }
                function test_setRestartDataset()
                {
                        //Tested within test_RestartDataSet()
                }
               function test_RestartDataset()
               {
                        $this->assertEquals($this->object->RestartDataset,true);
                        $this->assertEquals($this->object->RestartDataset,$this->object->defaultRestartDataset());
                        $this->object->RestartDataset='dummyEvent';
                        $this->assertEquals($this->object->RestartDataset,'dummyEvent');
               }
               function test_Kind()
               {
                        $this->assertEquals($this->object->Limit,$this->object->defaultLimit());
                        $this->object->Kind='dummyEvent';
                        $this->assertEquals($this->object->Kind,'dummyEvent');
               }
               function test_DataSource()
               {
                        $myObj=new Object();
                        $this->object->DataSource=$myObj;
                        $this->assertEquals($this->object->DataSource, $myObj);
               }
                function test_dumpContents()
                {
                        $this->object->Name="DBRepeater1";
                        $c=$this->object->show(true);
                        //print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim("");
                        $this->assertEquals(trim($c),$compare);
                }

                function test_Layout()
                {
                        $this->assertEquals($this->object->Layout->Type ,"XY_LAYOUT","a");
                        $this->object->Layout->setType('FLOW_LAYOUT');
                        $newLayout=$this->object->Layout->Type;
                        $this->assertEquals($this->object->Layout->Type ,$newLayout,"b");
                        $this->object->Layout->setType('XY_LAYOUT');
                        $newLayout=$this->object->Layout->Type;
                        $this->assertEquals($this->object->Layout->Type ,$newLayout,"c");
                }

        }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_dbrepeater.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "DBRepeaterTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>
