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
               public $Button1 = null;
               public $DateTimePicker3 = null;
               public $GroupBox1 = null;
               public $DateTimePicker2 = null;
               public $Panel1 = null;
               public $DateTimePicker1 = null;
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