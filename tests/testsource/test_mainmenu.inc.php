<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_custommainmenu.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");


        class MainMenuTest extends CustomMainMenuTest
        {
                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;
                        $this->object=new MainMenu();
                        $this->object->Name="myobject";
                }

                function test_dumpHeaderCode()
                {
                        $this->setup();
                        $this->aowner->insertComponent($this->object);
                        ob_start();
                        $this->object->dumpHeaderCode();
                        $c=ob_get_contents();
                        $c=$this->cleanString($c);
                        $compare=$this->cleanString("<scripttype=\"text/javascript\"src=\"/vcl-bin/qooxdoo/framework/script/qx.js\"></script><scripttype=\"text/javascript\">qx.log.Logger.ROOT_LOGGER.setMinLevel(qx.log.Logger.LEVEL_FATAL);qx.manager.object.AliasManager.getInstance().add(\"static\",\"/vcl-bin/qooxdoo/framework/resource/static/\");qx.manager.object.AliasManager.getInstance().add(\"widget\",\"/vcl-bin/qooxdoo/framework/resource/widget/windows/\");qx.manager.object.AliasManager.getInstance().add(\"icon\",\"/vcl-bin/qooxdoo/framework/resource/icon/VistaInspirate/\");</script><scripttype=\"text/javascript\">functionmyobject_clickwrapper(e){submit=true;vartag=e.getTarget().tag;if((tag!=0)&&(submit)){varhid=findObj('myobject_state');if(hid)hid.value=tag;if((document.StandardOwner.onsubmit)&&(typeof(document.StandardOwner.onsubmit)=='function')){document.StandardOwner.onsubmit();}document.StandardOwner.submit();}}</script>");
                        $this->assertEquals($c,$compare);
                        $this->aowner->removeComponent($this->object);
                }

                function test_addOtherChildren()
                {
                        if ($this->object->className()!="MainMenu")
                                $this->assertEquals(true,false);
                }
        }

        if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
           else $script=$_GET['script'];

        if (basename($script)=='test_mainmenu.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "MainMenuTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }
?>
