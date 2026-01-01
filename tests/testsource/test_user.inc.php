<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_component.inc.php";
        use_unit("auth.inc.php");



        class UserTest extends ComponentTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new User();
                        $this->object->Name="myobject";
                }

                function test_readLogged(){/*Checked in test_Logged*/}
                function test_writeLogged(){/*Checked in test_Logged*/}

                function test_Logged()
                {
                        $this->assertEquals($this->object->Logged,null,"logged check 1");
                        //$this->assertEquals($this->object->Logged,$this->object->defaultLogged());
                        $this->object->Logged=true;
                        $this->assertEquals($this->object->Logged,true,"logged check 2");
                }

                function test_authenticate()
                {
                        if($this->object->className()!="User")
                                $this->assertEquals(true,false);
                }
        }
      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_user.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "UserTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
