<?php

        require_once "phpunit/phpunit.php";
        require_once "../../system.inc.php";

        class DerivedObject extends Object
        {
                private $_property;

                function method()
                {
                }

                function setProperty($value)
                {
                        $this->_property=$value;
                }

                function getProperty()
                {
                        return($this->_property);
                }
        }

        class ObjectTest extends TestCase
        {
                public $object;

                function setup()
                {
                        $this->object=new DerivedObject();
                }

                function test_method()
                {
                  //stub
                }

                function test_Property()
                {
                  //stub
                }

                function test___construct()
                {
                }

                function test_className()
                {
                        $object=new DerivedObject();
                        $this->assertEquals($object->ClassName(),'DerivedObject');
                }

                function test_classNameIs()
                {
                        $object=new DerivedObject();
                        $this->assertEquals($object->ClassNameIs('DerivedObject'),true);
                }

                function test_methodExists()
                {
                        $this->assertEquals($this->object->methodExists('methodExists'),true);
                }

                function test_classParent()
                {
                        $object=new DerivedObject();
                        $this->assertEquals($object->classParent(),'Object');
                }

                function test_inheritsFrom()
                {
                        $this->assertEquals($this->object->inheritsFrom('Object'),true);
                }

                function test_readProperty()
                {
                        if (($this->object->className()!='Persistent') && ($this->object->className()!='Object') && ($this->object->className()!='DerivedObject'))
                        {
                                $_POST['value']='test_value';
                                $this->object->readProperty('Tag','value');

                                $this->assertEquals($this->object->Tag,'test_value');
                        }
                }

                function test___get()
                {
                        $object=new DerivedObject();

                        $object->Property="hola";
                        $this->assertEquals($object->Property,'hola');
                }

                function test___set()
                {
                        $object=new DerivedObject();

                        $object->Property="hola";
                        $this->assertEquals($object->Property,'hola');
                }

                function defaultTest($propname, $defvalue,$newvalue)
                {
                        $defmethod='default'.$propname;
                        $this->assertEquals($this->object->$propname,$defvalue,"Default check");
                        $this->assertEquals($this->object->$defmethod(),$this->object->$propname,"Default check 2");
                        $this->object->$propname= $newvalue;
                        $this->assertEquals($this->object->$propname,$newvalue,"Assignment");
                }

                function test_dump_properties_and_methods_not_tested()
                {
                        /*echo "<hr>";
                        echo $this->object->className();
                        echo "<hr>";*/
                        $methods=get_class_methods($this->object->className());
                        $testmethods=get_class_methods(get_class($this));
                        /*echo "<pre>";
                        print_r($methods);
                        echo "<hr>";
                        print_r($testmethods);
                        echo "<hr>";
                        echo "</pre>";*/
                        reset($methods);
                        $nottested=0;
                        ob_start();
                        while (list($k,$v)=each($methods))
                        {
                                $mname=$v;
                                $fst=substr($v,0,3);
                                $fr=substr($v,0,4);
                                $fw=substr($v,0,5);
                                $fd=substr($v,0,7);

                                if ($fd=='default') continue;
                                if (($fst=='set') || ($fst=='get')) $mname=substr($v,3);
                                if ($fr=='read') $mname=substr($v,4);
                                if ($fw=='write') $mname=substr($v,5);

                                $keys=array_keys($testmethods,"test_".$mname);
                                if (count($keys)==0)
                                {
                                        $keys=array_keys($testmethods,"test_".$v);
                                        if (count($keys)==0)
                                        {
                                                $nottested++;
                                                echo $this->object->className()."::".$v."\n";
                                        }

                                }
                        }
                        $c=ob_get_contents();
                        ob_end_clean();
                        if ($c!='')
                        {
                        echo "<pre>";
                        echo "Properties and Methods not tested\n";
                        echo "---------------------------------\n";
                        echo $c;
                        echo "</pre>";
                        }
                        $this->assertEquals($nottested,0);
                }

                //Util usded to format text copied from html. It just converts \r\n to \n
                function fixString($text) { return str_replace("\n","\r\n",trim($text)); }

                //Util used to get text without rubish
                function cleanString($text)
                {
                        $chars=array("\n","\r","\t"," ");
                        $c=str_replace($chars,"",$text);
                        return $c;
                }
        }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_object.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
        $suite = new TestSuite( "ObjectTest" );
        $testRunner = new TestRunner();
        $testRunner->run( $suite );
        }


?>