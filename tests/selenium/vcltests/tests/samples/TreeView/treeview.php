<?php
        //Includes
        require_once("vcl/vcl.inc.php");
        use_unit("imglist.inc.php");
        use_unit("comctrls.inc.php");
        use_unit("forms.inc.php");
        use_unit("extctrls.inc.php");
        use_unit("stdctrls.inc.php");

        //Class definition
        class Unit1 extends Page
        {
               public $lblSelectedNode2 = null;
               public $lblSelectedNode1 = null;
               public $CheckBox1 = null;
               public $btnCollapsAll = null;
               public $btnExpandAll = null;
               public $btnRemoveLast = null;
               public $btnSelect3rd = null;
               public $btnAddToSelected = null;
               public $btnAddNode = null;
               public $ImageList1 = null;
               public $edtNodeName = null;
               public $TreeView1 = null;
               function CheckBox1Click($sender, $params)
               {
                        if ($this->CheckBox1->Checked)
                                $this->TreeView1->OnChangeSelected = "TreeView1ChangeSelected";
                        else
                                $this->TreeView1->OnChangeSelected = null;
               }

               function TreeView1ChangeSelected($sender, $params)
               {
                        if (is_object($params["treenode"]))
                                $this->lblSelectedNode2->Caption = "Selected: ".$params["treenode"]->Caption;
               }

               function TreeView1JSChangeSelected($sender, $params)
               {
               ?>
               //Add your javascript code here
               document.getElementById("lblSelectedNode1").innerHTML = "Selected node: " + TreeView1.getSelectedElement().getLabelObject().getHtml();
               <?php
               }

               function btnCollapsAllClick($sender, $params)
               {
                        $this->CollapsAll($this->TreeView1->Items);
               }

               function btnExpandAllClick($sender, $params)
               {
                        $this->ExpandAll($this->TreeView1->Items);
               }

               function ExpandAll($items)
               {
                        if (is_array($items))
                        {
                                foreach ($items as $item)
                                {
                                        $item->Expanded = true;
                                        $this->ExpandAll($item->Items);
                                }
                        }
               }

               function CollapsAll($items)
               {
                        if (is_array($items))
                        {
                                foreach ($items as $item)
                                {
                                        $item->Expanded = false;
                                        $this->CollapsAll($item->Items);
                                }
                        }
               }


               function btnRemoveLastClick($sender, $params)
               {
                        unset($this->TreeView1->Items[count($this->TreeView1->Items)-1]);
               }

               function btnSelect3rdClick($sender, $params)
               {
                        $counter = 0;
                        foreach ($this->TreeView1->Items as $item)
                        {
                                $counter++;
                                if ($counter == 3)
                                {
                                        $this->TreeView1->SelectedItemID = $item->ItemID;
                                }
                        }
               }

               function btnAddToSelectedClick($sender, $params)
               {
                        $node = $this->TreeView1->findNodeWithItemID($this->TreeView1->SelectedItemID);
                        if ($node != null)
                               $node->addChild($this->edtNodeName->Text, 0, 1, 1);
               }

               function btnAddNodeClick($sender, $params)
               {
                        $this->TreeView1->addNodeToItems($this->edtNodeName->Text, 0, 1, 1);
               }

        }

        global $application;

        global $Unit1;

        //Creates the form
        $Unit1=new Unit1($application);

        //Read from resource file
        $Unit1->loadResource(__FILE__);

        //Shows the form
        $Unit1->show();

?>
