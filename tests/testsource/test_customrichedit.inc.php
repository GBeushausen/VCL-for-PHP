<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_custommemo.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("comctrls.inc.php");

        class CustomRichEditTest extends CustomMemoTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new CustomRichedit();
                        $this->object->Name="myobject";
                }


                function test_RichEditJSEvents()
                {
                        $this->object->jsOnBlur="myblurevent";
                        $this->object->jsOnBlur=      "blur";
                                $this->object->jsOnChange=    "change";
                                $this->object->jsOnClick=     "click";
                                $this->object->jsOnDblClick=  "dblclick";
                                $this->object->jsOnFocus=     "focus";
                                $this->object->jsOnMouseDown= "mousedown";
                                $this->object->jsOnMouseUp=   "mouseup";
                                $this->object->jsOnMouseOver= "mouseover";
                                $this->object->jsOnMouseMove= "mousemove";
                                $this->object->jsOnMouseOut=  "mouseout";
                                $this->object->jsOnKeyPress=  "keypress";
                                $this->object->jsOnKeyDown=   "keydown";
                                $this->object->jsOnKeyUp=     "keyup";
                                $this->object->jsOnSelect=    "select";

                        $c=$this->cleanString($this->object->RichEditJsEvents);
                        $expected=$this->cleanString('HTMLArea._addEvent(html_editor._htmlArea, "blur", blur);
        HTMLArea._addEvent(html_editor._doc, "blur", blur);
        HTMLArea._addEvent(html_editor._htmlArea, "change", change);
        HTMLArea._addEvent(html_editor._doc, "change", change);
        HTMLArea._addEvent(html_editor._htmlArea, "click", click);
        HTMLArea._addEvent(html_editor._doc, "click", click);
        HTMLArea._addEvent(html_editor._htmlArea, "dblclick", dblclick);
        HTMLArea._addEvent(html_editor._doc, "dblclick", dblclick);
        HTMLArea._addEvent(html_editor._htmlArea, "focus", focus);
        HTMLArea._addEvent(html_editor._doc, "focus", focus);
        HTMLArea._addEvent(html_editor._htmlArea, "mousedown", mousedown);
        HTMLArea._addEvent(html_editor._doc, "mousedown", mousedown);
        HTMLArea._addEvent(html_editor._htmlArea, "mouseup", mouseup);
        HTMLArea._addEvent(html_editor._doc, "mouseup", mouseup);
        HTMLArea._addEvent(html_editor._htmlArea, "mouseover", mouseover);
        HTMLArea._addEvent(html_editor._doc, "mouseover", mouseover);
        HTMLArea._addEvent(html_editor._htmlArea, "mousemove", mousemove);
        HTMLArea._addEvent(html_editor._doc, "mousemove", mousemove);
        HTMLArea._addEvent(html_editor._htmlArea, "mouseout", mouseout);
        HTMLArea._addEvent(html_editor._doc, "mouseout", mouseout);
        HTMLArea._addEvent(html_editor._htmlArea, "keypress", keypress);
        HTMLArea._addEvent(html_editor._doc, "keypress", keypress);
        HTMLArea._addEvent(html_editor._htmlArea, "keydown", keydown);
        HTMLArea._addEvent(html_editor._doc, "keydown", keydown);
        HTMLArea._addEvent(html_editor._htmlArea, "keyup", keyup);
        HTMLArea._addEvent(html_editor._doc, "keyup", keyup);
        HTMLArea._addEvent(html_editor._htmlArea, "select", select);
        HTMLArea._addEvent(html_editor._doc, "select", select);');

                        $this->assertEquals($c,$expected);
                }

                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        //print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim('<textarea id="myobject" name="myobject" style=" font-family: Verdana; font-size: 10px;  height:269px;width:400px;"    tabindex="0"    wrap="virtual"></textarea>');
                        $this->assertEquals(trim($c),$compare);
                }

                function test_addOtherChildren()
                {
                        if ($this->object->className()!="CustomRichEdit")
                                $this->assertEquals(true,false);
                }

                function test_dumpForAjax()
                {
                        ob_start();
                        $this->object->dumpForAjax();
                        $actual=ob_get_contents();
                        ob_end_clean();
                        //print_r($actual);
                        $expected=trim('var html_editor = myobject_html_editor;
        html_editor._textArea.value = "";
        html_editor.setHTML("");');

                        $actual=trim($actual);
                        $this->assertEquals($expected,$actual);
                }

                function test_dumpHeaderCode()
                {
                        ob_start();
                        $this->object->dumpHeaderCode();
                        $c=ob_get_contents();
                        $c=$this->cleanString($c);
                        //print_r($c);
                        $compare=$this->cleanString("<scripttype=\"text/javascript\">_editor_url=\"/vcl-bin/resources/xinha/\";_editor_lang=\"en\";//Andthelanguageweneedtouseintheeditor.</script><scripttype=\"text/javascript\"src=\"/vcl-bin/resources/xinha/htmlarea.js\"></script><scripttype=\"text/javascript\">varmyobject_previous_load=null;varmyobject_html_editor=null;xinha_init=null;//ThiscontainsthenamesoftextareaswewillmakeintoXinhaeditorsxinha_init=xinha_init?xinha_init:function(){xinha_editors=null;xinha_config=null;xinha_plugins=null;xinha_plugins=xinha_plugins?xinha_plugins:[];if(!HTMLArea.loadPlugins(xinha_plugins,xinha_init))return;xinha_editors=xinha_editors?xinha_editors:['myobject'];xinha_config=xinha_config?xinha_config:newHTMLArea.Config();xinha_config.pageStyle='body{font-family:Verdana;font-size:10px;}';xinha_editors=HTMLArea.makeEditors(xinha_editors,xinha_config,xinha_plugins);myobject_html_editor=xinha_editors['myobject'];//xinha_editors.myobject.config.width=400;//xinha_editors.myobject.config.height=270;HTMLArea.startEditors(xinha_editors);if(myobject_previous_load!=null)myobject_previous_load();}myobject_previous_load=window.onload;window.onload=xinha_init;functionupdateEditor_myobject(){varhtml_editor=myobject_html_editor;HTMLArea._addEvent(html_editor._htmlArea,\"mouseout\",function(){html_editor._textArea.value=html_editor.getHTML();});}</script><scripttype=\"text/javascript\">//allowenoughtimetoloadthepage;seepublicvariabletochangethetimesetTimeout(\"updateEditor_myobject()\",5000);</script>");
                        $this->assertEquals($c,$compare);
                }

        }

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_customrichedit.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "CustomRichEditTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }
?>
