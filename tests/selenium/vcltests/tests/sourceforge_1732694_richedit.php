<?php
        //Includes
        require_once("vcl/vcl.inc.php");
        use_unit("buttons.inc.php");
        use_unit("comctrls.inc.php");
        use_unit("menus.inc.php");
        use_unit("forms.inc.php");
        use_unit("extctrls.inc.php");
        use_unit("stdctrls.inc.php");

        //Class definition
        class Unit537 extends Page
        {
               public $Label1 = null;
               public $Button2 = null;
               public $Button1 = null;
               public $RichEdit1 = null;
               function Button2JSClick($sender, $params)
               {
                echo $this->Button2->ajaxCall("updatelabel",$params, array("Label1","RichEdit1"));
               ?>
               //Add your javascript code here
               
               <?php

               }


               function Button1JSClick($sender, $params)
               {
                echo $this->Button1->ajaxCall("updateevent",array(),array("RichEdit1"));
               ?>
               //Add your javascript code here
               
               <?php

               }

               function updateevent($sender, $params)
               {
                $this->RichEdit1->Lines=array("this<br>is<br>html");
               }

               function updatelabel($sender, $params)
               {
                $this->Label1->Caption=$this->RichEdit1->LinesAsHTML();
               }

        }

        global $application;

        global $Unit537;

        //Creates the form
        $Unit537=new Unit537($application);

        //Read from resource file
        $Unit537->loadResource(__FILE__);

        //Shows the form
        $Unit537->show();

?>