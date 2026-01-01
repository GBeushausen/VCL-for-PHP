<?php
        //Includes
        require_once("vcl/vcl.inc.php");
        use_unit("comctrls.inc.php");
        use_unit("forms.inc.php");
        use_unit("extctrls.inc.php");
        use_unit("stdctrls.inc.php");

        //Class definition
        class Unit1 extends Page
        {
               public $Label1 = null;
               public $ListBox1 = null;
               public $Button1 = null;
               public $ListView1 = null;
               
               function ListView1Submit($sender, $params)
               {
                        $this->ListBox1->Clear();
                        $lv = $this->ListView1;

                        // get all selected ListView items
                        foreach($lv->Items as $item)
                        {
                                if ($item->Selected)
                                {
                                        $this->ListBox1->AddItem($item->Caption);
                                }
                        }
               }

               function Button1Click($sender, $params)
               {
                        // this OnClick event is just used so that the form
                        // is submitted and the persistence of the
                        // selected items/rows can be tested.
                        // Note that it is all handeled by the ListView.
               }

               /**
               * All commented properties work, uncomment them to test.
               */
               function Unit1Create($sender, $params)
               {
                        $lv = $this->ListView1;

                        //$lv->SortColumnIndex = 2;
                        //$lv->SortAscending = false;

                        // get the first column
                        $col = $lv->Columns[0];
                        $col->Width = 150;
                        $col->Editable = true;
                        //$col->Visible = false;

                        $col = $lv->Columns[2];
                        // if creBoolean is used the cell can not be editable
                        // currently this is a limitation by qooxdoo
                        $col->Editable = false;
                        $col->CellRenderType = creBoolean;

                        // only add the items if they are not already in the session
                        if (count($lv->Items) == 0)
                        {
                                $lv->addItem("First item", array("subitem 1", "true", "subitem 3"));
                                $lv->addItem("Second item", array("column 2", "false", "column 4"));
                                for ($x = 0; $x < 50; $x++)
                                {
                                        $lv->addItem($x, array("column 2", ($x%2 == 0 ? "true" : "false"), "column 4"));
                                }

                                // modify the fifth item
                                $li = $lv->Items[4];
                                $li->Caption = "Fifth item";
                                $li->Selected = true;              // select the item/row
                                $li->Data = "Custom data to add";  // you can add any data here...
                                $li->SubItems[2] = "sub item 3";   // change the string of the third sub item
                        }
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

