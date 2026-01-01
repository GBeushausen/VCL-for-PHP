<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_customupload.inc.php";
        use_unit("controls.inc.php");
        use_unit("stdctrls.inc.php");
        use_unit("classes.inc.php");

        class UploadTest extends CustomUploadTest
        {

                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;
                        $this->object=new Upload();
                        $this->object->name="myobject";
                }

                function test_getParentFont(){}
                function test_setParentFont(){}
                function test_getPopupMenu() {}
                function test_setPopupMenu() {}
  
       }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_upload.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "UploadTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>
