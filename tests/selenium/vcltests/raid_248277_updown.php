<?php

set_include_path(get_include_path() . PATH_SEPARATOR . './PEAR/');
require_once 'Testing/Selenium.php';
require_once 'PHPUnit/Framework/TestCase.php';

class raid_248277_updown extends PHPUnit_Framework_TestCase
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
        $this->selenium->open("/raid_248277_updown.php?restore_session=1&DBGSESSID=-1");
        $this->selenium->waitForPageToLoad(10000);

        $this->selenium->click("Button1");
        $this->selenium->waitForPageToLoad(10000);
        $this->assertEquals("0",$this->selenium->getText('Label1'),"Initial value must be 0");

        $this->selenium->type("//div[@id='UpDown1']/div/div/input",2);
        $this->selenium->type("UpDown1_state",2);

        $this->selenium->click("Button1");
        $this->selenium->waitForPageToLoad(10000);
        $this->assertEquals("2",$this->selenium->getText('Label1'),"After modify, value must be 2");
    }

}
?>