<?php
        //Includes
        require_once("vcl/vcl.inc.php");
        use_unit("forms.inc.php");
        use_unit("extctrls.inc.php");
        use_unit("stdctrls.inc.php");

        //Class definition
        class Unit537 extends Page
        {
               public $Label1 = null;
               public $Button2 = null;
               public $Button1 = null;

               public $myButton = null;
               function DynamicEvent($sender, $params)
               {
                        $this->Label1->Caption="Clicked!!";
               }

               function Button1Click($sender, $params)
               {
                        $this->myButton=new Button($this);
                        $this->myButton->Parent=$this;
                        $this->myButton->Left=100;
                        $this->myButton->Top=100;
                        $this->myButton->Name="myButton";
                        $this->myButton->OnClick="DynamicEvent";

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