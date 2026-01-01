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
               public $SpeedButton1 = null;
               public $BitBtn1 = null;
               public $LabeledEdit1 = null;
               public $Edit1 = null;
               public $Button1 = null;
               public $Label1 = null;


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