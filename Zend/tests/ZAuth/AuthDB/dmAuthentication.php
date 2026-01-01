<?php
        //Includes
        require_once("vcl/vcl.inc.php");
        use_unit("Zend/zauthdigest.inc.php");
        use_unit("Zend/zauth.inc.php");
        use_unit("Zend/zauthdb.inc.php");
        use_unit("forms.inc.php");
        use_unit("extctrls.inc.php");
        use_unit("stdctrls.inc.php");

        //Class definition
        class dmAuthentication extends DataModule
        {
               public $ZAuthDB1 = null;
               public $ZAuth1=null;
               function ZAuth1Login($sender, $params)
               {
                redirect("LoginForm.inc.php");
               }

        }

        global $application;

        global $dmAuthentication;

        //Creates the form
        $dmAuthentication=new dmAuthentication($application);

        //Read from resource file
        $dmAuthentication->loadResource(__FILE__);

?>