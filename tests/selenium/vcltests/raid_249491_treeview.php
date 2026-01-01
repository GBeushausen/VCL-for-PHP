<?php

set_include_path(get_include_path() . PATH_SEPARATOR . './PEAR/');
require_once 'Testing/Selenium.php';
require_once 'PHPUnit/Framework/TestCase.php';

class raid_249491_treeview extends PHPUnit_Framework_TestCase
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
        $this->selenium->open("/raid_249491_treeview.php?restore_session=1&DBGSESSID=-1");
        $this->selenium->waitForPageToLoad(10000);
        $this->selenium->click("Button1");        
        $this->selenium->waitForPageToLoad(15000);        
        $text=$this->selenium->getText("//div[@id='TreeView1']/div/div/div[2]/div/div[3]");                
        $this->assertEquals("testnode0",$text, "Node must exist");
        
        $this->selenium->click("Button1");        
        $this->selenium->waitForPageToLoad(15000);        
        $text=$this->selenium->getText("//div[@id='TreeView1']/div/div/div[2]/div[2]/div[3]");                
        $this->assertEquals("testnode1",$text, "Node must exist");        
        
        $this->selenium->click("Button1");        
        $this->selenium->waitForPageToLoad(15000);        
        $text=$this->selenium->getText("//div[@id='TreeView1']/div/div/div[2]/div[3]/div[3]");                
        $this->assertEquals("testnode2",$text, "Node must exist");                
    }

}
?>