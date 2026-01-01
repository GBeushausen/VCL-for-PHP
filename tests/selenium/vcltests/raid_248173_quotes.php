<?php

set_include_path(get_include_path() . PATH_SEPARATOR . './PEAR/');
require_once 'Testing/Selenium.php';
require_once 'PHPUnit/Framework/TestCase.php';

class raid_248173_quotes extends PHPUnit_Framework_TestCase
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
        $this->selenium->open("/raid_248173_quotes.php?restore_session=1&DBGSESSID=-1");
        $this->selenium->waitForPageToLoad(10000);

        $this->assertEquals("That's \"Peter\"",$this->selenium->getText("Label1"),"Error getting Label text");
        $this->assertEquals("That's \"Peter\"",$this->selenium->getValue("Button1"),"Error getting Button text");
        $this->assertEquals("That's \"Peter\"",$this->selenium->getValue("Edit1"),"Error getting Edit text");
        $this->assertEquals("That's \"Peter\"",$this->selenium->getText("//div[@id='LabeledEdit1']/div/div/div"),"Error getting Label text from LabeledEdit");
        $this->assertEquals("That's \"Peter\"",$this->selenium->getValue("//div[@id='LabeledEdit1']/div/input"),"Error getting Edit text from LabeledEdit");
        $this->assertEquals("That's \"Peter\"",$this->selenium->getText("//div[@id='SpeedButton1']/div/div"),"Error getting SpeedButton text");
        $this->assertEquals("That's \"Peter\"",$this->selenium->getText("//div[@id='BitBtn1']/div/div"),"Error getting BitBtn text");

        $this->selenium->click("Button1");
        $this->selenium->waitForPageToLoad(10000);

        $this->assertEquals("That's \"Peter\"",$this->selenium->getText("Label1"),"Error getting Label text 2");
        $this->assertEquals("That's \"Peter\"",$this->selenium->getValue("Button1"),"Error getting Button text 2");
        $this->assertEquals("That's \"Peter\"",$this->selenium->getValue("Edit1"),"Error getting Edit text 2");
        $this->assertEquals("That's \"Peter\"",$this->selenium->getText("//div[@id='LabeledEdit1']/div/div/div"),"Error getting Label text from LabeledEdit 2");
        $this->assertEquals("That's \"Peter\"",$this->selenium->getValue("//div[@id='LabeledEdit1']/div/input"),"Error getting Edit text from LabeledEdit 2");
        $this->assertEquals("That's \"Peter\"",$this->selenium->getText("//div[@id='SpeedButton1']/div/div"),"Error getting SpeedButton text 2");
        $this->assertEquals("That's \"Peter\"",$this->selenium->getText("//div[@id='BitBtn1']/div/div"),"Error getting BitBtn text 2");

    }

}
?>