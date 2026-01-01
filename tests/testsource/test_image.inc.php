<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "../../dbctrls.inc.php";
        require_once "test_graphiccontrol.inc.php";
        use_unit("extctrls.inc.php");
        use_unit("menus.inc.php");

        class ImageTest extends FocusControlTest
        {
                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;
                        $this->object=new Image();
                        $this->object->name="myobject";
                }

                function test_Stretch()
                {
                        $this->object->Autosize=false;
                        $this->object->ImageSource="my_car_modena.jpg";
                        $this->object->Stretch=true;
                        $this->object->Width=200;
                        $this->object->Height=300;
                        $c=$this->object->show(true);
                        $c=$this->cleanString($c);
                        $compare=$this->cleanString('<divid="myobject_container"style="width:200;height:300;"><imgid="myobject"src="my_car_modena.jpg"width="200"height="300"border="0"/></div>');
                        $this->assertEquals(trim($c),$compare);
                }

                function test_loaded()
                {
                        $this->object->AutoSize=1;
                        $this->object->ImageSource="my_car_modena.jpg";
                        $this->assertEquals($this->object->Width,105);
                        $this->assertEquals($this->object->Height,105);

                        $this->setup();
                }

                function test_init()
                {
                        $this->aowner->insertComponent($this->object);

                        $this->assertEquals($this->aowner->executed,false,'Init test 1');

                        $_POST['myobjectSubmitEvent']='myobject_DummyEvent';
                        $this->aowner->executed=false;
                        $this->object->OnClick="DummyEvent";
                        $this->object->init();
                        $this->assertEquals($this->aowner->executed,true,'Init test 2');

                        /*$_POST['bimg']='d2ff1a30a837a2b7b5c5ae5497d45e75';
                        $this->object->Binary=true;
                        ob_start();
                        $this->object->init();
                        $c=ob_get_contents();
                        ob_clean();
                        $c=$this->cleanString($c);
                        $compare="";
                        $this->assertEquals($c,$compare);*/

                        $this->aowner->removeComponent($this->object);

                }

                function test_dumpGraphic()
                {
                        //No testing, as it dumps binary data
                }

                function test_dumpJavascript()
                {
                        ob_start();
                        $this->object->OnClick="DummyEvent";
                        $this->object->dumpJavascript();
                        $c=ob_get_contents();
                        ob_clean();
                        $c=$this->cleanString($c);
                        $compare="functionDummyEventWrapper(event,hiddenfield,submitvalue,wrappedfunc){varevent=event||window.event;submit1=true;submit2=true;if(typeof(wrappedfunc)=='function')submit1=wrappedfunc(event);hiddenfield.value=submitvalue;form=hiddenfield.form;if((form)&&(form.onsubmit)&&(typeof(form.onsubmit)=='function'))submit2=form.onsubmit();if((submit1)&&(submit2))form.submit();returnfalse;}";
                        $this->assertEquals($c,$compare);
                }

                function test_OnClick()
                {
                        $this->assertEquals($this->object->OnClick, null);
                        $this->assertEquals($this->object->defaultOnClick(),$this->object->OnClick);
                        $this->object->OnClick= 'onclickEvent';
                        $this->assertEquals($this->object->OnClick, 'onclickEvent');
                }

                function test_OnCustomize()
                {
                        $this->assertEquals($this->object->OnCustomize, null);
                        $this->assertEquals($this->object->defaultOnCustomize(),$this->object->OnCustomize);
                        $this->object->OnCustomize= 'onCustomize';
                        $this->assertEquals($this->object->OnCustomize, 'onCustomize');
                }

                function test_Autosize()
                {
                        $this->assertEquals($this->object->Autosize, 0);
                        $this->object->Autosize= 1;
                        $this->assertEquals($this->object->Autosize, 1);
                }

                function test_Border()
                {
                        $this->assertEquals($this->object->Border, 0);
                        $this->object->Border= 1;
                        $this->assertEquals($this->object->Border, 1);
                }

                function test_BorderColor()
                {
                        $this->assertEquals($this->object->BorderColor, "");
                        $this->object->BorderColor= '#FF8000';
                        $this->assertEquals($this->object->BorderColor, '#FF8000');
                }

                 function test_Center()
                {
                        $this->assertEquals($this->object->Center, 0);
                        $this->object->Center= 1;
                        $this->assertEquals($this->object->Center, 1);
                }

                function test_DataField()
                {
                        $this->assertEquals($this->object->DataField, "");
                        $this->assertEquals($this->object->defaultDataField(),$this->object->DataField);
                        $this->object->OnCustomize= 'data field';
                        $this->assertEquals($this->object->OnCustomize, 'data field');
                }

                function test_DataSource()
                {
                        $myDataSrc=new DataSource();
                        $this->assertEquals(null,$this->object->DataSource,"b");
                        $this->object->DataSource=$myDataSrc;
                        $this->assertEquals($this->object->DataSource, $myDataSrc,"c");
                }

                function test_ImageSource()
                {
                        $this->assertEquals($this->object->ImageSource, null);
                        $this->object->ImageSource= 'C:/mydir/myimage.gif';
                        $this->assertEquals($this->object->ImageSource, 'C:/mydir/myimage.gif');
                }


                function test_Link()
                {
                        $this->assertEquals($this->object->Link, null);
                        $this->object->Link= 'http://www.google.es';
                        $this->assertEquals($this->object->Link, 'http://www.google.es');
                }

                 function test_LinkTarget()
                {
                        $this->assertEquals($this->object->LinkTarget, null);
                        $this->object->LinkTarget= 'link target1';
                        $this->assertEquals($this->object->LinkTarget, 'link target1');
                }

                 function test_Proportional()
                {
                        $this->assertEquals($this->object->Proportional, 0);
                        $this->assertEquals($this->object->defaultProportional(),$this->object->Proportional);
                        $this->object->Proportional= 1;
                        $this->assertEquals($this->object->Proportional, 1);
                }

                function test_ControlStyle($styles=array())
                {
                        $styles["csAcceptsControls"]=1;
                        $styles["csRenderOwner"]=1;
                        $styles["csRenderAlso"]="StyleSheet";
                        parent::test_ControlStyle($styles);
                }
                function test_Binary()
                {
                        $this->assertEquals($this->object->Binary,0);
                        $this->assertEquals($this->object->Binary,$this->object->defaultBinary());
                        $this->object->Binary=1;
                        $this->assertEquals($this->object->Binary,1);
                }
                function test_BinaryType()
                {
                        $this->assertEquals($this->object->BinaryType,"image/jpeg");
                        $this->assertEquals($this->object->BinaryType,$this->object->defaultBinaryType());
                        $this->object->BinaryType="myevent";
                        $this->assertEquals($this->object->BinaryType,"myevent");
                }

                function test_dumpContents()
                {
                        $this->setup();
                        $c=$this->object->show(true);
                        $c=$this->cleanString($c);
                        $compare=$this->cleanString(trim('<div id="myobject_container" style=" width: 105;  height: 105; overflow: hidden;" ><img id="myobject" src=""  width="105"  height="105"  border="0"       /></div>'));
                        $this->assertEquals(trim($c),$compare);
                }

                function test_PopupMenu()
                {
                  $p=new PopupMenu();
                  $this->defaultTest('PopupMenu',null,$p);
                }


        }
      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_image.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "ImageTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
