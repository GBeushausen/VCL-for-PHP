<?php

set_include_path(get_include_path() . PATH_SEPARATOR . './PEAR/');
require_once 'Testing/Selenium.php';
require_once 'PHPUnit/Framework/TestCase.php';

class raid_249500_datetimepicker extends PHPUnit_Framework_TestCase
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
        $this->selenium->open("/samples/DateTimePicker/index.php?restore_session=1&DBGSESSID=-1");
        $this->selenium->waitForPageToLoad(10000);
        $this->selenium->click("f-calendar-trigger-1");               
        $this->assertEquals("block",$this->selenium->getEval("var obj=selenium.page().findElement(\"xpath=//div[@class='calendar']\"); obj.style.display;"),"Calendar 1");               
        $this->selenium->refresh();
        
        $this->selenium->waitForPageToLoad(10000);
        $this->selenium->click("f-calendar-trigger-2");               
        $this->assertEquals("block",$this->selenium->getEval("var obj=selenium.page().findElement(\"xpath=//div[@class='calendar']\"); obj.style.display;"),"Calendar 2");               
        $this->selenium->refresh();        
        
        $this->selenium->waitForPageToLoad(10000);
        $this->selenium->click("f-calendar-trigger-3");               
        $this->assertEquals("block",$this->selenium->getEval("var obj=selenium.page().findElement(\"xpath=//div[@class='calendar']\"); obj.style.display;"),"Calendar 3");               
        $this->selenium->refresh();             
    }

}
?>