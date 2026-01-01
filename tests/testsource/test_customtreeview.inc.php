<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_qwidget.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("comctrls.inc.php");

        class CustomTreeViewTest extends QWidgetTest
        {
                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;
                        $this->object=new CustomTreeView();
                        $this->object->Name="myobject";
                }

                function test_SelectedItemID()
                {
                  $this->defaultTest('SelectedItemID',-1,10);
                }

                function test_init()
                {
                        $this->aowner->insertComponent($this->object);

                        $this->assertEquals($this->aowner->executed,false,'Init test 1');

                        $this->object->addNodeToItems("node1");
                        $this->object->addNodeToItems("node2");
                        $this->object->addNodeToItems("node3");
                        $Items=$this->object->readItems();
                        $id1=$Items[0]->getItemID();
                        $id2=$Items[1]->getItemID();

                        $_POST['myobjectExpandedNodes']="$id1,$id2";
                        $_POST['myobjectSelItemID']="yeahhh";

                        $_POST['myobjectSubmitEvent']='myobject_DummyEvent';
                        $this->aowner->executed=false;
                        $this->object->writeOnChangeSelected("DummyEvent");
                        $this->object->init();
                        $this->assertEquals($this->aowner->executed,true,'Init test 2');
                        $this->aowner->removeComponent($this->object);


                }

                function test_dumpJavascript()
                {
                        $this->setup();

                        ob_start();
                        $this->object->dumpJavaScript();
                        $c=$this->cleanString(ob_get_contents());
                        ob_clean();
                        $contents=$this->cleanString("function AnotherEvent(event){var event = event || window.event;var params=null;}function myobjectUpdateExpandedTreeNodes() { updateExpandedTreeNodes(myobject, document.forms[0].myobjectExpandedNodes); if(typeof(myobject_formonsubmit) == 'function') myobject_formonsubmit(); return true; } function updateExpandedTreeNodes(tree, hiddenfield) { var res = new Array(); var items = tree.getItems(); var item; var opencount = 0; for (var i=0; i<items.length; i++) { item = items[i]; if (typeof(item.getOpen) == 'function' && item.getOpen()) { res[opencount] = item.itemid; opencount++; } } hiddenfield.value = res.toString(); } function formSubmit() { var res = true; if (typeof(document.forms[0].onsubmit) == 'function') res = document.forms[0].onsubmit(); if (res) document.forms[0].submit(); }");
                        $this->cleanString($c,$contents);
                }

                function test_dumpRow()
                {
                        $this->test_dumpItem();
                }

                function test_dumpItem()
                {
                        $this->test_dumpContents();
                }

                function test_dumpContents()
                {
                        $this->object->addNodeToItems("node1");
                        $this->object->addNodeToItems("node2");
                        $this->object->addNodeToItems("node3");
                        $Items=$this->object->Items;

                        $Items[0]->ItemID=0;
                        $Items[1]->ItemID=0;
                        $Items[2]->ItemID=0;

                        $c=$this->object->show(true);
                        //print_r($c);
                        $c=$this->fixString($c);
                        $c=$this->cleanString($c);
                        $compare=$this->cleanString("<inputtype=\"hidden\"name=\"myobjectSelItemID\"id=\"myobjectSelItemID\"value=\"-1\"/><inputtype=\"hidden\"name=\"myobjectExpandedNodes\"id=\"myobjectExpandedNodes\"value=\"\"/><inputtype=\"hidden\"id=\"myobject_state\"name=\"myobject_state\"value=\"\"/><scripttype=\"text/javascript\">varmyobject_formonsubmit=document.forms[0].onsubmit;document.forms[0].onsubmit=myobjectUpdateExpandedTreeNodes;vartrsroot=qx.ui.treefullcontrol.TreeRowStructure.getInstance().standard(\"Items\");varmyobject=newqx.ui.treefullcontrol.Tree(trsroot);vartrs=null;trs=qx.ui.treefullcontrol.TreeRowStructure.getInstance().standard(\"node1\");varp_1_1=newqx.ui.treefullcontrol.TreeFile(trs);p_1_1.itemid='0';p_1_1.tag=0;p_1_1.setEnabled(true);myobject.add(p_1_1);trs=qx.ui.treefullcontrol.TreeRowStructure.getInstance().standard(\"node2\");varp_1_2=newqx.ui.treefullcontrol.TreeFile(trs);p_1_2.itemid='0';p_1_2.tag=0;p_1_2.setEnabled(true);myobject.add(p_1_2);trs=qx.ui.treefullcontrol.TreeRowStructure.getInstance().standard(\"node3\");varp_1_3=newqx.ui.treefullcontrol.TreeFile(trs);p_1_3.itemid='0';p_1_3.tag=0;p_1_3.setEnabled(true);myobject.add(p_1_3);myobject.setUseDoubleClick(true);myobject.setUseTreeLines(true);myobject.setHideNode(true);myobject.setBorder(qx.renderer.border.BorderPresets.getInstance().inset);myobject.setBackgroundColor(\"white\");myobject.setEnabled(true);myobject.setLeft(0);myobject.setTop(0);myobject.setOpen(1);myobject.setOverflow(\"scroll\");myobject.setWidth(300);myobject.setHeight(320);myobject.getManager().addEventListener(\"changeSelection\",function(e){document.forms[0].myobjectSelItemID.value=e.getData()[0].itemid;});myobject.setEnabled(true);myobject.setVisibility(true);</script>");
                        $this->assertEquals($c,$compare);
                }


                function test_addOtherChildren()
                {
                        if ($this->object->className()!="CustomTreeView")
                                $this->assertEquals(true,false);
                }

                function test_convertPureArrayToTreeNodes()
                {

                        $nodes=array(0=>array('Caption'=>"node1",'ImageIndex'=>1,'SelectedIndex'=>0,'Tag'=>2,'Items'=>array(0=>array('Caption'=>"node11",'ImageIndex'=>2,'SelectedIndex'=>1,'Tag'=>1))));
                        $nodes=$this->object->convertPureArrayToTreeNodes($nodes);

                        $node11=$nodes[0]->Items[0];
                        $node1=$nodes[0];
                        $this->assertEquals($node11->Caption,"node11","PureArrayToTreeNOdes test 1");
                        $this->assertEquals($node11->Expanded,0,"PureArrayToTreeNOdes test 2");
                        $this->assertEquals($node11->ImageIndex,2,"PureArrayToTreeNodes Test 3");
                        $this->assertEquals($node11->ItemID,$nodes[0]->Items[0]->ItemID,"PureArrayToTreeNodes Test 4 ");
                        $this->assertEquals($node11->Level,1,"PureArrayToTreeNodes Test  5");
                        $this->assertEquals($node11->SelectedIndex,1,"PureArrayToTreeNodes Test  6");
                        $this->assertEquals($node11->Tag,1,"PureArrayToTreeNodes Test  7");
                        $this->assertEquals($node11->ParentNode->Caption,"node1","PureArrayToTreeNodes Test  8");

                        $this->assertEquals($node1->Caption,"node1","PureArrayToTreeNOdes test 9");
                        $this->assertEquals($node1->Expanded,0,"PureArrayToTreeNOdes test 10");
                        $this->assertEquals($node1->ImageIndex,1,"PureArrayToTreeNodes Test 11");
                        $this->assertEquals($node1->Level,0,"PureArrayToTreeNodes Test  13");
                        $this->assertEquals($node1->SelectedIndex,0,"PureArrayToTreeNodes Test  14");
                        $this->assertEquals($node1->Tag,2,"PureArrayToTreeNodes Test  15");
                        $this->assertEquals($node1->ParentNode,null,"PureArrayToTreeNodes Test  16");

                }


                function test_addNodeToItems()
                {
                        //Checked within test_convertPureArrayTOTreeNodes()
                        $this->object->addNodeToItems("node1");
                        $this->object->addNodeToItems("node2");
                        $this->assertEquals(get_class($this->object->Items[0]),"TreeNode");
                        $this->assertEquals(get_class($this->object->Items[1]),"TreeNode");
                        $this->assertEquals($this->object->Items[0]->Caption,"node1");
                        $this->assertEquals($this->object->Items[1]->Caption,"node2");
                }
                function test_findNodeWithItemID()
                {
                        $this->object->addNodeToItems("node1");
                        $this->object->addNodeToItems("node2");
                        $node=$this->object->findNodeWithItemID($this->object->Items[0]->getItemID());
                        $this->assertEquals(get_class($node),"TreeNode");
                        $this->assertEquals($node->Caption,"node1");
                }
                function test_ImageList()
                {
                        //at start Images must be null
                        $this->assertEquals($this->object->ImageList,$this->object->defaultImageList(),"ImageList test 1");

                        //get/set check
                        $ImageList=new ImageList();
                        $ImageList->Images=array(0=>"image1", 1=>"image2", 2=>"image3");
                        $TreeView1= new CustomTreeView();
                        $TreeView2= new CustomTreeView();
                        $TreeView1->ImageList=$ImageList;
                        $TreeView2->ImageList=$ImageList;
                        $this->assertEquals($TreeView1->ImageList,$TreeView2->ImageList,"ImageList test 2");
                        $this->assertEquals($ImageList,$TreeView1->ImageList,"ImageList test 3");
                }

                function test_Items()
                {
                         //at start Items must be null
                        $this->assertEquals($this->object->Items,array());

                         //Create a ItemList to link to a several main menues and check if everything is ok
                        $Items=array(
                                 0=>"Item1",1=>"Item2",2=>"Item3"
                                     );

                        //Read/write items check
                        $CustomTreeView1= new CustomTreeView();
                        $CustomTreeView2= new CustomTreeView();
                        $CustomTreeView1->Items=$Items;
                        $CustomTreeView2->Items=$Items;
                        $this->assertEquals($CustomTreeView1->Items,$CustomTreeView2->Items,"a");
                        $this->assertEquals($Items,$CustomTreeView1->Items,"b");
                }

                function test_SelectedItem()
                {
                        $this->assertEquals($this->object->SelectedItemID,-1);
                        $this->assertEquals($this->object->SelectedItemID,$this->object->defaultSelectedItemID());
                        $this->object->SelectedItemID=2;
                        $this->assertEquals($this->object->SelectedItemID,2);
                }
                function test_ShowLines()
                {
                        $this->assertEquals($this->object->ShowLines,1);
                        $this->assertEquals($this->object->ShowLines,$this->object->defaultShowLines());
                        $this->object->ShowLines=0;
                        $this->assertEquals($this->object->ShowLines,0);
                }
                function test_ShowRoot()
                {
                        $this->assertEquals($this->object->ShowRoot,0);
                        $this->assertEquals($this->object->ShowRoot,$this->object->defaultShowRoot());
                        $this->object->ShowRoot=false;
                        $this->assertEquals($this->object->ShowRoot,false);
                }
                function test_jsOnChangeSelected()
                {
                        $this->assertEquals($this->object->jsOnChangeSelected,null);
                        $this->assertEquals($this->object->jsOnChangeSelected,null);
                        $this->object->jsOnChangeSelected="myevent";
                        $this->assertEquals($this->object->jsOnChangeSelected,"myevent");
                }
                function test_jsOnTreeClose()
                {
                        $this->assertEquals($this->object->jsOnTreeClose,null);
                        $this->object->jsOnTreeClose="myevent";
                        $this->assertEquals($this->object->jsOnTreeClose,"myevent");
                }
                function test_jsOnTreeOpenWhileEmpty()
                {
                        $this->assertEquals($this->object->jsOnTreeOpenWhileEmpty,null);
                        $this->object->jsOnTreeOpenWhileEmpty="myevent";
                        $this->assertEquals($this->object->jsOnTreeOpenWhileEmpty,"myevent");
                }
                function test_jsOnTreeOpenWithContent()
                {
                        $this->assertEquals($this->object->jsOnTreeOpenWithContent,null);
                        $this->object->jsOnTreeOpenWithContent="myevent";
                        $this->assertEquals($this->object->jsOnTreeOpenWithContent,"myevent");
                }
                function test_OnChangeSelected()
                {
                        $this->assertEquals($this->object->OnChangeSelected,null);
                        $this->object->OnChangeSelected="myevent";
                        $this->assertEquals($this->object->OnChangeSelected,"myevent");
                }

                function test_getRootNodeCaption() {}
                function test_setRootNodeCaption() {}
                function test_RootNodeCaption()
                {
                        $this->assertEquals("Items",$this->object->RootNodeCaption);
                        $this->assertEquals($this->object->defaultRootNodeCaption(),$this->object->RootNodeCaption);
                        $this->object->RootNodeCaption="mynodecaption";
                        $this->assertEquals($this->object->RootNodeCaption,"mynodecaption");
                }
}

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_customtreeview.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "CustomTreeViewTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
