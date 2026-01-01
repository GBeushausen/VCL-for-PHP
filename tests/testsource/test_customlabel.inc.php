<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_graphiccontrol.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");


        class CustomLabelTemp extends CustomLabel
        {
                function test_strip_selected_tags($text, $tags = array())
                {
                        return parent::strip_selected_tags($text,$tags);
                }
        }

        class CustomLabelTest extends GraphicControlTest
        {
                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;
                        $this->object=new CustomLabel();
                        $this->object->Name="myobject";
                }

                function test_init()
                {
                        $this->aowner->insertComponent($this->object);

                        $this->assertEquals($this->aowner->executed,false,'Init test 1');

                        $_POST['myobjectSubmitEvent']='myobject_DummyEvent';
                        $this->aowner->executed=false;
                        $this->object->OnClick="DummyEvent";
                        $this->object->init();
                        $this->assertEquals($this->aowner->executed,true,'Init test 3');

                        $_POST['myobjectSubmitEvent']='myobject_DummyEvent';
                        $this->aowner->executed=false;
                        $this->object->OnDblClick="DummyEvent";
                        $this->object->init();
                        $this->assertEquals($this->aowner->executed,true,'Init test 4');

                        $this->aowner->removeComponent($this->object);
                }

                function test_dumpJavascript()
                {
                        ob_start();
                        $this->object->OnClick="myonclick";
                        $this->object->OnDblclick="myondblclick";
                        $this->object->dumpJavascript();
                        $c=$this->cleanString(ob_get_contents());
                        ob_clean();
                        $real=$this->cleanString("functionmyonclickWrapper(event,hiddenfield,submitvalue,wrappedfunc){varevent=event||window.event;submit1=true;submit2=true;if(typeof(wrappedfunc)=='function')submit1=wrappedfunc(event);hiddenfield.value=submitvalue;form=hiddenfield.form;if((form)&&(form.onsubmit)&&(typeof(form.onsubmit)=='function'))submit2=form.onsubmit();if((submit1)&&(submit2))form.submit();returnfalse;}functionmyondblclickWrapper(event,hiddenfield,submitvalue,wrappedfunc){varevent=event||window.event;submit1=true;submit2=true;if(typeof(wrappedfunc)=='function')submit1=wrappedfunc(event);hiddenfield.value=submitvalue;form=hiddenfield.form;if((form)&&(form.onsubmit)&&(typeof(form.onsubmit)=='function'))submit2=form.onsubmit();if((submit1)&&(submit2))form.submit();returnfalse;}");

                        $this->assertEquals($c,$real);
                }

                function test_Link()
                {
                        $this->assertEquals($this->object->Link,'');
                        $this->assertEquals($this->object->Link,$this->object->defaultLink());
                        $this->object->Link="http://www.google.es";
                        $this->assertEquals($this->object->Link,"http://www.google.es");
                }

                function test_DataSource()
                {
                        $obj=new DataSource();
                        $this->assertEquals($this->object->DataSource,null);
                        $this->assertEquals($this->object->DataSource,$this->object->defaultDataSource());
                        $this->object->DataSource=$obj;
                        $this->assertEquals($this->object->DataSource,$obj);
                }

                function test_DataField()
                {
                        $this->assertEquals($this->object->DataField,'');
                        $this->assertEquals($this->object->DataField,$this->object->defaultDataField());
                        $this->object->DataField="field name";
                        $this->assertEquals($this->object->DataField,"field name");
                }


                function test_dumpContents()
                {
                        $this->object->Name="Label1";
                        $c=$this->object->show(true);
                        $compare=trim('<div id="Label1" style=" font-family: Verdana; font-size: 10px;  height:13px;width:75px;"   ></div>');
                        $this->assertEquals(trim($c),$compare);
                }

                function test_OnClick()
                {
                        $this->object->init();
                }

                function test_OnDblClick()
                {
                        $this->object->init();
                }

                function test_LinkTarget()
                {
                        $this->assertEquals($this->object->LinkTarget,"");
                        $this->assertEquals($this->object->defaultLinkTarget(),$this->object->LinkTarget);
                        $this->object->LinkTarget= 'Link Target, Link Target, Link Target';
                        $this->assertEquals($this->object->LinkTarget,'Link Target, Link Target, Link Target');
                }

                 function test_WordWrap()
                {
                        $this->assertEquals($this->object->WordWrap,1);
                        $this->assertEquals($this->object->defaultWordWrap(),$this->object->WordWrap);
                        $this->object->WordWrap=0;
                        $this->assertEquals($this->object->WordWrap,0);
                }

                function test_ControlStyle($styles=array())
                {
                        $styles["csRenderOwner"]=1;
                        $styles["csRenderAlso"]="StyleSheet";
                        parent::test_ControlStyle($styles);
                }

                function test_strip_selected_tags()
                {
                        $object= new CustomLabelTemp();

                        $text="<mytag> first chunk </mytag>
                               <mysecondtag> second chunk </mysecondtag>";

                        $tags=array("mytag","mysecondtag");
                        $finaltext=$object->test_strip_selected_tags($text,$tags);
                        $finaltext=$this->cleanString($finaltext);
                        $mytext=$this->cleanString("firstchunk second chunk");
                        $this->assertEquals($finaltext,$mytext);
                }
        }

        if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_customlabel.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "CustomLabelTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>
