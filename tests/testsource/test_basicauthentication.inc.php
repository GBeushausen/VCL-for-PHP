<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_component.inc.php";

        use_unit("auth.inc.php");

        class OwnerPage extends StandardOwner
        {
                function AuthOnAuthenticate($sender, $params)
                {
                        $this->executed=true;
                        return(true);
                }

        }

        class BasicAuthenticationTest extends ComponentTest
        {
                public $aowner=null;

                function setup()
                {
                        parent::setup();
                        global $application;
                        $this->aowner=new OwnerPage($application);
                        $this->aowner->Name="Form1";

                        $this->object=new BasicAuthentication();
                        $this->object->name="myobject";
                }

                function test_Password()
                {
                        $this->assertEquals($this->object->Password,"");
                        $this->assertEquals($this->object->Password,$this->object->defaultPassword());
                        $this->object->Password=10;
                        $this->assertEquals($this->object->Password,10);
                }
                function test_Username()
                {
                        $this->assertEquals($this->object->Username,"");
                        $this->assertEquals($this->object->Username,$this->object->defaultUsername());
                        $this->object->Username=10;
                        $this->assertEquals($this->object->Username,10);
                }
                function test_ErrorMessage()
                {
                        $this->assertEquals($this->object->ErrorMessage,"Unauthorized");
                        $this->assertEquals($this->object->ErrorMessage,$this->object->defaultErrorMessage());
                        $this->object->ErrorMessage=10;
                        $this->assertEquals($this->object->ErrorMessage,10);
                }
                function test_Title()
                {
                        $this->assertEquals($this->object->Title,"Login");
                        $this->assertEquals($this->object->Title,$this->object->defaultTitle());
                        $this->object->Title=10;
                        $this->assertEquals($this->object->Title,10);
                }
                function test_OnAuthenticate()
                {
                        $this->assertEquals($this->object->OnAuthenticate,null,'Initial value');
                        $this->object->OnAuthenticate=null;
                        $this->assertEquals($this->object->OnAuthenticate,$this->object->defaultOnAuthenticate(),'Default value');
                        $this->object->OnAuthenticate=10;
                        $this->assertEquals($this->object->OnAuthenticate,10,'Assignment');
                }

                function test_Execute()
                {
                        $_SERVER['PHP_AUTH_USER']='user';
                        $_SERVER['PHP_AUTH_PW']='password';
                        $this->object->username="user";
                        $this->object->password="password";
                        $result=$this->object->Execute();
                        $this->assertEquals($result,true,'Execute with user and password');

                        $this->aowner->insertComponent($this->object);
                        $this->aowner->executed=false;
                        $this->object->OnAuthenticate="AuthOnAuthenticate";
                        $this->object->username="";
                        $this->object->password="";
                        $result=$this->object->Execute();
                        $this->assertEquals($result,true,'Execute with event');
                        $this->assertEquals($this->aowner->executed,true,'Execute with event, event executed');
                        $this->aowner->removeComponent($this->object);
                }

        }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_basicauthentication.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "BasicAuthenticationTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>