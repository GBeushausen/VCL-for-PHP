<?php
        //Includes
        require_once("vcl/vcl.inc.php");
        use_unit("forms.inc.php");
        use_unit("extctrls.inc.php");
        use_unit("stdctrls.inc.php");

        //Class definition
        class Unit537 extends Page
        {
               public $PaintBox1 = null;
               function PaintBox1Paint($sender, $params)
               {
                $this->PaintBox1->Canvas->Brush->Color="#FF0000";
                $this->PaintBox1->Canvas->Rectangle(0,0,100,100);
                $this->PaintBox1->Canvas->Line(0,0,100,100);                
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