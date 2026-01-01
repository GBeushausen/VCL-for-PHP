<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_graphiccontrol.inc.php";
        use_unit("controls.inc.php");
        use_unit("chart.inc.php");


        class SimpleChartTest extends GraphicControlTest
        {
                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;
                        $this->object=new SimpleChart();
                        $this->object->name="myobject";
                }

                function test_init()
                {
                        $this->aowner->insertComponent($this->object);

                        $_POST['myobjectSubmitEvent']='myobject_DummyEvent';
                        $this->aowner->executed=false;
                        $this->object->OnClick="DummyEvent";
                        $this->object->init();
                        $this->assertEquals($this->aowner->executed,true,'Init test 1');

                        $this->aowner->removeComponent($this->object);
                }

                function test_dumpHeaderCode()
                {
                        ob_start();
                        $this->object->dumpHeaderCode();
                        $c=ob_get_contents();
                        ob_clean();
                        //print_r('<hr>'.$c.'<hr>');
                        $expected="<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\" />";
                        $this->assertEquals($this->cleanString($c),$this->cleanString($expected));
                }

                function test_dumpContents()
                {
                        $this->aowner->insertComponent($this->object);
                        $GLOBALS['PHP_SELF']='http://www.mysite.com/mypage.php';
                        $c=$this->object->show(true);
                        $c=$this->cleanString($c);
                        $compare=$this->cleanString("<img src=\"http://www.mysite.com/mypage.php?bchart=9581653be59e8a6e741c3b4278b73ed0\" width=\"400\" height=\"250\" id=\"myobject\" name=\"myobject\" alt=\"Untitled chart\" />");
                        $this->assertEquals($c,$compare);
                        $this->aowner->removeComponent($this->object);
                }

                function test_dumpJavascript()
                {
                        $this->setup();
                        $this->object->OnClick="DummyEvent";
                        ob_start();
                        $this->object->dumpJavaScript();
                        $c=ob_get_contents();
                        ob_clean();
                        $expected='functionDummyEventWrapper(event,hiddenfield,submitvalue,wrappedfunc){varevent=event||window.event;submit1=true;submit2=true;if(typeof(wrappedfunc)==\'function\')submit1=wrappedfunc(event);hiddenfield.value=submitvalue;form=hiddenfield.form;if((form)&&(form.onsubmit)&&(typeof(form.onsubmit)==\'function\'))submit2=form.onsubmit();if((submit1)&&(submit2))form.submit();returnfalse;}';
                        $this->assertEquals($this->cleanString($c),$this->cleanString($expected));
                }

                function test_dumpGraphic()
                {
                        //This dumps binary data
                }

                function test_serialize()
                {
                        $this->setup();
                        $this->aowner->insertComponent($this->object);

                        $prefix = $this->object->readNamePath().".Chart.";

                        $Chart=$this->object->Chart;
                        $Chart->title="mytitle";
                        $Chart->logoFileName="mylogofilename";
                        $Chart->margin=10;
                        $Chart->lowerBound=40;
                        $Chart->labelMarginLeft=0;
                        $Chart->labelMarginRight=10;
                        $Chart->labelMarginTop=20;
                        $Chart->labelMarginBotton=50;

                        $this->object->serialize();

                        $this->assertEquals($_SESSION[$prefix."Title"],$Chart->title,"Serialize Test 1");
                        $this->assertEquals($_SESSION[$prefix."LogoFileName"],$Chart->logoFileName,"Serialize Test 2");
                        $this->assertEquals($_SESSION[$prefix."Margin"],$Chart->margin,"Serialize Test 3");
                        $this->assertEquals($_SESSION[$prefix."LowerBound"],$Chart->lowerBound,"Serialize Test 4");
                        $this->assertEquals($_SESSION[$prefix."LabelMarginLeft"],$Chart->labelMarginLeft,"Serialize Test 5");
                        $this->assertEquals($_SESSION[$prefix."LabelMarginRight"],$Chart->labelMarginRight,"Serialize Test 6");
                        $this->assertEquals($_SESSION[$prefix."LabelMarginTop"],$Chart->labelMarginTop,"Serialize Test 7");
                        $this->assertEquals($_SESSION[$prefix."LabelMarginBottom"],$Chart->labelMarginBottom,"Serialize Test 18");

                        $this->object->unserialize();

                        $this->assertEquals("mytitle",$Chart->title,"Serialize Test 9");
                        //$this->assertEquals("mylogofilename",$Chart->LogoFileName,"Serialize Test 10");
                        $this->assertEquals(10,$Chart->margin,"Serialize Test 11");
                        $this->assertEquals(40,$Chart->lowerBound,"Serialize Test 12");
                        $this->assertEquals(0,$Chart->labelMarginLeft,"Serialize Test 13");
                        $this->assertEquals(10,$Chart->labelMarginRight,"Serialize Test 14");
                        $this->assertEquals(20,$Chart->labelMarginTop,"Serialize Test 15");
                        $this->assertEquals(50,$Chart->labelMarginBottom,"Serialize Test 16");

                        $this->aowner->removeComponent($this->object);
                }

                function test_unserialize()
                {
                        $this->test_serialize();
                }

                function test_ControlStyle($styles=array())
                {
                        $styles["csImageContent"]=1;
                        $styles["csRenderOwner"]=1;
                        $styles["csRenderAlso"]="StyleSheet";
                        parent::test_ControlStyle($styles);
                }

               function test_OnClick()
                {
                        $this->assertEquals($this->object->OnClick,null);
                        $this->assertEquals($this->object->OnClick,$this->object->defaultOnClick());
                        $this->object->OnClick='onClickEvent';
                        $this->assertEquals($this->object->OnClick,'onClickEvent');
                }
               function test_ChartType()
                {
                        $this->assertEquals($this->object->ChartType,'ctVerticalChart');
                        $this->assertEquals($this->object->ChartType,$this->object->defaultChartType());
                        $this->object->ChartType='ctPieChart';
                        $this->assertEquals($this->object->ChartType,'ctPieChart');

                }
                function test_clearChart()
                {
                        $this->test_createChart();
                        $this->object->Chart->addPoint(new Point(10, 10));
                        $Chart=$this->object->readChart();
                        $Chart->title="this is my title";
                        $this->object->clearChart();
                        $this->assertEquals("Untitle chart","Untitle chart");
                        $this->assertEquals($this->object->Chart->point,array());
                }
               function test_createChart()
                {
                        $this->object->ChartType='ctVerticalChart';
                        $this->object->createChart();
                        $chart=$this->object->Chart;
                        $this->assertEquals(get_class($chart),'VerticalChart');

                        $this->object->ChartType='ctPieChart';
                        $this->object->createChart();
                        $chart=$this->object->Chart;
                        $this->assertEquals(get_class($chart),'PieChart');
                }
               function test_readChart()
                {
                        $this->assertEquals($this->object->ChartType,'ctVerticalChart');
                        $this->assertEquals($this->object->ChartType,$this->object->defaultChartType());
                        $this->object->ChartType=ctPieChart;
                        $this->assertEquals($this->object->ChartType,ctPieChart);
                }
               function test_fillDummyValues()
                {
                        $this->assertEquals($this->object->ChartType,'ctVerticalChart');
                        $this->assertEquals($this->object->ChartType,$this->object->defaultChartType());
                        $this->object->ChartType='onClickEvent';
                        $this->assertEquals($this->object->ChartType,'onClickEvent');
                }


        }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_simplechart.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "SimpleChartTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>
