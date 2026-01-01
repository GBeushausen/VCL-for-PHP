<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "../../jsval/formvalidator.inc.php";
        require_once "test_component.inc.php";

        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");


        class FormValidatorTest extends ComponentTest
        {
                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;
                        $this->object=new FormValidator();
                        $this->object->Name="myobject";
                }


                function test_dumpHeaderCode()
                {
                        $rules[]=array
                        (
                       'Control'=>'Edit1',
                       'Equals'=>'Edit2',
                       'ErrorMessage'=>'Edit1 and Edit2 must match and are required',
                       'ValidationType'=>'FieldsMustMatch',
                       'OnValidate' => 'mycallback',
                       'FieldName' => 'myfieldname',
                       'Required'=>true,
                       'MaxLength'=>'0',
                       'MinLength'=>'0',
                       'MaxValue'=>'0',
                       'MinValue'=>'0'
                        );

                        $this->object->Rules=$rules;

                        $this->aowner->insertComponent($this->object);
                        ob_start();
                        $this->object->dumpHeaderCode();
                        $c=ob_get_contents();
                        ob_clean();
                        $c=$this->cleanString($c);

                        $compare=$this->cleanString("<scriptlanguage=\"javascript\"src=\"/vcl-bin/jsval/jsval.js\"></script><scriptlanguage=\"javascript\"type=\"text/javascript\"><!--functionmyobject_validate(){varobj=findObj('Edit1');obj.required=1;obj.equals='Edit2';obj.err='Edit1andEdit2mustmatchandarerequired';obj.realname='myfieldname';obj.regexp=\"FieldsMustMatch\";obj.callback='mycallback';returnvalidateStandard(findObj('StandardOwner'));}--></script>");
                        $this->assertEquals($c,$compare);
                }

                function test_getRules() {}
                function test_setRules() {}

                function test_Rules()
                {

                        $this->object->setRules(array());
                        $this->assertEquals($this->object->getRules(),array());
                        $rules[]=array
                        (
                       'Control'=>'Edit1',
                       'Equals'=>'Edit2',
                       'ErrorMessage'=>'Edit1 and Edit2 must match and are required',
                       'ValidationType'=>'FieldsMustMatch',
                       'OnValidate' => 'mycallback',
                       'FieldName' => 'myfieldname',
                       'Required'=>true,
                       'MaxLength'=>'0',
                       'MinLength'=>'0',
                       'MaxValue'=>'0',
                       'MinValue'=>'0'
                        );
                        $this->object->Rules=$rules;
                        $this->assertEquals($this->object->getRules(),$rules);
                }

                function test_getValidateCompleteForm()
                {
                        $this->assertEquals($this->object->getValidateCompleteForm(),0,"a");
                        $this->object->setValidateCompleteForm(1);
                        $this->assertEquals($this->object->getValidateCompleteForm(),1,"b");
                }

                function test_setValidateCompleteForm()
                {
                        $this->object->setValidateCompleteForm(true);
                        $this->assertEquals($this->object->getValidateCompleteForm(),true);
                }

        }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_formvalidator.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "FormValidatorTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>
