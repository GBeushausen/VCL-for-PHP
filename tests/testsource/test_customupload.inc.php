<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_focuscontrol.inc.php";
        use_unit("controls.inc.php");
        use_unit("stdctrls.inc.php");
        use_unit("classes.inc.php");

        class CustomUploadTest extends FocusControlTest
        {

                function setup()
                {
                        parent::setup();
                        $this->aowner=$this->stowner;
                        $this->object=new CustomUpload();
                        $this->object->name="myobject";
                }

                function test_init()
                {
                        $this->aowner->insertComponent($this->object);

                        $this->assertEquals($this->aowner->executed,false,'Init test 1');

                        $_FILES['myobject']['tmp_name']='test.file';
                        $_POST['myobjectSubmitEvent']='myobject_DummyEvent';
                        $this->aowner->executed=false;
                        $this->object->OnSubmit="DummyEvent";
                        $this->object->init();
                        $this->assertEquals($this->aowner->executed,true,'Init test 2');
                        $this->aowner->removeComponent($this->object);

                }

                function test_ControlStyle($styles=array())
                {
                        $styles["csRenderOwner"]=1;
                        $styles["csRenderAlso"]="StyleSheet";
                        parent::test_ControlStyle($styles);
                }

                function test_BorderStyle()
                {
                        $this->assertEquals($this->object->BorderStyle,(string)$this->object->defaultBorderStyle(),"a");
                        $this->object->BorderStyle="bsNone";
                        $this->assertEquals($this->object->BorderStyle,"bsNone","b");
                }
                 function test_MaxLength()
                {
                        $this->assertEquals($this->object->MaxLength,0);
                        $this->assertEquals($this->object->defaultMaxLength(),$this->object->MaxLength);
                        $this->object->MaxLength=20;
                        $this->assertEquals($this->object->MaxLength,20);
                        $this->object->MaxLength=20000;
                        $this->assertEquals($this->object->MaxLength,20000);
                }
                function test_Size()
                {
                        $this->assertEquals($this->object->Size,"","a");
                        $this->object->Size=1;
                        $this->assertEquals($this->object->Size,1);
                }
                function test_ReadOnly()
                {
                        $this->assertEquals($this->object->ReadOnly,0);
                        $this->assertEquals($this->object->defaultReadOnly(),$this->object->ReadOnly);
                        $this->object->ReadOnly=1;
                        $this->assertEquals($this->object->ReadOnly,1);
                }
               function test_jsOnSelect()
               {
                        $this->assertEquals($this->object->jsOnSelect,null);
                        $this->assertEquals($this->object->jsOnSelect,$this->object->defaultjsOnSelect());
                        $this->object->jsOnSelect='dummyEvent';
                        $this->assertEquals($this->object->jsOnSelect,'dummyEvent');
               }
               function test_TabOrder()
                {
                        $this->assertEquals($this->object->TabOrder,0);
                        $this->assertEquals($this->object->defaultTabOrder(),$this->object->TabOrder);
                        $this->object->TabOrder=1;
                        $this->assertEquals($this->object->TabOrder,1);
                }
                 function test_TabStop()
                {
                        $this->assertEquals($this->object->TabStop,1);
                        $this->assertEquals($this->object->defaultTabStop(),$this->object->TabStop);
                        $this->object->TabStop=0;
                        $this->assertEquals($this->object->TabStop,0);
                }
                function test_Name()
                {
                        //Create an object just to check if defaultname when creation == object->defaultname()
                        $customupload=new customupload();
                        $this->assertEquals($customupload->Name,$this->object->defaultName());
                        //keep checking name with common method
                        parent::test_Name();
                }
                function test_dumpContents()
                {
                        $c=$this->object->show(true);
                        $c=$this->fixString($c);
                        $compare=trim('<input type="file" value="Up" id="myobject" name="myobject" style=" font-family: Verdana; font-size: 10px;  height:20px;width:260px;"     tabindex="0"   />');
                        $this->assertEquals(trim($c),$compare);
                }
                 function test_Accept()
                {
                        $this->assertEquals($this->object->Accept,"");
                        $a=null;
                        $this->assertEquals($this->object->defaultAccept(),$a);
                        $this->object->Accept=0;
                        $this->assertEquals($this->object->Accept,0);
                }
                function test_moveUploadedFile()
                {
                        $destination=null;
                        $autoExt=false;
                        $a=$this->object->moveUploadedFile($destination,$autoExt);
                        $this->assertEquals($this->object->moveUploadedFile($destination,$autoExt),$a);
                }
                 function test_isText()
                {
                        $this->assertEquals($this->object->isText(), false);
                        $a=$this->object->isText("isXX");
                        $this->assertEquals($this->object->isText("isXX"),$a);
                }
                 function test_isImage()
                {
                        $this->assertEquals($this->object->isImage(), false);
                        $a=$this->object->isImage("isXX");
                        $this->assertEquals($this->object->isImage("isXX"),$a);
                }
                 function test_isVideo()
                {
                        $this->assertEquals($this->object->isVideo(), false);
                        $a=$this->object->isVideo("isXX");
                        $this->assertEquals($this->object->isVideo("isXX"),$a);
                }
                 function test_isApplication()
                {
                        $this->assertEquals($this->object->isApplication(), false);
                        $a=$this->object->isApplication("isXX");
                        $this->assertEquals($this->object->isApplication("isXX"),$a);
                }
                 function test_isGIF()
                {
                        $this->assertEquals($this->object->isGIF(), false);
                        $a=$this->object->isGIF("isXX");
                        $this->assertEquals($this->object->isGIF("isXX"),$a);
                }
                 function test_isJPEG()
                {
                        $this->assertEquals($this->object->isJPEG(), false);
                        $a=$this->object->isJPEG("isXX");
                        $this->assertEquals($this->object->isJPEG("isXX"),$a);
                }
                 function test_isPNG()
                {
                        $this->assertEquals($this->object->isPNG(), false);
                        $a=$this->object->isPNG("isXX");
                        $this->assertEquals($this->object->isPNG("isXX"),$a);
                }
                 function test_readFileExt()
                {
                        $this->assertEquals($this->object->readFileExt(), false);
                        $a=$this->object->readFileExt('jpg');
                        $this->assertEquals($this->object->readFileExt('jpg'),$a);
                }
                 function test_readFileTmpName()
                {
                        $this->assertEquals($this->object->readFileTmpName(), null);
                        $a=$this->object->readFileTmpName('read');
                        $this->assertEquals($this->object->readFileTmpName('read'),$a);
                }
                 function test_readFileName()
                {
                        $this->assertEquals($this->object->readFileName(), null);
                        $a=$this->object->readFileName('read');
                        $this->assertEquals($this->object->readFileName('read'),$a);
                }
                 function test_readFileSize()
                {
                        $this->assertEquals($this->object->readFileSize(), null);
                        $a=$this->object->readFileSize('read');
                        $this->assertEquals($this->object->readFileSize('read'),$a);
                }
                 function test_readFileType()
                {
                        $this->assertEquals($this->object->readFileSubType(), null);
                        $a=$this->object->readFileSubType('read');
                        $this->assertEquals($this->object->readFileSubType('read'),$a);
                }
                 function test_readFileSubType()
                {
                        $this->assertEquals($this->object->readFileSubType(), null);
                        $a=$this->object->readFileSubType('read');
                        $this->assertEquals($this->object->readFileSubType('read'),$a);
                }
                 function test_readGraphicWidth()
                {
                        $this->assertEquals($this->object->readGraphicWidth(), 0,"a");
                        $a=$this->object->readGraphicWidth('read');
                        $this->assertEquals($this->object->readGraphicWidth('read'),$a,"b");
                }
                 function test_readGraphicHeight()
                {
                        $this->assertEquals($this->object->readGraphicHeight(), 0);
                        $a=$this->object->readGraphicHeight('read');
                        $this->assertEquals($this->object->readGraphicHeight('read'),$a);
                }
                function test_readOnSubmit()
                {
                         //tested in test_OnSubmit
                }
                 function test_writeOnSubmit()
                {
                         //tested in test_OnSubmit
                }
                 function test_OnSubmit()
                {
                        $this->assertEquals($this->object->OnSubmit, null);
                        $this->assertEquals($this->object->OnSubmit,$this->object->defaultOnSubmit());
                        $this->object->OnSubmit="Submitevent";
                        $this->assertEquals($this->object->OnSubmit, "Submitevent");
                        $this->test_init();
                }


                function test_isUploadedFile()
                {
                        //can´t be done anything else, because we need to submit a form to have the
                        //file uploaded
                        $this->assertEquals($this->object->isUploadedFile(), false);
                }
                function test_errorMessage()
                {
                        //nothing to test
                }
                function test_getOnUploaded()
                {
                        //tested in OnUploaded
                }
                function test_setOnUploaded()
                {
                        //tested in OnUploaded
                }
                function test_OnUploaded()
                {
                        $this->assertEquals($this->object->OnUploaded, $this->object->defaultOnUploaded());
                        $this->assertEquals($this->object->OnUploaded,$this->object->defaultOnUploaded());
                        $this->object->OnUploaded="Uploadedevent";
                        $this->assertEquals($this->object->OnUploaded, "Uploadedevent");
                }


        }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_customupload.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE SubSubType=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "CustomUploadTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>
