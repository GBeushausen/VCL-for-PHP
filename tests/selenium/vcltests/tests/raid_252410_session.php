<?php
        //Includes
        require_once("vcl/vcl.inc.php");
        use_unit("comctrls.inc.php");
        use_unit("menus.inc.php");
        use_unit("forms.inc.php");
        use_unit("extctrls.inc.php");
        use_unit("stdctrls.inc.php");

        //Class definition
        class Unit537 extends Page
        {
               public $Button1 = null;
               public $Label2 = null;
               public $Label1 = null;
               function Button1Click($sender, $params)
               {
                       $this->Label2->Caption = $this->Label1->Caption;
               }

               function Unit537Show($sender, $params)
               {
                        $this->Label1->Caption = 'ABC';
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