<?php

set_include_path(get_include_path() . PATH_SEPARATOR . './PEAR/');
require_once 'Testing/Selenium.php';
require_once 'PHPUnit/Framework/TestCase.php';

class raid_252410_session extends PHPUnit_Framework_TestCase
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
        $this->selenium->open("/raid_252410_session.php?restore_session=1&DBGSESSID=-1");
        $this->selenium->waitForPageToLoad(10000);
        //Initial condition, label1 must contain ABC and Label2 must contain Label2
        $this->assertEquals("ABC", $this->selenium->getText("Label1"),'Label1 must be "ABC" when showing');
        $this->assertEquals("Label2", $this->selenium->getText("Label2"),'Label2 must be "Label2" when showing');
        $this->selenium->click("Button1");
        $this->selenium->waitForPageToLoad(10000);

        //After clicking on the button, both labels must have the same content
        $this->assertEquals("ABC", $this->selenium->getText("Label1"),'Label1 must be "ABC" after clicking button once');
        $this->assertEquals("ABC", $this->selenium->getText("Label2"),'Label2 must be "ABC" after clicking button once');

        //Click again to ensure session is working ok
        $this->selenium->click("Button1");
        $this->selenium->waitForPageToLoad(10000);
        $this->assertEquals("ABC", $this->selenium->getText("Label1"),'Label1 must be "ABC" after clicking button twice');
        $this->assertEquals("ABC", $this->selenium->getText("Label2"),'Label2 must be "ABC" after clicking button twice');
    }

}
?>