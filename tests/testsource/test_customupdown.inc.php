<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_qwidget.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("comctrls.inc.php");

        class CustomUpDownTemp extends CustomUpDown
        {
                public function TestPosition() { $this->CheckPosition(); }
        }

        class CustomUpDownTest extends QWidgetTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new CustomUpDown();
                        $this->object->Name="myobject";
                }


                function test_init()
                {
                        $_POST['myobject_state']=20;
                        $this->object->init();
                        $this->assertEquals("20",$this->object->Position);
                }

                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        //print_r($c);
                        $c=$this->cleanString($this->fixString($c));
                        $compare=trim('<input type="hidden" id="myobject_state" name="myobject_state" value="0" />
<script type="text/javascript">
  var myobject = new qx.ui.form.Spinner(0,0,100);
  myobject.setLeft(0);
  myobject.setTop(0);
  myobject.setWidth(120);
  myobject.setHeight(21);
  myobject.setIncrementAmount(1);
  myobject.setBorder(new qx.renderer.border.Border(1, \'solid\'));
  myobject.addEventListener(\'change\', function(e) { hid=findObj("myobject_state"); hid.value=myobject.getValue(); });
  myobject.setEnabled(true);
  myobject.setVisibility(true);
</script>');
                        $compare=$this->cleanString($compare);
                        $this->assertEquals(trim($c),$compare);
                }


                function test_ajaxCall()
                {
                        $owner=new QWidget();
                        $owner->Name="MyOwner";
                        $this->object->owner=$owner;
                        $actual=$this->object->ajaxCall("myevent"/*,array(0=>"param1",1=>"param2")*/);
                        $actual=trim($actual);
                        //print_r($actual);
                        $expected="xajax_ajaxProcess('MyOwner','myobject',params,'myevent',xajax.getFormValues('MyOwner'),[]);";
                        $this->assertEquals($expected,$actual);
                }

                function test_addOtherChildren()
                {
                        if ($this->object->className()!="CustomUpDown")
                                $this->assertEquals(true,false);
                }

                function test_getBorderStyle(){/*checked in test_BorderStyle*/}
                function test_setBorderStyle(){/*checked in test_BorderStyle*/}
                function test_getDataField(){/*checked in test_DataField*/}
                function test_setDataField(){/*checked in test_DataField*/}
                function test_getDataSource(){/*checked in test_DataSource*/}
                function test_setDataSource(){/*checked in test_DataSource*/}
                function test_readIncrement(){/*checked in test_Increment*/}
                function test_writeIncrement(){/*checked in test_Increment*/}
                function test_getIncrement(){/*checked in test_Increment*/}
                function test_setIncrement(){/*checked in test_Increment*/}
                function test_getMin(){/*checked in test_Min*/}
                function test_setMin(){/*checked in test_Min*/}
                function test_getMax(){/*checked in test_Max*/}
                function test_setMax(){/*checked in test_Max*/}
                function test_getPosition(){/*checked in test_Position*/}
                function test_setPosition(){/*checked in test_Postion*/}

                function test_BorderStyle()
                {
                        $this->assertEquals($this->object->BorderStyle,bsSingle);
                        $this->assertEquals($this->object->BorderStyle,(string)$this->object->defaultBorderStyle(),"a");
                        $this->object->BorderStyle="bsNone";
                        $this->assertEquals($this->object->BorderStyle,"bsNone","b");
                }
                function test_DataField()
                {
                        $this->assertEquals($this->object->DataField,"");
                        $this->assertEquals($this->object->DataField,$this->object->defaultDataField());
                        $this->object->DataField="myDataField";
                        $this->assertEquals($this->object->DataField,"myDataField");
                }
                function test_DataSource()
                {
                        $myObj=new Object();
                        $this->assertEquals($this->object->DataSource,null);
                        $this->assertEquals($this->object->DataSource,$this->object->defaultDataSource());
                        $this->object->DataSource=$myObj;
                        $this->assertEquals($this->object->DataSource, $myObj);
                }
                function test_Min()
                {
                        $this->assertEquals($this->object->Min, 0);
                        $this->assertEquals($this->object->defaultMin(),$this->object->Min);
                        $this->object->Min= 20;
                        $this->assertEquals($this->object->Min, 20);
                }
                function test_Max()
                {
                        $this->assertEquals($this->object->defaultMax(),$this->object->Max);
                        $this->object->Max= 10000;
                        $this->assertEquals($this->object->Max, 10000);
                }
                function test_Position()
                {
                        $this->assertEquals($this->object->Position,$this->object->defaultPosition());
                        $this->object->Position=10;
                        $this->assertEquals($this->object->Position,10);
                }
                function test_Increment()
                {
                        $this->assertEquals($this->object->Increment, 1);
                        $this->assertEquals($this->object->defaultIncrement(),$this->object->Increment);
                        $this->object->Increment= 20;
                        $this->assertEquals($this->object->Increment, 20);
                }
                function test_CheckPosition()
                {
                        $Object=new CustomUpDownTemp();
                        $Object->Min=15;
                        $Object->Max=10;
                        $Object->TestPosition();
                        $this->assertEquals($Object->Max,$Object->Min,"CheckPosition test 1");
                        $Object->Position=30;
                        $Object->TestPosition();
                        $this->assertEquals($Object->Position,$Object->Max,"CheckPosition test 2");
                        $Object->Position=1;
                        $Object->TestPosition();
                        $this->assertEquals($Object->Min,$Object->Position,"CheckPosition test 3");
                }


        }

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_customupdown.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "CustomUpDownTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
