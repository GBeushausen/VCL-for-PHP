<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_buttoncontrol.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");


        class ButtonTest extends ButtonControlTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new Button();
                        $this->object->name="myobject";
                }

                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        //print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim('<input type="submit" id="myobject" name="myobject" value=""  style=" font-family: Verdana; font-size: 10px;  height:25px;width:75px;"   tabindex="0"    />');
                        $this->assertEquals(trim($c),$compare);
                }

                function test_ButtonType()
                {
                        $this->assertEquals($this->object->ButtonType,'btSubmit');
                        $this->assertEquals($this->object->ButtonType,$this->object->defaultButtonType());
                        $this->object->ButtonType='btCancel';
                        $this->assertEquals($this->object->ButtonType,'btCancel');
                        $this->object->ButtonType='btNone';
                        $this->assertEquals($this->object->ButtonType,'btNone');
                }

                function test_Default()
                {
                        $this->assertEquals($this->object->Default,0);
                        $this->assertEquals($this->object->Default,$this->object->defaultDefault());
                        $this->object->Default="myevent";
                        $this->assertEquals($this->object->Default,"myevent");
                }

                function test_ImageSource()
                {
                        $this->assertEquals($this->object->ImageSource,'');
                        $this->object->ImageSource='new image source';
                        $this->assertEquals($this->object->ImageSource,'new image source');
                }

                function test_Action()
                {
                        $this->defaultTest('Action',null,'dummy value');
                }

                function test_getParentFont(){}
                function test_setParentFont(){}
                function test_getPopupMenu(){}
                function test_setPopupMenu(){}

        }

        if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
           else $script=$_GET['script'];

        if (basename($script)=='test_button.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "ButtonTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>
