<?php

        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_component.inc.php";
        require_once "../../menus.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");

        class CustomPopupMenuTest extends ComponentTest
        {
                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;
                        $this->object=new CustomPopupMenu();
                        $this->object->Name="myobject";
                }

                function test_dumpHeaderCode()
                {
                        $this->setup();

                        $this->aowner->insertComponent($this->object);

                        $items=array();

                        $subitems=array();
                        $subitems[]=array(
                               'Caption'=>'Sub Menu1',
                               'ImageIndex'=>0,
                               'SelectedIndex'=>0,
                               'StateIndex'=>-1,
                               'Tag'=>1,
                               'Items'=>array()
                        );

                        $subitems[]=array(
                               'Caption'=>'Sub Menu2',
                               'ImageIndex'=>0,
                               'SelectedIndex'=>0,
                               'StateIndex'=>-1,
                               'Tag'=>2,
                               'Items'=>array()
                        );

                        $items[]=array(
                               'Caption'=>'Top Menu',
                               'ImageIndex'=>0,
                               'SelectedIndex'=>0,
                               'StateIndex'=>-1,
                               'Tag'=>0,
                               'Items'=>$subitems
                        );

                        $this->object->Items=$items;
                        ob_start();
                        $this->object->dumpHeaderCode();
                        $c=ob_get_contents();
                        $c=$this->cleanString($c);
                        $compare=$this->cleanString('<scripttype="text/javascript"src="/vcl-bin/qooxdoo/framework/script/qx.js"></script><scripttype="text/javascript">qx.log.Logger.ROOT_LOGGER.setMinLevel(qx.log.Logger.LEVEL_FATAL);qx.manager.object.AliasManager.getInstance().add("static","/vcl-bin/qooxdoo/framework/resource/static/");qx.manager.object.AliasManager.getInstance().add("widget","/vcl-bin/qooxdoo/framework/resource/widget/windows/");qx.manager.object.AliasManager.getInstance().add("icon","/vcl-bin/qooxdoo/framework/resource/icon/VistaInspirate/");</script><scripttype="text/javascript"><!--varmyobject;functionmyobjectCreateMenu(){if(typeofmyobject==\'undefined\'){vard=qx.ui.core.ClientDocument.getInstance();varmyobject_sm0_0_it1_0Cmd=newqx.client.Command();myobject_sm0_0_it1_0Cmd.addEventListener("execute",function(e){SubmitMenuEvent(e,1);});varmyobject_sm0_0_it1_0=newqx.ui.menu.Button("SubMenu1",null,myobject_sm0_0_it1_0Cmd);varmyobject_sm0_0_it1_1Cmd=newqx.client.Command();myobject_sm0_0_it1_1Cmd.addEventListener("execute",function(e){SubmitMenuEvent(e,2);});varmyobject_sm0_0_it1_1=newqx.ui.menu.Button("SubMenu2",null,myobject_sm0_0_it1_1Cmd);varmyobject_sm0_0=newqx.ui.menu.Menu();myobject_sm0_0.add(myobject_sm0_0_it1_0,myobject_sm0_0_it1_1);d.add(myobject_sm0_0);varmyobject_it0_0=newqx.ui.menu.Button("TopMenu",null,null,myobject_sm0_0);myobject=newqx.ui.menu.Menu();myobject.add(myobject_it0_0);d.add(myobject);}}functionSubmitMenuEvent(e,tag){submit=true;if((tag!=0)&&(submit)){varhid=findObj(\'myobject_state\');if(hid)hid.value=tag;if((document.StandardOwner.onsubmit)&&(typedef(document.StandardOwner.onsubmit))==\'function\'){document.StandardOwner.onsubmit();}document.StandardOwner.submit();}}functionShowmyobject(event,type){myobjectCreateMenu();if(myobject!=null){if(type==0){vartempX=0vartempY=0if(event.pageX||event.pageY){tempX=event.pageXtempY=event.pageY}else{tempX=event.clientX+document.body.scrollLeft-document.body.clientLefttempY=event.clientY+document.body.scrollTop-document.body.clientTop}}else{tempX=event.getPageX()tempY=event.getPageY()}if(tempX<0){tempX=0}if(tempY<0){tempY=0}myobject.setLeft(tempX);myobject.setTop(tempY);myobject.show();}}--></script>');
                        $this->assertEquals($c,$compare);
                        $this->aowner->removeComponent($this->object);
                }

                function test_dumpJavascript()
                {
                        $this->aowner->insertComponent($this->object);
                        ob_start();
                        $this->object->jsOnClick='dummyevent';
                        $this->object->dumpJavascript();
                        $c=ob_get_contents();
                        ob_end_clean();
//                        $c=$this->fixString($c);
                        $this->assertEquals($this->cleanString($c),$this->cleanString("function dummyevent(event) { var event = event || window.event; var params=null; }"));
                        $this->aowner->removeComponent($this->object);
                }

                function test_init()
                {
                        $this->aowner->insertComponent($this->object);

                        $this->assertEquals($this->aowner->executed,false,'Init test 1');

                        $_POST['myobject_state']='myobject_DummyEvent';

                        $this->object->owner->UseAjax=false;
                        $this->aowner->executed=false;
                        $this->object->OnClick="DummyEvent";
                        $this->object->init();
                        $this->assertEquals($this->aowner->executed,true,'Init test 2');

                }

                function test_dumpFormItems()
                {
                        ob_start();
                        $this->object->dumpFormItems();
                        $c=ob_get_contents();
                        ob_end_clean();
//                        $c=$this->fixString($c);
                        $this->assertEquals($this->cleanString($c),$this->cleanString('<inputtype="hidden"id="myobject_state"name="myobject_state"value=""/> '));
                }

                function test_dumpMenuItems()
                {
                                                $this->setup();

                        $this->aowner->insertComponent($this->object);

                        $items=array();

                        $subitems=array();
                        $subitems[]=array(
                               'Caption'=>'Sub Menu1',
                               'ImageIndex'=>0,
                               'SelectedIndex'=>0,
                               'StateIndex'=>-1,
                               'Tag'=>1,
                               'Items'=>array()
                        );

                        $subitems[]=array(
                               'Caption'=>'Sub Menu2',
                               'ImageIndex'=>0,
                               'SelectedIndex'=>0,
                               'StateIndex'=>-1,
                               'Tag'=>2,
                               'Items'=>array()
                        );

                        $items[]=array(
                               'Caption'=>'Top Menu',
                               'ImageIndex'=>0,
                               'SelectedIndex'=>0,
                               'StateIndex'=>-1,
                               'Tag'=>0,
                               'Items'=>$subitems
                        );

                        $this->object->Items=$items;
                        ob_start();
                        $this->object->dumpHeaderCode();
                        $c=ob_get_contents();
                        $c=$this->cleanString($c);
                        $compare=$this->cleanString('<scripttype="text/javascript"><!--varmyobject;functionmyobjectCreateMenu(){if(typeofmyobject==\'undefined\'){vard=qx.ui.core.ClientDocument.getInstance();varmyobject_sm0_0_it1_0Cmd=newqx.client.Command();myobject_sm0_0_it1_0Cmd.addEventListener("execute",function(e){SubmitMenuEvent(e,1);});varmyobject_sm0_0_it1_0=newqx.ui.menu.Button("SubMenu1",null,myobject_sm0_0_it1_0Cmd);varmyobject_sm0_0_it1_1Cmd=newqx.client.Command();myobject_sm0_0_it1_1Cmd.addEventListener("execute",function(e){SubmitMenuEvent(e,2);});varmyobject_sm0_0_it1_1=newqx.ui.menu.Button("SubMenu2",null,myobject_sm0_0_it1_1Cmd);varmyobject_sm0_0=newqx.ui.menu.Menu();myobject_sm0_0.add(myobject_sm0_0_it1_0,myobject_sm0_0_it1_1);d.add(myobject_sm0_0);varmyobject_it0_0=newqx.ui.menu.Button("TopMenu",null,null,myobject_sm0_0);myobject=newqx.ui.menu.Menu();myobject.add(myobject_it0_0);d.add(myobject);}}functionSubmitMenuEvent(e,tag){submit=true;if((tag!=0)&&(submit)){varhid=findObj(\'myobject_state\');if(hid)hid.value=tag;if((document.StandardOwner.onsubmit)&&(typedef(document.StandardOwner.onsubmit))==\'function\'){document.StandardOwner.onsubmit();}document.StandardOwner.submit();}}functionShowmyobject(event,type){myobjectCreateMenu();if(myobject!=null){if(type==0){vartempX=0vartempY=0if(event.pageX||event.pageY){tempX=event.pageXtempY=event.pageY}else{tempX=event.clientX+document.body.scrollLeft-document.body.clientLefttempY=event.clientY+document.body.scrollTop-document.body.clientTop}}else{tempX=event.getPageX()tempY=event.getPageY()}if(tempX<0){tempX=0}if(tempY<0){tempY=0}myobject.setLeft(tempX);myobject.setTop(tempY);myobject.show();}}--></script>');
                        $this->assertEquals($c,$compare);
                        $this->aowner->removeComponent($this->object);
                }


                function test_Items()
                {
                         //at start Images must be null
                        $this->assertEquals($this->object->Items,array());

                                                //Create a imageList link to a several main menues and check if everything is ok
                        $Items=array(
                                 0=>"Item1",1=>"Item2",2=>"Item3"
                                     );

                        //Read/write items check
                        $PopupMenu1= new CustomPopupMenu();
                        $PopupMenu2= new CustomPopupMenu();
                        $PopupMenu1->Items=$Items;
                        $PopupMenu2->Items=$Items;
                        $this->assertEquals($PopupMenu1->Items,$PopupMenu2->Items,"a");
                        $this->assertEquals($Items,$PopupMenu1->Items,"b");
                }

        }

        if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
           else $script=$_GET['script'];

        if (basename($script)=='test_custompopupmenu.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "CustomPopupMenuTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }

?>
