<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_object.inc.php";
        use_unit("classes.inc.php");
        use_unit("graphics.inc.php");


        class PersistentTest extends ObjectTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new Persistent();
                }

                function test_serialize()
                {
                        $own=new Component();
                        $own->Name="Form";
                        $object=new Component($own);
                        $object->Name="Component";
                        $object->serialize();

                        //We can´t test this here, because value is the same that default value
                        //so we won´t keep "Tag" property in the session. Only changing values
                        //are kept.
                        //$this->assertEquals($_SESSION['Form.Component.Tag'],0);

                        $this->assertEquals($object->inSession("notused"),true);

                        $object->Tag=10;
                        $object->serialize();
                        $object->Tag=0;
                        $object->unserialize();
                        $this->assertEquals($_SESSION['Form.Component.Tag'],10);
                }

                function test_unserialize()
                {
                        $this->test_serialize();
                }

                function test_assign()
                {
                        $f1=new Font();

                        $f2=new Font();

                        $font_string=$f1->FontString;
                        $this->assertEquals(trim($font_string),trim('font-family: Verdana; font-size: 10px;'));

                        $font_string=$f2->FontString;
                        $this->assertEquals(trim($font_string),trim('font-family: Verdana; font-size: 10px;'));

                        $f1->Family="Tahoma";

                        $font_string=$f1->FontString;
                        $this->assertEquals(trim($font_string),trim('font-family: Tahoma; font-size: 10px;'));

                        $font_string=$f2->FontString;
                        $this->assertEquals(trim($font_string),trim('font-family: Verdana; font-size: 10px;'));

                        $f2->assign($f1);

                        $font_string=$f1->FontString;
                        $this->assertEquals(trim($font_string),trim('font-family: Tahoma; font-size: 10px;'));

                        $font_string=$f2->FontString;
                        $this->assertEquals(trim($font_string),trim('font-family: Tahoma; font-size: 10px;'));


                }

                function test_assignTo()
                {
                        $f1=new Font();

                        $f2=new Font();

                        $font_string=$f1->FontString;
                        $this->assertEquals(trim($font_string),trim('font-family: Verdana; font-size: 10px;'));

                        $font_string=$f2->FontString;
                        $this->assertEquals(trim($font_string),trim('font-family: Verdana; font-size: 10px;'));

                        $f1->Family="Tahoma";

                        $font_string=$f1->FontString;
                        $this->assertEquals(trim($font_string),trim('font-family: Tahoma; font-size: 10px;'));

                        $font_string=$f2->FontString;
                        $this->assertEquals(trim($font_string),trim('font-family: Verdana; font-size: 10px;'));

                        $f1->assignto($f2);

                        $font_string=$f1->FontString;
                        $this->assertEquals(trim($font_string),trim('font-family: Tahoma; font-size: 10px;'));

                        $font_string=$f2->FontString;
                        $this->assertEquals(trim($font_string),trim('font-family: Tahoma; font-size: 10px;'));


                }

                function test_assignError()
                {
                        try
                        {
                                $this->object->assignError(null);
                                $this->assertEquals(1,0);
                        }
                        catch (Exception $e)
                        {
                                $this->assertEquals(1,1);
                        }
                }

                function test_allowserialize()
                {
                        $this->assertEquals($this->object->allowserialize("notused"),true);
                }

                function test_readOwner()
                {
                        $this->assertEquals($this->object->readOwner(),null);
                }

                function test_readNamePath()
                {
                        $own=new Component();
                        $own->Name="Form";
                        $object=new Component($own);
                        $object->Name="Component";
                        $subobject=new Component($object);
                        $subobject->Name="SubComponent";
                        $this->assertEquals($subobject->readNamePath(),"Form.Component.SubComponent");
                }

                function test_inSession()
                {
                        $this->test_serialize();
                }
        }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_persistent.inc.php')
        {        
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "PersistentTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>
