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
               public $GroupBox1 = null;

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