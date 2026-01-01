<?php

        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_custompopupmenu.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");


        class PopupMenuTest extends CustomPopupMenuTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new PopupMenu();
                        $this->object->Name="myobject";
                }

                function test_getImages()
                {
                        //tested within test_Images();
                }
                function test_setImages()
                {
                        //tested within test_Images();
                }

                function test_getItems()
                {
                        //tested within test_Items();
                }
                function test_setItems()
                {
                        //tested within test_Items();
                }
                function test_getOnClick()
                {
                        //tested within test_Onclick();
                }
                function test_setOnClick()
                {
                        //tested within test_Onclick();
                }
                function test_getjsOnClick()
                {
                        //tested within test_jsOnclick();
                }
                function test_setjsOnClick()
                {
                        //tested within test_jsOnclick();
                }

                function test_Images()
                {
                        //at start Images must be null
                        $this->assertEquals($this->object->Images,null);

                        //get/set check
                        $ImageList=new ImageList();
                        $ImageList->Images=array(0=>"image1", 1=>"image2", 2=>"image3");
                        $PopMenu1=new PopupMenu();
                        $PopMenu2=new PopupMenu();
                        $PopMenu1->Images=$ImageList;
                        $PopMenu2->Images=$ImageList;
                        $this->assertEquals($PopMenu1->Images,$PopMenu2->Images,"a");
                        $this->assertEquals($ImageList,$PopMenu1->Images,"b");

                }

                function test_OnClick()
                {
                        $this->assertEquals($this->object->OnClick,null);
                        $this->object->OnClick="OnClickHandler";
                        $this->assertEquals($this->object->OnClick,"OnClickHandler");
                }

                function test_OnjsClick()
                {
                        $this->assertEquals($this->object->jsOnClick,null);
                        $this->object->jsOnClick="jsOnClickHandler";
                        $this->assertEquals($this->object->jsOnClick,"jsOnClickHandler");
                }
        }

        if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
           else $script=$_GET['script'];

        if (basename($script)=='test_popupmenu.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "PopupMenuTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }

?>
