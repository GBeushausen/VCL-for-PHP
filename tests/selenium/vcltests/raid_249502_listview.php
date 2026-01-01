<?php

set_include_path(get_include_path() . PATH_SEPARATOR . './PEAR/');
require_once 'Testing/Selenium.php';
require_once 'PHPUnit/Framework/TestCase.php';

class raid_249502_listview extends PHPUnit_Framework_TestCase
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
        $this->selenium->open("/samples/ListView/listview.php?restore_session=1&DBGSESSID=-1");
        $this->selenium->waitForPageToLoad(10000);
        $this->assertEquals("", $this->selenium->getText("ListBox1"),'Listbox must be empty at start');        
        $this->selenium->click("Button1");
        $this->selenium->waitForPageToLoad(10000);        
        $this->assertEquals("Fifth item", $this->selenium->getText("ListBox1"),'ListView must have some selected items');
    }

}
?>