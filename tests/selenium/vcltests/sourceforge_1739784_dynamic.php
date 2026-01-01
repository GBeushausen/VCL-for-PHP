<?php

set_include_path(get_include_path() . PATH_SEPARATOR . './PEAR/');
require_once 'Testing/Selenium.php';
require_once 'PHPUnit/Framework/TestCase.php';

class sourceforge_1739784_dynamic extends PHPUnit_Framework_TestCase
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
        $this->selenium->open("/sourceforge_1739784_dynamic.php?restore_session=1&DBGSESSID=-1");
        $this->selenium->waitForPageToLoad(10000);
        $this->selenium->click("Button1");
        $this->selenium->waitForPageToLoad(10000);
        $this->assertEquals(true,$this->selenium->isElementPresent('myButton'),'Button must have been created');

        $this->selenium->click("myButton");
        $this->selenium->waitForPageToLoad(10000);
        $this->assertEquals('Clicked!!',$this->selenium->getText('Label1'),'Clicking dynamic created button must change label text');
    }

}
?>