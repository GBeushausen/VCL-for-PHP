<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_control.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");


        class MapShapeTest extends ControlTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new MapShape();
                        $this->object->name="myobject";
                }

                function test_OnClick()
                {
                  $this->defaultTest('OnClick',null,'test');
                }

                function test_OnDblClick()
                {
                  $this->defaultTest('OnClick',null,'test');
                }

                function test_Kind()
                {
                  $this->defaultTest('Kind',skRectangle,skCircle);
                }

                function test_Link()
                {
                  $this->defaultTest('Link','#','test.html');
                }

                function test_dumpContents()
                {
                        $this->object->Name="Mapshape1";
                        $c=$this->object->show(true);
                        //print_r($c);
                        //$c=$this->fixString($c);
                        $compare=$this->cleanString('<area id="Mapshape1" shape="rect" coords="0,0,20,20" title="" href="#" />');
                        $this->assertEquals($this->cleanString($c),$compare);
                }

                //Tested in test_Property
/*                function test_getOnClick(){}
                function test_setOnClick(){}
                function test_getOnDblClick(){}
                function test_setOnDblClick(){}
                function test_getKind(){}
                function test_setKind(){}
                function test_getLink(){}
                function test_setLink(){}   */

                /*function test_OnClick()
                {

                        $this->assertEquals($this->object->defaultOnClick(),$this->object->OnClick);
                        $this->object->OnClick= 'onclickEvent';
                        $this->assertEquals($this->object->OnClick, 'onclickEvent');

                }
                function test_OnDblClick()
                {
                        $this->assertEquals($this->object->defaultOnDblClick(),$this->object->OnDblClick);
                        $this->object->OnDblClick= 'OnDblClickEvent';
                        $this->assertEquals($this->object->OnDblClick, 'OnDblClickEvent');

                } */
/*                function test_Kind()
                {
                        $this->assertEquals($this->object->defaultKind(),$this->object->Kind);
                        $this->object->Kind= skRectangle;
                        $this->assertEquals($this->object->Kind, skRectangle);

                }*/

/*                function test_Link()
                {
                        //$this->assertEquals($this->object->defaultLink(),$this->object->Link);
                        $this->assertEquals("",$this->object->Link);
                        $this->object->Link= "http://www.google.es";
                        $this->assertEquals($this->object->Link, "http://www.google.es");
                }*/
        }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_mapshape.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "MapShapeTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>
