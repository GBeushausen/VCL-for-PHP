<?php
        //Includes
        require_once("vcl/vcl.inc.php");
        require_once("dmAuthentication.php");
        use_unit("forms.inc.php");
        use_unit("extctrls.inc.php");
        use_unit("stdctrls.inc.php");
        use_unit("rtl.inc.php");

        global $LoginFormPage;

        //Class definition
        class AuthenticationPage extends Page
        {
               public $Label1 = null;
               function AuthenticationPageBeforeShow($sender, $params)
               {
                        global $dmAuthentication;

                        $dmAuthentication->ZAuth1->Execute();
               }


        }

        global $application;

        global $AuthenticationPage;

        //Creates the form
        $AuthenticationPage=new AuthenticationPage($application);

        //Read from resource file
        $AuthenticationPage->loadResource(__FILE__);

        //Shows the form
        $AuthenticationPage->show();

?>