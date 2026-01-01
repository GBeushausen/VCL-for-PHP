<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_control.inc.php";
        require_once "../../actnlist.inc.php";
        use_unit("controls.inc.php");
        use_unit("stdctrls.inc.php");
        use_unit("classes.inc.php");

        class FocusControlTest extends ControlTest
        {
                function setup()
                {
                        parent::setup();                
                        $this->object=new FocusControl();
                        $this->object->Name='myobject';
                }

                function test_controls()
                {
                        $this->assertEquals($this->object->controls->count(),0);
                        $this->assertEquals($this->object->ControlCount,0);

                        $c=new Control;
                        $c->Parent=$this->object;
                        $this->assertEquals($this->object->controls->count(),1);
                        $this->assertEquals($this->object->ControlCount,1);

                        $c->Parent=null;
                        $this->assertEquals($this->object->controls->count(),0);
                        $this->assertEquals($this->object->ControlCount,0);

                        $c->Parent=$this->object;
                        $this->assertEquals($this->object->controls->count(),1);
                        $this->assertEquals($this->object->ControlCount,1);

                        $this->object->controls->remove($c);
                        $this->assertEquals($this->object->controls->count(),0);
                        $this->assertEquals($this->object->ControlCount,0);
                }

                function test_ControlCount()
                {
                        $this->test_controls();
                }

                function test_dumpChildren()
                {
                        if ((isset($this->_controlstyle["csAcceptsControls"])) && ($this->_controlstyle["csAcceptsControls"] == "1" ))
                        {
                                $this->assertEquals(true,false,'TODO: Test here the rendering of the child controls');
/*                              ob_start();
                                $c=new Label();
                                $c->Parent=$this->object;
                                $c->Name="Label1";
                                $c->Caption="test!!";
                                $c=new Button();
                                $c->Parent=$this->object;
                                $cName="Button1";
                                $c->Caption="Buttontest";
                                $this->object->dumpChildren();
                                $c=ob_get_contents();
                                ob_end_clean();
                                print_r($c);
                                $this->assertEquals(trim($c),'<div id="Label1" style=" font-family: Verdana; font-size: 10px;  height:13px;width:75px;"   >test!!</div>');*/
                        }
                }

               function test_Layout()
                {
                        $input1=new Layout();

                        $newLayout1=$input1->Type;
                        $this->assertEquals($this->object->Layout->Type ,$newLayout1,"a");
                        $this->object->Layout->setType('FLOW_LAYOUT');
                        $newLayout=$this->object->Layout->Type;
                        $this->assertEquals($this->object->Layout->Type ,$newLayout,"b");
                        $this->object->Layout->setType('XY_LAYOUT');
                        $newLayout=$this->object->Layout->Type;
                        $this->assertEquals($this->object->Layout->Type ,$newLayout,"c");
                }

                   function test_updateChildrenFonts()
                   {
                        $parentPanel=new Panel();
                        $obj=new Edit();

                        $this->assertEquals($obj->ParentFont , 1);
                        $parentPanel->Font->Size=15;
                        $parentPanel->Font->Color='#FF0123';
                        $parentPanel->Font->Family='Arial';

                        $str1='font-family: Verdana; font-size: 10px; ';
                        $str2='font-family: Arial; font-size: 15; color: #FF0123; ';
                        $font_string=$obj->Font->FontString;
                        $font_string2=$parentPanel->Font->FontString;

                        $this->assertEquals(trim($font_string) , trim($str1) );
                        $this->assertEquals(trim($font_string2) , trim($str2));

                        $obj->Parent=$parentPanel;
                        $parentPanel->updateChildrenFonts();

                        $font_string=$obj->Font->FontString;
                        $font_string2=$parentPanel->Font->FontString;
                        $this->assertEquals(trim($font_string) , trim($str2));
                        $this->assertEquals(trim($font_string2) , trim($str2));

                         $this->assertEquals($obj->Font->Size , 15);
                         $this->assertEquals($obj->Font->Color , '#FF0123');
                         $this->assertEquals($obj->Font->Family , 'Arial');
                   }

                   function test_updateChildrenColors()
                   {
                        parent::setup();
                        $parentPanel=new Panel();
                        $obj=new Edit();

                        $this->assertEquals($obj->ParentColor , 1);
                        $parentPanel->Color='#FF0123';

                        $obj->Parent=$parentPanel;
                        $parentPanel->updateChildrenColors();

                        $color_string=$obj->Color;
                        $color_string2=$parentPanel->Color;
                        $this->assertEquals(trim($color_string) , trim($color_string2));

                        $this->assertEquals($obj->Color , '#FF0123');
                   }

                   function test_updateChildrenShowHints()
                   {
                        parent::setup();
                        $parentPanel=new Panel();
                        $obj=new Edit();

                        $this->assertEquals($obj->ParentShowHint , 1,"ChildrenShowHints Check 1");
                        $parentPanel->ShowHint=1;
                        $obj->Parent=$parentPanel;
                        $parentPanel->updateChildrenShowHints();

                        $hints_string=$obj->ShowHint;
                        $hints_string2=$parentPanel->ShowHint;
                        $this->assertEquals(trim($hints_string) , trim($hints_string2),"ChildrenShowHints Check 2");

                        $this->assertEquals($obj->ShowHint ,1,"ChildrenShowHints Check 3");
                   }
                   function test_updateChildrenParentProperties()
                   {
                        $this->test_updateChildrenFonts();
                        $this->test_updateChildrenColors();
                        $this->test_updateChildrenShowHints();

                   }

                function test_dumpContents()
                {
                        if($this->object->className()!="FocusControl")
                                $this->assertEquals(false,true);
                }

        }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_focuscontrol.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "FocusControlTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>
