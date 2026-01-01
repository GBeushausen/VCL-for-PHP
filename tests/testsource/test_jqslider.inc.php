<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_control.inc.php";
        use_unit("jquery/jquery.inc.php");



        class JQSliderTest extends ControlTest
        {
                function setup()
                {
                        parent::setup();

                        $this->object=new JQSlider();
                        $this->object->Name="myobject";
                }

                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        //print_r($c);
                        $c=$this->fixString($c);
                        $c=str_replace(array(" ","\r","\n"),"",$c);
                        $compare=str_replace(array(" ","\r","\n"),"",'<div class="myobject_news_slider myobject_top_stories">
        <div class="myobject_messaging">
            Please Note: You may have disabled JavaScript and/or CSS. Although this news content will be accessible, certain functionality is unavailable.
        </div>
        <a href="" class="myobject_skip" title="If you have JavaScript turned off, you will be unable to skip to the news content.">Skip to news content.</a>
        <div class="myobject_prev">

            <a href="#"><img src="/vcl-bin/jquery/images/prev.gif" border="0" width="16" height="16" alt="Previous" title="Previous" env="images" /></a>
        </div>
        <div class="myobject_next">
            <a href="#"><img src="/vcl-bin/jquery/images/next.gif" border="0"  width="16" height="16" alt="Next" title="Next" env="images" /></a>
        </div>
        <div class="myobject_news_items" style=" font-family: Verdana; font-size: 10px;  ">
            <a name=""></a>
            <div class="myobject_container myobject_fl">
                            <div class="myobject_item myobject_fl">

                Edit Lines property to add new items to the slider, each line is an item, you can use HTML
                </div>
                        </div>
        </div>
    </div>');
                        $this->assertEquals(trim($c),$compare);
                }

                function test_dumpHeaderCode()
                {
                        ob_start();
                        $this->object->dumpHeaderCode();
                        $c=ob_get_contents();
                        $c=$this->cleanString($c);
                        ob_clean();
                        $compare="<scriptlanguage=\"javascript\"type=\"text/javascript\"src=\"/vcl-bin/jquery/jquery.js\"></script><scriptlanguage=\"javascript\"type=\"text/javascript\"src=\"/vcl-bin/jquery/jquery.accessible-news-slider.js\"></script><scriptlanguage=\"javascript\"type=\"text/javascript\">$(function(){varoptions={headline:\"Slider\",newsWidth:-15,prefix:\"myobject_\",newsSpeed:\"normal\"}$(\".myobject_top_stories\").slideNews(options);});</script><styletype=\"text/css\"media=\"screen,projection\"/>.myobject_fl{float:left;display:inline;}.myobject_skip{position:absolute;left:-5000px;}.myobject_news_slider{position:relative;width:px;margin:0010px0;}.myobject_news_slider.myobject_messaging{display:block;padding:5px;margin:020px5px20px;background:#ffffcc;}.myobject_news_slider.myobject_prev,.myobject_news_slider.myobject_next{position:absolute;top:42%;display:none;}.myobject_news_slider.myobject_next{right:0;}.myobject_news_slider.myobject_news_items{position:relative;width:-40px;left:20px;overflow:hidden;}.myobject_news_slider.myobject_news_items.myobject_view_all{padding:5px;margin:002px0;border-top:#eeeeed1pxsolid;border-bottom:#eeeeed1pxsolid;text-align:center;}.myobject_news_slider.myobject_news_items.myobject_container{position:relative;top:0;left:0;width:-30px;background:#eeeeed;}.myobject_news_slider.myobject_news_items.myobject_container.myobject_item{width:-45px;margin:010px00;}.myobject_news_slider.myobject_news_items.myobject_container.myobject_itemdiv{width:-45px;margin:10px010px0;}.myobject_news_slider.myobject_news_items.myobject_container.myobject_item{padding:10px;}</style>";
                        $this->assertEquals(trim($c),$compare);
                }

                function test_getLines() {/* Checked in test_Property */}
                function test_setLines(){/* Checked in test_Property */}
                function test_getTopLocation(){/* Checked in test_Property */}
                function test_setTopLocation(){/* Checked in test_Property */}

                function test_Lines()
                {
                        $this->assertEquals($this->object->Lines,array());
                        $this->assertEquals($this->object->Lines,$this->object->defaultLines());
                        $lines=array("line1","line2");
                        $this->object->Lines=$lines;
                        $this->assertEquals($this->object->Lines,$lines);
                }

                function test_TopCaption()
                {
                        $this->assertEquals($this->object->TopCaption,"Slider");
                        $this->assertEquals($this->object->TopCaption,$this->object->defaultTopCaption());
                        $this->object->TopCaption="mycaption";
                        $this->assertEquals($this->object->TopCaption, "mycaption");
                }

                function test_getParentFont(){}
                function test_setParentFont(){}

        }
      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_jqslider.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "JQSliderTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
