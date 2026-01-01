<?php
        //Includes
        require_once("vcl/vcl.inc.php");
        require_once("masterpage.php");
        use_unit("forms.inc.php");
        use_unit("extctrls.inc.php");
        use_unit("stdctrls.inc.php");

        //Class definition
        class Index extends MasterPage
        {
               public $Label1 = null;
               public $Memo1 = null;
               public $Button1 = null;
               public $pnIndex = null;
               function IndexStartBody($sender, $params)
               {
                //And now is set to false, so we decide when to show the
                //panel contents
                $this->pnIndex->Visible=false;
               }

               function IndexShowHeader($sender, $params)
               {
                //We need to set the panel visible here to allow it
                //add all the header javascript to all the components inside
                $this->pnIndex->Visible=true;
               }

               function Button1Click($sender, $params)
               {
                   $this->Memo1->AddLine("test");
               }

               function pnContentsShow($sender, $params)
               {
                    $this->pnIndex->show();
               }
        }

        global $application;

        global $Index;

        //Creates the form
        $Index=new Index($application);

        //Read from resource file
        $Index->loadResource(__FILE__);

        //Shows the form
        $Index->show();

?>
