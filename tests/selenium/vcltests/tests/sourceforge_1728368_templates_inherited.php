<?php
        //Includes
        require_once("vcl/vcl.inc.php");
        use_unit("buttons.inc.php");
        use_unit("comctrls.inc.php");
        use_unit("menus.inc.php");
        use_unit("forms.inc.php");
        use_unit("extctrls.inc.php");
        use_unit("stdctrls.inc.php");
        require_once("sourceforge_1728368_templates.php");

        //Class definition
        class InheritedTemplate extends Unit537
        {
               public $Memo1 = null;

        }

        global $application;

        global $InheritedTemplate;

        //Creates the form
        $InheritedTemplate=new InheritedTemplate($application);

        //Read from resource file
        $InheritedTemplate->loadResource(__FILE__);

        //Shows the form
        $InheritedTemplate->show();

?>