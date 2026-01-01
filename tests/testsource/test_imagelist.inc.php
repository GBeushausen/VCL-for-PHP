<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_component.inc.php";

        class ImageListTest extends ComponentTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new ImageList();
                        $this->object->name="myobject";
                }

                function test_readImage()
                {
                        $this->object->Images=array("c:\mypath\image1","c:\mypath\image2");
                        $this->assertEquals($this->object->readImage(1),"c:\mypath\image2");
                }



                function test_readImageByID()
                {
                        $this->object->Images=array("c:\mypath\image1","c:\mypath\image2");
                        $this->assertEquals($this->object->readImageByID(0,false),"c:\mypath\image1","readImageByID test 1");
                        $c=$this->object->readImageByID(0,true);
                        $this->assertEquals($this->object->readImageByID(0,true),"\"c:\mypath\image1\"","readImageByID test 2");

                }

                function test_getImages()
                {
                        //Check within test_Images();
                }

                function test_setImages()
                {
                        //Check within test_Images();
                }

                function test_Images()
                {
                        $this->assertEquals($this->object->Images,array(),"Images test 1");
                        $this->assertEquals($this->object->defaultImages(),$this->object->Images,"Images test 2");
                        $images=array("c:\mypath\image1","c:\mypath\image2");
                        $this->object->Images=$images;
                        $this->assertEquals($images,$this->object->Images,"Images test 3");
                }


        }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_imagelist.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "ImageListTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>
