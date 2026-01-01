<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_component.inc.php";
        use_unit("classes.inc.php");
        use_unit("stdctrls.inc.php");
        use_unit("actnlist.inc.php");
        use_unit("forms.inc.php");
        use_unit("controls.inc.php");
        use_unit("extctrls.inc.php");

        class ActionListTest extends ComponentTest
        {
                public $aowner=null;

                function setup()
                {
                        parent::setup();
                        global $application;
                        $this->aowner=$this->stowner;
                        $this->object=new ActionList(null);
                        $this->object->Name="myobject";
                }

                function test_init()
                {
                        $this->aowner->insertComponent($this->object);
                        //Test if OnExecute is called when should not be called
                        //because Actions is empty
                        $_POST['myobject']='test_action';
                        $this->aowner->executed=false;
                        $this->object->OnExecute="DummyEvent";
                        $this->object->init();
                        $this->assertEquals($this->aowner->executed,false,'Init test, empty Actions');

                        //Test if OnExecute is called when it should
                        $this->object->addAction('test_action');
                        $this->object->init();
                        $this->assertEquals($this->aowner->executed,true,'Init test, with Actions');
                        $this->aowner->removeComponent($this->object);
                }

                function test_addAction()
                {
                        $this->object->addAction('test_action');
                        $this->object->addAction('test_action_2');
                        $this->assertEquals(array(0=>'test_action',1=>'test_action_2'),$this->object->Actions,'Add action');

                        $this->object->deleteAction('test_action');
                        $this->assertEquals(array(0=>'test_action_2'),$this->object->Actions,'Delete action');

                        $this->object->deleteAction('test_action_2');
                        $this->assertEquals(array(),$this->object->Actions,'Delete action');
                }

                function test_deleteAction()
                {
                      $this->test_addAction();
                }

                function test_executeAction()
                {
                        $this->aowner->insertComponent($this->object);
                        $this->aowner->executed=false;
                        $this->object->OnExecute="DummyEvent";
                        $this->object->executeAction('test_action');
                        $this->assertEquals($this->aowner->executed,false,'execute action via command line, empty action list');

                        $this->object->addAction('test_action');
                        $this->object->executeAction('test_action');
                        $this->assertEquals($this->aowner->executed,true,'execute action via command line, empty action list');
                        $this->object->deleteAction('test_action');
                        $this->aowner->removeComponent($this->object);
                }

                function test_expandActionToURL()
                {
                        $url="http://www.google.com/index.php";
                        $result=$this->object->expandActionToUrl('test_action',$url);
                        $this->assertEquals($result,false,'Expand with no valid actions');

                        $this->object->addAction('test_action');
                        $result=$this->object->expandActionToUrl('test_action',$url);
                        $this->assertEquals($result,true,'Expand with valid actions');
                        $this->assertEquals($url,'http://www.google.com/index.php?myobject=test_action','Expand with valid actions, output url');

                        $url="http://www.google.com/index.php?default=var";
                        $result=$this->object->expandActionToUrl('test_action',$url);
                        $this->assertEquals($result,true,'Expand with valid actions');
                        $this->assertEquals($url,'http://www.google.com/index.php?default=var&myobject=test_action','Expand with valid actions, output url, initial values');
                }

                function test_getOnExecute()
                {
                        $this->test_init();
                }

                function test_setOnExecute()
                {
                        $this->test_init();
                }

                function test_getActions()
                {
                        $this->assertEquals(array(),$this->object->Actions,'Initially, must be empty');

                        $actions=array();
                        $actions[]="test_action";
                        $this->object->Actions=$actions;

                        $this->assertEquals(array(0=>'test_action'),$this->object->Actions,'Actions array must be correct');

                        $actions=array();
                        $actions[]="test_action_2";
                        $this->object->Actions=$actions;

                        $this->assertEquals(array(0=>'test_action_2'),$this->object->Actions,'Actions array must be completely changed');

                        $actions=$this->object->Actions;
                        $actions[]="test_action";
                        $this->object->Actions=$actions;

                        $this->assertEquals(array(0=>'test_action_2',1=>'test_action'),$this->object->Actions,'Actions array can be updated');
                }

                function test_setActions()
                {
                        $this->test_getActions();
                }

        }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_actionlist.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "ActionListTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>