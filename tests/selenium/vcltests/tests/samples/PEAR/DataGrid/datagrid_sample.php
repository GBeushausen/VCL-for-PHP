<?php
        //Includes
        require_once("vcl/vcl.inc.php");
        use_unit("PEAR/peardatagrid.inc.php");
        use_unit("forms.inc.php");
        use_unit("extctrls.inc.php");
        use_unit("stdctrls.inc.php");

        //Class definition
        class Unit293 extends Page
        {
               public $PearDataGrid1 = null;
        }

        global $application;

        global $Unit293;

        //Creates the form
        $Unit293=new Unit293($application);

        //Read from resource file
        $Unit293->loadResource(__FILE__);

        //Shows the form
        $Unit293->show();

?>
