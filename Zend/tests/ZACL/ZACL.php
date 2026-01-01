<?php
        //Includes
        require_once("vcl/vcl.inc.php");
        use_unit("actnlist.inc.php");
        use_unit("forms.inc.php");
        use_unit("extctrls.inc.php");
        use_unit("stdctrls.inc.php");

        use_unit("Zend/zacl.inc.php");

        //Class definition
        class Unit1 extends Page
        {
               public $ZACL1 = null;
               function loaded()
               {
                        $this->ZACL1->setRules(array("guest"=>array(null,"mypage.php","+show","Action1","+execute"),
                                                     "user"=>array("guest","myuserpage.php","+show","mypage.php","-show")));

                        $response=$this->ZACL1->isAllowed("guest","mypage.php","show")? "yes": "no";
                        echo("is Guest allowed to access mypage.php? ". $response."<br>");
                        $response=$this->ZACL1->isAllowed("guest","Action1","execute")? "yes": "no";
                        echo("is Guest allowed to execute Action1? ".$response."<br>");
                        $response=$this->ZACL1->isAllowed("guest","myuserpage.php","show")? "yes":"no";
                        echo("is Guest allowed to access myuserpage.php? ".$response."<br>");
                        $this->ZACL1->isAllowed("user","mypage.php","show")? "yes": "no";
                        echo("is User allowed to access mypage.php? ".$response."<br>");
                        $response=$this->ZACL1->isAllowed("user","Action1","execute")? "yes": "no";
                        echo("is User allowed to access Action1? ".$response."<br>");
               }

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
