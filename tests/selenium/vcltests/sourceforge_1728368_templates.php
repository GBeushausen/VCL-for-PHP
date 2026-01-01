<?php

set_include_path(get_include_path() . PATH_SEPARATOR . './PEAR/');
require_once 'Testing/Selenium.php';
require_once 'PHPUnit/Framework/TestCase.php';

class sourceforge_1728368_templates extends PHPUnit_Framework_TestCase
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
        $this->selenium->open("/sourceforge_1728368_templates_inherited.php?restore_session=1&DBGSESSID=-1");
        $this->selenium->waitForPageToLoad(10000);
        $this->assertEquals(true,$this->selenium->isTextPresent('Templatesample using form inheritance'),'Text from template must exist!!');
        $this->assertEquals(true,$this->selenium->isTextPresent('This content is placed on the master page'),'Text from master page must exist!!');
        $this->assertEquals("This memo is placed on the Inherited Page and the template is aware of it",$this->selenium->getText('Memo1'),'Memo1 must be present and contain the right text');



    }

}
?>