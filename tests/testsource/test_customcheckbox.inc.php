<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_buttoncontrol.inc.php";
        use_unit("stdctrls.inc.php");


        class CustomCheckboxTest extends ButtonControlTest
        {
                function setup()
                {
                        parent::setup();
 
                        $this->aowner=$this->stowner;
                        $this->object=new CustomCheckbox();
                        $this->object->Name="myobject";
                }

                function test_preinit()
                {
                        $_SERVER['REQUEST_METHOD']='POST';
                        $_POST['myobject']=1;
                        $this->object->Caption="1";
                        $this->object->preinit();
                        $this->assertEquals($this->object->Checked, 1,"Check Test 1");
                        $this->object->Caption="0";
                        $this->object->preinit();
                        $this->assertEquals($this->object->Checked, 0,"Check Test 2");
                }


                function test_dumpContents()
                 {
                        $this->test_dumpContentsButtonControl();
                 }

                function test_dumpContentsButtonControl()
                {
                        $this->object->Style="padding:left";
                        $this->object->Color="#FFFFFF";
                        $c=$this->object->show(true);
                        //print_r($c);
                        $c=$this->fixString($c);
                        $compare=trim('<table cellpadding="0" cellspacing="0" id="myobject_table" style="height:20px;width:121px;" class="padding:left"><tr><td width="20">
<input type="checkbox" id="myobject" name="myobject" value=""     tabindex="0"   class="padding:left" />
</td><td >
<span></span>
</td></tr></table>');
                        $this->assertEquals(trim($c),$compare);

                        $this->object->Style="";
                        $this->object->Color="";
                }
         }

      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_customcheckbox.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "CustomCheckboxTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
