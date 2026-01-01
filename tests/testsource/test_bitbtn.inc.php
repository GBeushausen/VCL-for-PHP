<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "../../buttons.inc.php";
        require_once "../../actnlist.inc.php";
        require_once "test_qwidget.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("menus.inc.php");

        class BitBtnTest extends QWidgetTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new BitBtn();
                        $this->object->Name="myobject";
                }

                function test_init()
                {
                        //Can´t use this if we want to check onClick form onclick test
                        $this->stowner->insertComponent($this->object);
/*                        $_POST[$this->object->readJSWrapperHiddenFieldName()]=$this->object->readJSWrapperSubmitEventValue($this->object->OnClick);
                        $this->stowner->executed=false;
                        $this->object->init();
                        $this->assertEquals($this->stowner->executed,false,'Init test, no event assigned 1');*/

                        $this->object->OnClick="DummyEvent";
                        $_POST[$this->object->readJSWrapperHiddenFieldName()]=$this->object->readJSWrapperSubmitEventValue($this->object->OnClick);
                        $this->stowner->executed=false;
                        $this->object->init();
                        $this->assertEquals($this->stowner->executed,true,'Init test, event assigned 1');
                        $this->stowner->removeComponent($this->object);

                        /*
                        //Test if OnExecute is called when it should
                        $this->object->addAction('test_action');
                        $this->object->init();
                        $this->assertEquals($this->aowner->executed,true,'Init test, with Actions');
                        $this->aowner->removeComponent($this->object);
                        */
                }

                function test_dumpJavascript()
                {
                        ob_start();
                        $this->object->jsOnClick='dummyevent';
                        $this->object->dumpJavascript();
                        $c=ob_get_contents();
                        ob_end_clean();
//                        $c=$this->fixString($c);
                        $this->assertEquals(trim($c),"function updateButtonTheme() {
  var theme =  qx.manager.object.AppearanceManager.getInstance().getAppearanceTheme();
  var apar = theme.getAppearance('button');
  if (!apar) {
     return;
  }
  var oldState = apar.state;
  apar.state = function(vTheme, vStates) {
    var res = oldState ? oldState.apply(this, arguments):{};

    if (typeof(res) != 'undefined' && typeof(res.backgroundColor) != 'undefined')
      delete res.backgroundColor;

    return res;
  }
}");
                }

                function test_OnClick()
                {
                        $this->assertEquals($this->object->OnClick,null,"Onclick check 1");
                        $this->assertEquals($this->object->defaultOnClick(),$this->object->OnClick,"Onclick check 2");
                        $this->object->OnClick= 'DummyEvent';
                        $this->assertEquals($this->object->OnClick,'DummyEvent',"Onclick check 3");
                        $this->test_Init();
                }

                 function test_ImageSource()
                {
                        $this->assertEquals($this->object->ImageSource, "");
                        $this->assertEquals($this->object->defaultImageSource(),$this->object->ImageSource);
                        $this->object->ImageSource= '../relativePath/image1.jpg';
                        $this->assertEquals($this->object->ImageSource, '../relativePath/image1.jpg');
                        $this->object->ImageSource= 'C:\absolutPath\image1.jpg';
                        $this->assertEquals($this->object->ImageSource, 'C:\absolutPath\image1.jpg');
                }

                function test_ButtonLayout()
                {
                        $this->assertEquals($this->object->ButtonLayout, blImageLeft);
                        $this->assertEquals($this->object->defaultButtonLayout(),$this->object->ButtonLayout);
                        $this->object->ButtonLayout= blImageBottom;
                        $this->assertEquals($this->object->ButtonLayout, blImageBottom);
                        $this->object->ButtonLayout= blImageRight;
                        $this->assertEquals($this->object->ButtonLayout, blImageRight);
                        $this->object->ButtonLayout= blImageTop;
                        $this->assertEquals($this->object->ButtonLayout, blImageTop);
                }

                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        $c=$this->cleanString($c);
                        $c=$this->fixString($c);
                        $compare=trim('<script type="text/javascript">updateButtonTheme();</script>
<input type="hidden" id="myobject_state" name="myobject_state" value="" />
<script type="text/javascript">
        var myobject = new qx.ui.form.Button("");
        myobject.setLeft(0);
        myobject.setTop(0);
        myobject.setWidth(75);
        myobject.setHeight(25);
  myobject.setEnabled(true);
  myobject.setVisibility(true);
        var lblobj = myobject.getLabelObject();
        if (lblobj) lblobj.setFont("10px \'Verdana\' ");
        myobject.setBackgroundColor(new qx.renderer.color.ColorObject(\'buttonface\'));myobject.addEventListener("mouseup",function(){this.setIcon("");});myobject.addEventListener("execute",function(e){document.forms[0].submit();returnfalse;});</script>');
                        $compare=$this->cleanString($compare);
                        $this->assertEquals(trim($c),trim($compare),'Comparing default code');

                        //*******************
                        $this->object->Kind=bkOK;
                        $c=$this->object->show(true);
                        $c=$this->cleanString($c);
                        $c=$this->fixString($c);
                        $compare=trim('<scripttype="text/javascript">updateButtonTheme();</script><inputtype="hidden"id="myobject_state"name="myobject_state"value=""/><scripttype="text/javascript">varmyobject=newqx.ui.form.Button("","/vcl-bin/images/ok.gif",22,18);myobject.setLeft(0);myobject.setTop(0);myobject.setWidth(75);myobject.setHeight(25);myobject.setEnabled(true);myobject.setVisibility(true);varlblobj=myobject.getLabelObject();if(lblobj)lblobj.setFont("10px\'Verdana\'");myobject.setBackgroundColor(newqx.renderer.color.ColorObject(\'buttonface\'));myobject.addEventListener("mouseup",function(){this.setIcon("");});myobject.addEventListener("execute",function(e){document.forms[0].submit();returnfalse;});</script>');
                        $compare=$this->cleanString($compare);
                        $this->assertEquals(trim($c),trim($compare),'Comparing Kind generation');
                        //*******************
                        $this->object->ImageDisabled='test.gif';
                        $c=$this->object->show(true);
                        $c=$this->cleanString($c);
                        $c=$this->fixString($c);
                        $compare=trim('<scripttype="text/javascript">updateButtonTheme();</script><inputtype="hidden"id="myobject_state"name="myobject_state"value=""/><scripttype="text/javascript">varmyobject=newqx.ui.form.Button("","/vcl-bin/images/ok.gif",22,18);myobject.addEventListener("changeEnabled",function(){if(myobject.getEnabled()==false)myobject.setIcon("test.gif");elsemyobject.setIcon("");});myobject.setLeft(0);myobject.setTop(0);myobject.setWidth(75);myobject.setHeight(25);myobject.setEnabled(true);myobject.setVisibility(true);varlblobj=myobject.getLabelObject();if(lblobj)lblobj.setFont("10px\'Verdana\'");myobject.setBackgroundColor(newqx.renderer.color.ColorObject(\'buttonface\'));myobject.addEventListener("mouseup",function(){this.setIcon("");});myobject.addEventListener("execute",function(e){document.forms[0].submit();returnfalse;});</script>');
                        $compare=$this->cleanString($compare);
                        $this->assertEquals(trim($c),trim($compare),'Comparing ImageDisabled generation');
                        //*******************
                        $this->object->ImageClicked='test.gif';
                        $c=$this->object->show(true);
                        $c=$this->cleanString($c);
                        $c=$this->fixString($c);
                        $compare=trim('<scripttype="text/javascript">updateButtonTheme();</script><inputtype="hidden"id="myobject_state"name="myobject_state"value=""/><scripttype="text/javascript">varmyobject=newqx.ui.form.Button("","/vcl-bin/images/ok.gif",22,18);myobject.addEventListener("changeEnabled",function(){if(myobject.getEnabled()==false)myobject.setIcon("test.gif");elsemyobject.setIcon("");});myobject.setLeft(0);myobject.setTop(0);myobject.setWidth(75);myobject.setHeight(25);myobject.setEnabled(true);myobject.setVisibility(true);varlblobj=myobject.getLabelObject();if(lblobj)lblobj.setFont("10px\'Verdana\'");myobject.setBackgroundColor(newqx.renderer.color.ColorObject(\'buttonface\'));myobject.addEventListener("mousedown",function(){this.setIcon("test.gif");});myobject.addEventListener("mouseup",function(){this.setIcon("");});myobject.addEventListener("execute",function(e){document.forms[0].submit();returnfalse;});</script>');
                        $compare=$this->cleanString($compare);
                        $this->assertEquals(trim($c),trim($compare),'Comparing ImageClicked generation');
                        //*******************
                        $this->object->Spacing=8;
                        $c=$this->object->show(true);
                        $c=$this->cleanString($c);
                        $c=$this->fixString($c);
                        $compare=trim('<scripttype="text/javascript">updateButtonTheme();</script><inputtype="hidden"id="myobject_state"name="myobject_state"value=""/><scripttype="text/javascript">varmyobject=newqx.ui.form.Button("","/vcl-bin/images/ok.gif",26,18);myobject.addEventListener("changeEnabled",function(){if(myobject.getEnabled()==false)myobject.setIcon("test.gif");elsemyobject.setIcon("");});myobject.setLeft(0);myobject.setTop(0);myobject.setWidth(75);myobject.setHeight(25);myobject.setEnabled(true);myobject.setVisibility(true);varlblobj=myobject.getLabelObject();if(lblobj)lblobj.setFont("10px\'Verdana\'");myobject.setBackgroundColor(newqx.renderer.color.ColorObject(\'buttonface\'));myobject.addEventListener("mousedown",function(){this.setIcon("test.gif");});myobject.addEventListener("mouseup",function(){this.setIcon("");});myobject.addEventListener("execute",function(e){document.forms[0].submit();returnfalse;});</script>');
                        $compare=$this->cleanString($compare);
                        $this->assertEquals(trim($c),trim($compare),'Comparing Spacing generation');
                        //*******************
                        $this->object->ButtonType=btReset;
                        $c=$this->object->show(true);
                        $c=$this->cleanString($c);
                        $c=$this->fixString($c);
                        $compare=trim('<scripttype="text/javascript">updateButtonTheme();</script><inputtype="hidden"id="myobject_state"name="myobject_state"value=""/><scripttype="text/javascript">varmyobject=newqx.ui.form.Button("","/vcl-bin/images/ok.gif",26,18);myobject.addEventListener("changeEnabled",function(){if(myobject.getEnabled()==false)myobject.setIcon("test.gif");elsemyobject.setIcon("");});myobject.setLeft(0);myobject.setTop(0);myobject.setWidth(75);myobject.setHeight(25);myobject.setEnabled(true);myobject.setVisibility(true);varlblobj=myobject.getLabelObject();if(lblobj)lblobj.setFont("10px\'Verdana\'");myobject.setBackgroundColor(newqx.renderer.color.ColorObject(\'buttonface\'));myobject.addEventListener("mousedown",function(){this.setIcon("test.gif");});myobject.addEventListener("mouseup",function(){this.setIcon("");});myobject.addEventListener("execute",function(e){document.forms[0].reset();returnfalse;});</script>');
                        $compare=$this->cleanString($compare);
                        $this->assertEquals(trim($c),trim($compare),'Comparing ButtonType generation');
                }

                function test_CommonScript()
                {
                        ob_start();
                        $this->object->commonScript();
                        $actual=ob_get_contents();
                        ob_end_clean();
                        //print_r($actual);
                        $expected=trim('myobject.setLeft(0);
        myobject.setTop(0);
        myobject.setWidth(75);
        myobject.setHeight(25);
  myobject.setEnabled(true);
  myobject.setVisibility(true);
        var lblobj = myobject.getLabelObject();
        if (lblobj) lblobj.setFont("10px \'Verdana\' ");
        myobject.setBackgroundColor(new qx.renderer.color.ColorObject(\'buttonface\'));');

                        $actual=$this->fixString($actual);
                        $this->assertEquals($expected,$actual);


                }

                function test_dumpForAjax()
                {
                        //dumpForAjax just calls commonScript
                        $this->test_CommonScript();
                }

                function test_Action()
                {
                        $this->defaultTest('Action',null,'dummy value');
                }

                function test_Kind()
                {
                        $this->defaultTest('Kind',bkCustom,bkClose);
                }

                function test_ImageDisabled()
                {
                        $this->defaultTest('ImageDisabled','','img/test.gif');
                }

                function test_ImageClicked()
                {
                        $this->defaultTest('ImageClicked','','img/test.gif');
                }

                function test_Spacing()
                {
                        $this->defaultTest('Spacing',4,7);
                }

                function test_ButtonType()
                {
                        $this->defaultTest('ButtonType',btSubmit,btNormal);
                }

                function test_addOtherChildren()
                {
                  //This method is not implemented in this component, so override it
                }

                function test_getParentFont(){}
                function test_setParentFont(){}
                function test_getPopupMenu(){}
                function test_setPopupMenu(){}
        }
      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_bitbtn.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "BitBtnTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
