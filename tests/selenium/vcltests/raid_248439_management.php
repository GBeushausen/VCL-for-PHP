<?php

set_include_path(get_include_path() . PATH_SEPARATOR . './PEAR/');
require_once 'Testing/Selenium.php';
require_once 'PHPUnit/Framework/TestCase.php';

class raid_248439_management extends PHPUnit_Framework_TestCase
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
        $this->selenium->open("/samples/SimpleManagement/simplemanagement.php?restore_session=1&DBGSESSID=-1");
        $this->selenium->waitForPageToLoad(10000);       
        
        $this->selenium->click("link=DVD-FDBL");
        $this->selenium->waitForPageToLoad(10000);                       
        $this->assertEquals("DVD-FDBL",$this->selenium->getValue('Edit2'),"After clicking, selected record must be the last one");
        
        $this->selenium->click("link=MG200MMS");
        $this->selenium->waitForPageToLoad(10000);                       
        $this->assertEquals("MG200MMS",$this->selenium->getValue('Edit2'),"After clicking, selected record must be the first one");        
    }

}
?>