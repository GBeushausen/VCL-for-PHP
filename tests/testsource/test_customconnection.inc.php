<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_component.inc.php";
        use_unit("auth.inc.php");

        class TestDataset extends Dataset
        {
          public $fired=false;

          function dataEvent($event, $info)
          {
            $this->fired=true;
          }
        }


        class CustomConnectionTest extends ComponentTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new CustomConnection();
                        $this->object->Name="myobject";
                }

                function test_OnAfterConnect()
                {
                        $this->assertEquals($this->object->defaultOnAfterConnect(),$this->object->OnAfterConnect);
                        $this->object->OnAfterConnect= 'myeventhandler';
                        $this->assertEquals($this->object->OnAfterConnect, 'myeventhandler');
                }
                function test_OnBeforeConnect()
                {
                        $this->assertEquals($this->object->defaultOnAfterConnect(),$this->object->OnAfterConnect);
                        $this->object->OnAfterConnect= 'myeventhandler';
                        $this->assertEquals($this->object->OnAfterConnect, 'myeventhandler');
                }
                function test_OnAfterDisconnect()
                {
                        $this->assertEquals($this->object->defaultOnAfterDisconnect(),$this->object->OnAfterDisconnect);
                        $this->object->OnAfterDisconnect= 'myeventhandler';
                        $this->assertEquals($this->object->OnAfterDisconnect, 'myeventhandler');
                }
                function test_OnBeforeDisconnect()
                {
                        $this->assertEquals($this->object->defaultOnAfterDisconnect(),$this->object->OnAfterDisconnect);
                        $this->object->OnAfterDisconnect= 'myeventhandler';
                        $this->assertEquals($this->object->OnAfterDisconnect, 'myeventhandler');
                }
                function test_OnLogin()
                {
                        $this->assertEquals($this->object->defaultOnLogin(),$this->object->OnLogin);
                        $this->object->OnLogin= 'myeventhandler';
                        $this->assertEquals($this->object->OnLogin, 'myeventhandler');
                }

                function test_readConnected(){/*checked in test_Property*/}
                function test_writeConnected(){/*checked in test_Property*/}

                function test_Connected()
                {
                        //conected property calls writeconnect that checks if we could connect or not to the database
                        //to do this we must overload DoConnect, that is not implemented at this level.
                        //So, it will be checked in the classes below

                        $this->assertEquals($this->object->defaultConnected(),$this->object->Connected,"a");

                        if($this->object->className()!="CustomConnection")
                                $this->assertEquals(true,false);
                }


                function test_DataSets()
                {
                        $this->assertEquals($this->object->DataSets,new Collection(),"Dataset test 1");
                        $this->assertEquals(null,$this->object->defaultDataSets(),"Default value must be changed to Collection()");
                        $DataSets=new Collection();
                        $DataSets->add(new DataSet());
                        $DataSets->add(new DataSet());
                        $this->object->DataSets=$DataSets;
                        $this->assertEquals($this->object->DataSets,$DataSets,"Dataset test 3");
                }

                function test_MetaFields()
                {
                        if($this->object->className()!="CustomConnection")
                                $this->assertEquals(true,false);
                }
                function test_BeginTrans()
                {
                        if($this->object->className()!="CustomConnection")
                                $this->assertEquals(true,false);
                }
                function test_CompleteTrans()
                {
                        if($this->object->className()!="CustomConnection")
                                $this->assertEquals(true,false);
                }

                function test_SendConnectEvent()
                {
                    $testd=new TestDataset();
                    $this->object->clients->add($testd);
                    $this->assertEquals($testd->fired,false,"Initial is false");
                    $this->object->sendConnectEvent(0,0);
                    $this->assertEquals($testd->fired,true,"Dataset must be notified");
                }

                function test_Clients()
                {
                  $this->test_SendConnectEvent();
                }

                function test_DBDate(){/*no test*/}
                function test_Prepare(){/*no test*/}
                function test_Param(){/*no test*/}
                function test_QuoteStr(){/*no test*/}
                function test_Open(){/*tested in Connected property*/}
                function test_Close(){/*tested in Connected property*/}
                function test_DoConnect()
                {
                        if($this->object->className()!="CustomConnection")
                                $this->assertEquals(true,false);
                }
                function test_DoDisconnect()
                {
                        if($this->object->className()!="CustomConnection")
                                $this->assertEquals(true,false);
                }
        }
      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_customconnection.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "CustomConnectionTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
