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
               public $Edit1 = null;
               public $Label1 = null;
               public $Button1 = null;
               public $UpDown1 = null;
               function Edit1JSFocus($sender, $params)
               {

               ?>
               //Add your javascript code here
//                alert('onfocus');
               <?php

               }

               function Edit1JSBlur($sender, $params)
               {

               ?>
               //Add your javascript code here
//                        alert('onblur');
               <?php

               }

               function Edit1JSChange($sender, $params)
               {

               ?>
               //Add your javascript code here
                alert('onchange');
               <?php

               }


               function Button1Click($sender, $params)
               {
                        $this->Label1->Caption=$this->UpDown1->Position;
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