<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_persistent.inc.php";
        require_once "test_object.inc.php";
        use_unit("classes.inc.php");
        use_unit("stdctrls.inc.php");
        use_unit("forms.inc.php");
        use_unit("controls.inc.php");
        use_unit("extctrls.inc.php");

        class StandardOwner extends Page
        {
                public $executed=false;

                function DummyEvent($sender, $params)
                {
                        $this->executed=true;
                }

                function AnotherEvent($sender, $params)
                {
                        $this->executed=true;
                }

                function ExtraEvent($sender, $params)
                {
                        $this->executed=true;
                }

        }

        class ComponentTest extends PersistentTest
        {
                public $stowner=null;

                function setup()
                {
                        parent::setup();
                        global $application;

                        $this->stowner=new StandardOwner($application);
                        $this->stowner->Name="StandardOwner";

                        $this->object=new Component();
                        $this->object->Name="myobject";
                }

                function test_Tag()
                {
                        $this->assertEquals($this->object->Tag,0);
                        $this->assertEquals($this->object->Tag,$this->object->defaultTag());
                        $this->object->Tag=10;
                        $this->assertEquals($this->object->Tag,10);
                }

                function test_ControlState()
                {
                        $this->assertEquals($this->object->ControlState,0);
                        $this->object->ControlState=csLoading;
                        $this->assertEquals($this->object->ControlState,csLoading);
                }

                function test_dumpFormItems()
                {
                        //Do nothing here
                }

                function test_dumpChildrenFormItems()
                {
                        //Do nothing here
                }

                function test_Name()
                {
                        $this->assertEquals('',$this->object->defaultName());
                        $this->assertEquals($this->object->Name,'myobject',"testnamea");

                        $this->object->Name="component1";
                        $this->assertEquals($this->object->Name,"component1","testnameb");

                        $own=new Component();
                        $obj=new Component($own);
                        $obj->Name="c1";
                        $aobj=new Component($own);
                        try
                        {
                                $aobj->Name="c1";

                                //This must not be executed
                                $this->assertEquals(0,1);
                        }
                        catch(Exception $e)
                        {
                                //Must be here
                                $this->assertEquals(1,1);
                        }
                }

                function test_Owner()
                {
                        $this->assertEquals($this->object->Owner,null);

                        $own=new Component();
                        $obj=new Component($own);
                        $obj->Name="c1";

                        $this->assertEquals(($obj->Owner!=null),true);
                }

                function test_NamePath()
                {
                        $own=new Component();
                        $own->Name="owner";
                        $obj=new Component($own);
                        $obj->Name="owned";
                        $this->assertEquals($obj->NamePath,"owner.owned");
                }

                function test_ComponentCount()
                {
                        $own=new Component();
                        $own->Name="owner";
                        $obj=new Component($own);
                        $obj->Name="owned";
                        $this->assertEquals($own->ComponentCount,1);
                }

                function test_Components()
                {
                        $own=new Component();
                        $own->Name="owner";
                        $obj=new Component($own);
                        $obj->Name="owned";
                        $this->assertEquals($own->Components->count(),1);
                }

                function test_removeComponent()
                {
                        $own=new Component();
                        $own->Name="owner";
                        $obj=new Component($own);
                        $obj->Name="owned";
                        $own->removeComponent($obj);
                        $this->assertEquals($own->Components->count(),0);
                }

                function test_insertComponent()
                {
                        $own=new Component();
                        $own->Name="owner";
                        $obj=new Component($own);
                        $obj->Name="owned";

                        $obj2=new Component();
                        $obj2->Name="owned2";
                        $own->insertComponent($obj2);
                        $this->assertEquals($own->Components->count(),2);
                }

                function test_callEvent()
                {
                        //TODO: Check here how to test events
                }

                function test_init()
                {
                        //Nothing to test
                }

                function test_loaded()
                {
                        //Nothing to test
                }

                function test_loadedChildren()
                {
                        //Nothing to test
                }


                function test_serializeChildren()
                {
                        $own=new Component();
                        $own->Name="Form";
                        $object=new Component($own);
                        $object->Name="Component";
                        $object->serialize();
                        //default value
                        $this->assertEquals($_SESSION['insession.Form.Component'],1,"a");
                        $object->Tag="DummyTag";
                        $object->serialize();
                        $this->assertEquals($_SESSION['Form.Component.Tag'],"DummyTag","b");
                        $object->Tag=10;
                        $own->serializeChildren();
                        $object->Tag=0;
                        $object->unserializeChildren();
                        $this->assertEquals($_SESSION['Form.Component.Tag'],10,"c");

                }

                function test_unserializeChildren()
                {
                        $this->test_serializeChildren();
                }

                function test_generateAjaxEvent()
                {
                        $own=new Component();
                        $own->Name="pepe";
                        $this->object->owner=$own;
                        $this->assertEquals(trim($this->object->generateAjaxEvent("onclick", "button1_click")),'onclick="xajax_ajaxProcess(\'pepe\',\'myobject\',null,\'button1_click\',xajax.getFormValues(\'pepe\'))"');

                }

                function test_dumpHeaderCode() {}
                function test_dumpChildrenJavascript() {}
                function test_dumpChildrenHeaderCode() {}

                function test_readFromResource()
                {
                        $c=new Component();
                        $this->assertEquals($c->Tag,0);
                        $c->readFromResource("test.xml.php");
                        $this->assertEquals($c->Tag,'10');
                }

                function test_loadResource()
                {
                        $c=new Component();
                        $this->assertEquals($c->Tag,0);
                        $c->loadResource("test.php");
                        $this->assertEquals($c->Tag,'10');
                }

                function test_updateDataField()
                {
                        //only outputs FAIL when we have a _datafield to check
                        if(isset($this->object->_datafield))
                                $this->assertEquals(true,false);
                }

                function test_fixupProperty()
                {
                        $my_edit=new Edit();
                        $obj=new Object();
                        $my_edit->DataSource=$obj;
                        $my_edit->DataSource=$this->object->fixupProperty($obj);
                        $this->assertEquals($my_edit->DataSource,$obj);
                }

                function test_ajaxCall()
                {
                        $owner=new QWidget();
                        $owner->Name="MyOwner";
                        $this->object->owner=$owner;
                        $actual=$this->object->ajaxCall("myevent"/*,array(0=>"param1",1=>"param2")*/);
                        $actual=trim($actual);
                        $expected="xajax_ajaxProcess('MyOwner','myobject',params,'myevent',xajax.getFormValues('MyOwner'),[]);";
                        $this->assertEquals($expected,$actual);
                }


                function test_hasValidDataField()
                {
                        //only outputs FAIL when we have a _datafield to check
                        if(isset($this->object->_datafield))
                                $this->assertEquals(true,false);
                }

                function test_readDataFieldValue()
                {
                        //only outputs FAIL when we have a _datafield to check
                        if(isset($this->object->_datafield))
                                $this->assertEquals(true,false);
                }

                function test_readAccessibility()
                {
                        use_unit("rpc/rpc.inc.php");
                        $this->assertEquals(Accessibility_Fail,$this->object->readAccessibility("test",Accessibility_Fail));
                }

                function test_dumpHiddenKeyFields()
                {
                        //only outputs FAIL when we have a _datafield to check
                        if(isset($this->object->_datafield) || isset($this->object->_dataSource))
                                $this->assertEquals(true,false);
                }

                function test_inSession()
                {
                        $own=new Component();
                        $own->Name="Form";
                        $own->insertComponent($this->object);
                        $this->object->serialize();
                        //default value
                        $this->assertEquals($_SESSION['insession.Form.Component'],1,"a");
                        $own->removeComponent($this->object);
                }

                function test_preinit()
                {
                        //Nothing to test
                }

                function test_dumpJavascript()
                {
                        //Nothing to test
                }

                function test_dumpJSEvent()
                {
                        ob_start();
                        $this->stowner->insertComponent($this->object);
                        $this->object->dumpJSEvent('ExtraEvent');
                        $c=ob_get_contents();
                        ob_end_clean();
                        $c=str_replace("\n","",$c);
                        $this->assertEquals(trim($c),'function ExtraEvent(event){var event = event || window.event;var params=null;}');
                        $this->stowner->removeComponent($this->object);
                }

        }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_component.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "ComponentTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>