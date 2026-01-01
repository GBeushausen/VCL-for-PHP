<?php
        //Includes
        require_once("vcl/vcl.inc.php");
        use_unit("Zend/zacl.inc.php");
        use_unit("Zend/zauthdigest.inc.php");
        use_unit("Zend/zauth.inc.php");
        use_unit("Zend/zauthdb.inc.php");
        use_unit("forms.inc.php");
        use_unit("extctrls.inc.php");
        use_unit("stdctrls.inc.php");

        //Class definition
        class dmAuthentication extends DataModule
        {
               public $ZAuthDB2 = null;
               public $ZACL1 = null;
               public $ZAuthDB1 = null;
               public $ZAuth1=null;

               function loaded()
               {
                        $this->ZACL1->setRules(array("user"=>array(null,"lbAll","+show"),
                                                         "administrator"=>array("user","lbAdministrator","+show")));

                        $response=$this->ZACL->isAllowed("user","lbAdministrator","show")? "yes": "no";
                        echo("is user allowed to see lbAdministrator? ". $response."<br>");
                        $response=$this->ZACL->isAllowed("administrator","lbAll","show")? "yes": "no";
                        echo("is administrator allowed to see lbAll? ".$response."<br>");

               }

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