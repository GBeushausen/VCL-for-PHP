<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_control.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");

        class HiddenFieldTest extends ControlTest
        {
                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;
                        $this->object=new HiddenField();
                        $this->object->name="myobject";
                }

                function test_preinit()
                {
                        $_POST['myobject']="myvalue";
                        $this->object->preinit();
                        $this->assertEquals($this->object->Value, "myvalue","Check Test 1");
                }

                function test_init()
                {
                        $this->aowner->insertComponent($this->object);

                        $this->assertEquals($this->aowner->executed,false,'Init test 1');

                        $_POST['myobjectSubmitEvent']='myobject_DummyEvent';
                        $this->aowner->executed=false;
                        $this->object->OnSubmit="DummyEvent";
                        $this->object->init();
                        $this->assertEquals($this->aowner->executed,true,'Init test 2');

                        $this->aowner->removeComponent($this->object);

                }

                function test_Value()
                {
                        $this->assertEquals($this->object->Value, '');
                        $this->assertEquals($this->object->defaultValue(),$this->object->Value);
                        $this->object->Value= 'new hidden field';
                        $this->assertEquals($this->object->Value, 'new hidden field');
                        $this->object->Value= 2544;
                        $this->assertEquals($this->object->Value, 2544);
                }

                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        //print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim('<input type="hidden" id="myobject" name="myobject" value="" />');
                        $this->assertEquals(trim($c),$compare);
                }
                 function test_OnSubmit()
                {
                        $this->assertEquals($this->object->OnSubmit, null);
                        $this->assertEquals($this->object->OnSubmit,$this->object->defaultOnSubmit());
                        $this->object->OnSubmit="Submitevent";
                        $this->assertEquals($this->object->OnSubmit, "Submitevent");
                }


         }
      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_hiddenfield.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "HiddenFieldTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
