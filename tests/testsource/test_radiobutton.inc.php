<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_buttoncontrol.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");

        class RadioButtonTest extends ButtonControlTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new RadioButton();
                        $this->object->name="myobject";
                }

                function test_preinit()
                {
                        $_POST['myobject']=1;
                        $this->object->Caption="1";
                        $this->object->preinit();
                        $this->assertEquals($this->object->Checked, 1,"Check Test 1");
                        unset($_POST['myobject']);
                        $this->object->preinit();
                        $this->assertEquals($this->object->Checked, 0,"Check Test 2");
                }

                function test_dumpJavascript()
                {
                        $this->setup();
                        ob_start();
                        $this->object->jsOnSelect="DummyEvent";
                        $this->object->OnClick="AnotherDummyEvent";
                        $this->object->dumpJavascript();

                        $c=ob_get_contents();
                        ob_clean();
                        $expected="functionAnotherDummyEventWrapper(event,hiddenfield,submitvalue,wrappedfunc){varevent=event||window.event;submit1=true;submit2=true;if(typeof(wrappedfunc)=='function')submit1=wrappedfunc(event);hiddenfield.value=submitvalue;form=hiddenfield.form;if((form)&&(form.onsubmit)&&(typeof(form.onsubmit)=='function'))submit2=form.onsubmit();if((submit1)&&(submit2))form.submit();returnfalse;}functionRadioButtonClick(elem,caption){if(typeof(elem.length)=='undefined'){elem.checked=true;return(typeof(elem.onclick)=='function')?elem.onclick():false;}else{for(vari=0;i<elem.length;i++){if(elem[i].value==caption){elem[i].checked=true;return(typeof(elem[i].onclick)=='function')?elem[i].onclick():false;}}}returnfalse;}";
                        $this->assertEquals($this->cleanString($c),$this->cleanString($expected));

                }

                function test_Group()
                {
                        $this->assertEquals($this->object->Group, '');
                        $this->assertEquals($this->object->defaultGroup(),$this->object->Group);
                        $this->object->Group=1;
                        $this->assertEquals($this->object->Group,1);
                }
                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        //print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim('<table cellpadding="0" cellspacing="0" id="myobject_table" style=" font-family: Verdana; font-size: 10px;  height:20px;width:121px;" ><tr><td width="20">
<input type="radio" id="myobject" name="myobject" value=""  style=" font-family: Verdana; font-size: 10px;  "   tabindex="0"    />
</td><td >
<span></span>
</td></tr></table>');
                        $this->assertEquals(trim($c),$compare);
                }



                function test_dumpContentsButtonControl()
                {
                        $c=$this->object->show(true);
                        //print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim('<table cellpadding="0" cellspacing="0" id="myobject_table" style=" font-family: Verdana; font-size: 10px;  height:20px;width:121px;" ><tr><td width="20">
<input type="radio" id="myobject" name="myobject" value=""  style=" font-family: Verdana; font-size: 10px;  "   tabindex="0"    />
</td><td >
<span></span>
</td></tr></table>');
                        $this->assertEquals(trim($c),$compare);
                }

                function test_getOnSubmit(){}
                function test_setOnSubmit(){}
                function test_getParentFont(){}
                function test_setParentFont(){}
                function test_getPopupMenu(){}
                function test_setPopupMenu(){}

         }
      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_radiobutton.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "RadioButtonTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
