<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_graphiccontrol.inc.php";
        use_unit("stdctrls.inc.php");
        use_unit("extctrls.inc.php");

        class FlashObjectTest extends GraphicControlTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new FlashObject();
                        $this->object->Name="myobject";
                }
                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        //print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim('<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" id="myobject" width="105" height="105">
<param name="movie" value="" />
<param name="quality" value="high" />
<embed src="" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="105" height="105"></embed>
</object>');
                        $this->assertEquals(trim($c),$compare);
                }

                function test_getSwfFile()
                {
                        //tested within test_SwfFile
                }
                function test_setSwfFile()
                {
                        //tested within test_SwfFile
                }
                function test_SwfFile()
                {
                        $this->assertEquals($this->object->SwfFile,null);
                        $this->object->SwfFile="/dir1/dir2/myfile.swf";
                        $this->assertEquals($this->object->SwfFile,"/dir1/dir2/myfile.swf");
                }

         }
      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_flashobject.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "FlashObjectTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
