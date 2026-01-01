<?php

set_include_path(get_include_path() . PATH_SEPARATOR . './PEAR/');
require_once 'Testing/Selenium.php';
require_once 'PHPUnit/Framework/TestCase.php';

class raid_249482_groupbox extends PHPUnit_Framework_TestCase
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
        $this->selenium->open("/raid_249482_groupbox.php?restore_session=1&DBGSESSID=-1");
        $this->selenium->waitForPageToLoad(10000);
        
        //Groupbox.left=96 + Edit1.left = 30 = 126
    		$this->assertEquals(126,$this->selenium->getElementPositionLeft('Edit1'));
    		
        //Groupbox.left=48 + Edit1.top = 28 = 76
    		$this->assertEquals(76,$this->selenium->getElementPositionTop('Edit1'));    		
    }

}
?>