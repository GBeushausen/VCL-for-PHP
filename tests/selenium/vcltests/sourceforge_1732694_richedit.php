<?php

set_include_path(get_include_path() . PATH_SEPARATOR . './PEAR/');
require_once 'Testing/Selenium.php';
require_once 'PHPUnit/Framework/TestCase.php';

class sourceforge_1732694_richedit extends PHPUnit_Framework_TestCase
{
    private $selenium;

    public function setUp()
    {
        //We can select here the browser to use to run the test
        //And selenium works also in Linux and MacOsX
        $this->selenium = new Testing_Selenium("*chrome", "http://localhost:3569");
        $this->selenium->start();
    }

    public function tearDown()
    {
        $this->selenium->stop();
    }

    public function testButton()
    {
        $this->selenium->open("/sourceforge_1732694_richedit.php?restore_session=1&DBGSESSID=-1");
        $this->selenium->waitForPageToLoad(10000);
        $this->selenium->click("Button1");
        $this->selenium->setSpeed(4000);
        $this->selenium->selectFrame("//iframe");
        $abody=$this->selenium->getText("//body");
        $abody=str_replace("\n","<br>",$abody);
        $this->assertEquals("this<br>is<br>html",$abody,'Rich edit must be filled');


        $this->selenium->selectFrame("relative=top");
        $this->selenium->click("Button2");
        $this->selenium->setSpeed(4000);
        $abody=$this->selenium->getText("Label1");
        $abody=str_replace("\n","<br>",$abody);
        $this->assertEquals("this<br>is<br>html",$abody,'Label must be filled');
    }

}
?>