<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_qwidget.inc.php";
        use_unit("comctrls.inc.php");



        class CustomToolBarTest extends QWidgetTest
        {
                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;
                        $this->object=new CustomToolBar();
                        $this->object->Name="myobject";
                }

                function test_dumpHeaderCode()
                {
                        $this->aowner->insertComponent($this->object);
                        ob_start();
                        $this->object->dumpHeaderCode();
                        $c=ob_get_contents();
                        ob_clean();
                        $c=$this->cleanString($c);
                        $compare=$this->cleanString("<scripttype=\"text/javascript\"src=\"/vcl-bin/qooxdoo/framework/script/qx.js\"></script><scripttype=\"text/javascript\">qx.log.Logger.ROOT_LOGGER.setMinLevel(qx.log.Logger.LEVEL_FATAL);qx.manager.object.AliasManager.getInstance().add(\"static\",\"/vcl-bin/qooxdoo/framework/resource/static/\");qx.manager.object.AliasManager.getInstance().add(\"widget\",\"/vcl-bin/qooxdoo/framework/resource/widget/windows/\");qx.manager.object.AliasManager.getInstance().add(\"icon\",\"/vcl-bin/qooxdoo/framework/resource/icon/VistaInspirate/\");</script><scripttype=\"text/javascript\">functionmyobject_clickwrapper(e){submit=true;vartag=e.getTarget().tag;if((tag!=0)&&(submit)){varhid=findObj('myobject_state');if(hid)hid.value=tag;if((document.StandardOwner.onsubmit)&&(typeof(document.StandardOwner.onsubmit)=='function')){document.StandardOwner.onsubmit();}document.StandardOwner.submit();}}</script>");
                        $this->assertEquals($c,$compare);
                        $this->aowner->removeComponent($this->object);
                }

                function test_dumpContents()
                {
                        $items=array("button1","button2","button3");

                        $this->object->Items=$items;
                        $c=$this->object->show(true);
//                        print_r($c);
                        $c=$this->cleanString($c);
                        $compare=trim('<inputtype="hidden"id="myobject_state"name="myobject_state"value=""/><scripttype="text/javascript">varmyobject=newqx.ui.toolbar.ToolBar;myobject.setLeft(0);myobject.setTop(0);myobject.setWidth(300);myobject.setHeight(29);<!--PartMainStart-->vartbp=newqx.ui.toolbar.Part;vartbp_0=newqx.ui.toolbar.Button("b",null);tbp_0.addEventListener("execute",myobject_clickwrapper);tbp_0.tag=b;vartbp_1=newqx.ui.toolbar.Button("b",null);tbp_1.addEventListener("execute",myobject_clickwrapper);tbp_1.tag=b;vartbp_2=newqx.ui.toolbar.Button("b",null);tbp_2.addEventListener("execute",myobject_clickwrapper);tbp_2.tag=b;tbp.add(tbp_0,tbp_1,tbp_2);myobject.add(tbp);<!--PartMainEnd-->myobject.setEnabled(true);myobject.setVisibility(true);</script>');
                        $this->assertEquals(trim($c),$compare);
                }

                function test_dumpParts()
                {
                        $this->test_dumpContents();
                }

                function test_dumpButtons()
                {
                        $this->test_dumpParts();
                }

                function test_addOtherChildren()
                {
                        if($this->object->className()!="CustomToolBar")
                                $this->assertEquals(true,false);
                }



        }
      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_customtoolbar.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "CustomToolBarTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
