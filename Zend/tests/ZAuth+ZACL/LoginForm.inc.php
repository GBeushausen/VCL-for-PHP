<?php
        //Includes
        require_once("vcl/vcl.inc.php");
        require_once("dmAuthentication.php");
        use_unit("forms.inc.php");
        use_unit("extctrls.inc.php");
        use_unit("stdctrls.inc.php");
        use_unit("rtl.inc.php");

        //Class definition
        class LoginFormPage extends Page
        {
               public $edUserRealm = null;
               public $lbRole = null;
               public $Button1 = null;
               public $edUserPassword = null;
               public $edUserName = null;
               public $Label2 = null;
               public $Label1 = null;
               function Button1Click($sender, $params)
               {
                        global $dmAuthentication;

                        $dmAuthentication->ZAuth1->UserName=$this->edUserName->Text;
                        $dmAuthentication->ZAuth1->UserPassword=$this->edUserPassword->Text;
                        $dmAuthentication->ZAuth1->UserRealm=$this->edUserRealm->Text;
                        redirect("Authentication.php");
               }


        }


        global $application;

        global $LoginFormPage;

        //Creates the form
        $LoginFormPage=new LoginFormPage($application);

        //Read from resource file
        $LoginFormPage->loadResource(__FILE__);

        //Shows the form
        $LoginFormPage->show();

?>