<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "../../buttons.inc.php";
        require_once "test_bitbtn.inc.php";
        use_unit("stdctrls.inc.php");

        class SpeedButtonTest extends BitBtnTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new SpeedButton();
                        $this->object->Name="myobject";
                }

                function test_loaded()
                {
                        $this->setup();
                        $this->assertEquals($this->object->Down,0,"loaded check 1");
                        $this->object->GroupIndex=1;
                        $_POST["myobjectDown"]="1";
                        $this->object->loaded();
                        $this->assertEquals($this->object->Down,1,"loaded check 2");
                }

                //This must be reviewed
                function test_dumpJavascript()
                {
                        ob_start();
                        $this->object->OnClick="DummyEvent";
                        $this->object->dumpJavascript();
                        $c=ob_get_contents();
                        ob_end_clean();
                        $compare=$this->cleanString('functionDummyEventWrapper(event,hiddenfield,submitvalue,wrappedfunc){varevent=event||window.event;submit1=true;submit2=true;if(typeof(wrappedfunc)==\'function\')submit1=wrappedfunc(event);hiddenfield.value=submitvalue;form=hiddenfield.form;if((form)&&(form.onsubmit)&&(typeof(form.onsubmit)==\'function\'))submit2=form.onsubmit();if((submit1)&&(submit2))form.submit();returnfalse;}');
                        $this->assertEquals($this->cleanString($c),$compare);
                }

                function test_addOtherChildren()
                {
                        if($this->object->className()!="SpeedButton")
                                $this->assertEquals(true,false);
                }

                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim('<input type="hidden" name="myobjectDown" id="myobjectDown" value="0" /><input type="hidden" id="myobject_state" name="myobject_state" value="" />
<script type="text/javascript">
        var myobject = new qx.ui.toolbar.RadioButton("");
        myobject.setAppearance(\'myobject\');
        myobject.setLeft(0);
        myobject.setTop(0);
        myobject.setWidth(25);
        myobject.setHeight(25);
        myobject.addEventListener("execute", function() { this.setChecked(false); }, myobject);
  myobject.setEnabled(true);
  myobject.setVisibility(true);
        var lblobj = myobject.getLabelObject();
        if (lblobj) lblobj.setFont("10px \'Verdana\' ");
        myobject.setBackgroundColor(new qx.renderer.color.ColorObject(\'buttonface\'));
</script>');
                        $this->assertEquals(trim($c),$compare);
                }


                function test_ControlStyle($styles=array())
                {
                        $styles["csRenderOwner"]=1;
                        $styles["csRenderAlso"]="SpeedButton";
                        parent::test_ControlStyle($styles);
                }

                function test_beginUpdateProperties()
                {
                        //Can not be tested (no accesors on private member)
                }

                function test_endUpdateProperties()
                {
                        //Can not be tested (no accesors on private member)
                }

                function test_getAllowAllUp()
                {
                        //tested int test_AllowAllUp
                }

                function test_setAllowAllUp()
                {
                          //tested int test_AllowAllUp
                }

                function test_AllowAllUp()
                {
                        $this->assertEquals($this->object->AllowAllUp,0);
                        //$this->assertEquals($this->object->AllowAllUp,defaultAllowAllUp());
                        $this->object->AllowAllUp=1;
                        $this->assertEquals($this->object->AllowAllUp,1);
                }

                function test_getDown()
                {
                        //tested int test_Down
                }
                function test_setDown()
                {
                        //tested int test_Down
                }

                function test_Down()
                {
                        $this->assertEquals($this->object->Down,0);
                        //if GroupIndex= 0, Down will be forced to 0 allways.
                        $this->object->Down=1;
                        $this->assertEquals($this->object->Down,0);

                        $this->object->GroupIndex=1;
                        $this->object->Down=1;
                        $this->assertEquals($this->object->Down,1);

                }

                function test_getFlat()
                {
                        //tested int test_Flat
                }
                function test_setFlat()
                {
                        //tested int test_Flat
                }
                function test_Flat()
                {
                        $this->assertEquals($this->object->Flat,0);
                        //$this->assertEquals($this->object->Flat,$this->defaultFlat());
                        $this->object->Flat=1;
                        $this->assertEquals($this->object->Flat,1);
                }

                function test_getGroupIndex()
                {
                        //tested int test_GroupIndex
                }
                function test_setGroupIndex()
                {
                        //tested int test_GroupIndex
                }
                function test_GroupIndex()
                {
                        $this->assertEquals($this->object->GroupIndex,0);
                        $this->assertEquals($this->object->GroupIndex,$this->object->defaultGroupIndex());
                        $this->object->GroupIndex=1;
                        $this->assertEquals($this->object->GroupIndex,1);
                }

                function test_dumpHeaderCode()
                {
                        ob_start();
                        $this->object->dumpHeaderCode();
                        $c=ob_get_contents();
                        ob_clean();
                        print_r($c);
                        $expected="<script type=\"text/javascript\" src=\"/vcl-bin/qooxdoo/framework/script/qx.js\"></script>
<script type=\"text/javascript\">
  qx.log.Logger.ROOT_LOGGER.setMinLevel(qx.log.Logger.LEVEL_FATAL);
  qx.manager.object.AliasManager.getInstance().add(\"static\", \"/vcl-bin/qooxdoo/framework/resource/static/\");
  qx.manager.object.AliasManager.getInstance().add(\"widget\", \"/vcl-bin/qooxdoo/framework/resource/widget/windows/\");
  qx.manager.object.AliasManager.getInstance().add(\"icon\", \"/vcl-bin/qooxdoo/framework/resource/icon/VistaInspirate/\");
</script>

<script type=\"text/javascript\">
  var theme = qx.manager.object.AppearanceManager.getInstance().getAppearanceTheme();
  theme.registerAppearance('myobject',
  {
    setup : function()
    {
      this.bgcolor_default = new qx.renderer.color.ColorObject(\"buttonface\");
      this.bgcolor_left = new qx.renderer.color.Color(\"#FFF0C9\");
            this.border_pressed = qx.renderer.border.BorderPresets.getInstance().inset;
      this.border_over = qx.renderer.border.BorderPresets.getInstance().outset;
      this.border_default = qx.renderer.border.BorderPresets.getInstance().outset;

      this.checked_background = \"static/image/dotted_white.gif\";
    },

    initial : function(vTheme)
    {
      var ret = vTheme.initialFrom(\"toolbar-button\");
    },

    state : function(vTheme, vStates)
    {
      var vReturn = vTheme.stateFrom(\"toolbar-button\", vStates);
      vReturn.backgroundColor = vStates.abandoned ? this.bgcolor_left : this.bgcolor_default;
      vReturn.backgroundImage = vStates.checked && !vStates.over ? this.checked_background : null;

      if (vStates.pressed || vStates.checked || vStates.abandoned) {
        vReturn.border = this.border_pressed;
      } else if (vStates.over) {
        vReturn.border = this.border_over;
      } else {
        vReturn.border = this.border_default;
      }

      return vReturn;
    }
  });
  theme.setupAppearance(theme.getAppearance('myobject'));
</script>";

                        $this->assertEquals($this->cleanString($c),$this->cleanString($expected));
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
        myobject.setWidth(25);
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
         }



      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_speedbutton.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "SpeedButtonTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
