<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_customlistbox.inc.php";
        use_unit("controls.inc.php");
        use_unit("stdctrls.inc.php");
        use_unit("classes.inc.php");

        class ComboBoxTest extends CustomListBoxTest
        {

                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;

                        $this->object=new ComboBox();
                        $this->object->name="myobject";
                }

                //No multiselec for comobox we must rewrite this
                function test_preinit()
                {
                        $_POST['myobject']=array("1");
                        $this->object->AddItem("item1");
                        $this->object->AddItem("item2");
                        $this->object->MultiSelect=0;
                        $this->object->preinit();
                        $this->assertEquals($this->object->readSelected(0), true,"Check Test 4");

                }

                function test_dumpContents()
                {
                        $this->setup();
                        $c=$this->object->show(true);
                        $c=$this->cleanString($c);
                        $compare=$this->cleanString('<select name="myobject" id="myobject" size="1" style=" font-family: Verdana; font-size: 10px; height:16px;width:185px;" tabindex="0" ></select>');
                        $this->assertEquals($c,$compare);
                }


                function test_Size()
                {
                        $this->assertEquals($this->object->Size,1);
                        $this->object->Size=4;
                        $this->assertEquals($this->object->Size,4);
                }

                function test_MultiSelect()
                {
                        $this->assertEquals($this->object->MultiSelect,0);
                        $this->assertEquals($this->object->defaultMultiSelect(),$this->object->MultiSelect);
                        $this->object->MultiSelect=1;
                        $this->assertEquals($this->object->MultiSelect,0);
                }

                function test_ClearSelection()
                {
                        $this->setup();
                        $this->test_preinit();
                        $this->assertEquals($this->object->readSelCount(),1,"ClearSelection test 1");
                        $this->object->ClearSelection();
                        $this->assertEquals($this->object->readSelCount(),0,"ClearSelection test 2");
                }

                function test_getParentFont(){}
                function test_setParentFont(){}
                function test_getPopupMenu(){}
                function test_setPopupMenu(){}

       }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_combobox.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "ComboBoxTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>
