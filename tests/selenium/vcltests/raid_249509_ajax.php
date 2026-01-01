<?php

set_include_path(get_include_path() . PATH_SEPARATOR . './PEAR/');
require_once 'Testing/Selenium.php';
require_once 'PHPUnit/Framework/TestCase.php';

class raid_249509_ajax extends PHPUnit_Framework_TestCase
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
        $this->selenium->open("/samples/Ajax/Basic/basicajax.php?restore_session=1&DBGSESSID=-1");
        $this->selenium->waitForPageToLoad(10000);
        $this->selenium->click("Button1");
        $this->selenium->waitForCondition('var allText = selenium.getText("Label1"); var expectedText = "Hello from Ajax!!"; allText.indexOf(expectedText) == 0;',5000);
        $this->assertRegexp("/clicked Ajax/", $this->selenium->getValue("Button1"),'Button caption must be modified');        
    }

}
?>