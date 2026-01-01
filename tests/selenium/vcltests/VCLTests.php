<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'TestSuite::main');
}

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';


require_once 'raid_252410_session.php';
require_once 'raid_251160_inheritance.php';
require_once 'raid_250665_paintbox.php';
require_once 'raid_249509_ajax.php';
require_once 'raid_249502_listview.php';
require_once 'raid_249500_datetimepicker.php';
require_once 'raid_249493_layers.php';
require_once 'raid_249491_treeview.php';
require_once 'raid_249482_groupbox.php';
require_once 'raid_248487_image.php';
require_once 'raid_248439_management.php';
require_once 'raid_248277_updown.php';
require_once 'raid_248173_quotes.php';
require_once 'sourceforge_1728364_templates.php';
require_once 'sourceforge_1728368_templates.php';
require_once 'sourceforge_1732694_richedit.php';
require_once 'sourceforge_1739784_dynamic.php';


class VCLTestSuite
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHPUnit Framework');

        $suite->addTestSuite('raid_252410_session');
        $suite->addTestSuite('raid_251160_inheritance');
        $suite->addTestSuite('raid_250665_paintbox');
        $suite->addTestSuite('raid_249509_ajax');
        $suite->addTestSuite('raid_249502_listview');
        $suite->addTestSuite('raid_249500_datetimepicker');
        $suite->addTestSuite('raid_249493_layers');
        $suite->addTestSuite('raid_249491_treeview');
        $suite->addTestSuite('raid_249482_groupbox');
        $suite->addTestSuite('raid_248487_image');
        $suite->addTestSuite('raid_248439_management');
        $suite->addTestSuite('raid_248277_updown');
        $suite->addTestSuite('raid_248173_quotes');
        $suite->addTestSuite('sourceforge_1728364_templates');
        $suite->addTestSuite('sourceforge_1728368_templates');   
        $suite->addTestSuite('sourceforge_1732694_richedit');
        $suite->addTestSuite('sourceforge_1739784_dynamic');

        return $suite;
    }
}


VCLTestSuite::main();

if (PHPUnit_MAIN_METHOD == 'Framework_AllTests::main') {
    Framework_AllTests::main();
}
?>