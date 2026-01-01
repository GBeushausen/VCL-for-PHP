<?php
        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_component.inc.php";
        use_unit("controls.inc.php");
        use_unit("extctrls.inc.php");


        class ControlTest extends ComponentTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new Control();
                        $this->object->Name="myobject";
                }

                function test_Enabled()
                {
                        $this->assertEquals($this->object->Enabled,1);
                        $this->assertEquals($this->object->defaultEnabled(),$this->object->Enabled);
                        $this->object->Enabled=0;
                        $this->assertEquals($this->object->Enabled,0);
                }

                function test_Autosize()
                {
                        $this->assertEquals($this->object->Autosize,0);
                        $this->assertEquals($this->object->defaultAutosize(),$this->object->Autosize);
                        $this->object->Autosize=0;
                        $this->assertEquals($this->object->Autosize,0);
                }

                function test_canShow()
                {
                        $this->assertEquals($this->object->canShow(),1);

                        $parent=new Panel();
                        $parent->Visible=false;
                        $this->object->Parent=$parent;

                        $this->assertEquals($this->object->canShow(),false);
                }

        function test_readJSWrapperHiddenFieldName()
        {
                $this->assertEquals("{$this->object->Name}SubmitEvent", $this->object->readJSWrapperHiddenFieldName());
        }

        function test_readJSWrapperSubmitEventValue()
        {
                $event="testevent";
                $this->assertEquals("{$this->object->Name}_$event", $this->object->readJSWrapperSubmitEventValue($event));
        }

              function test_ParentColor()
                {
                        //Initially set
                        $this->assertEquals($this->object->ParentColor, 1);
                        //$this->assertEquals($this->object->ParentColor,$this->object->defaultParentColor());

                          //Setting the parent of the object, the Color must be the parent's
                          $parent=new Panel();
                          $this->object->Parent=$parent;
                          //Modifying the color, must modify ParentColor property
                          $this->object->Color="#800000";
                          $this->assertEquals($this->object->ParentColor,0);

                          $this->object->ParentColor=1;
                           $this->object->ParentColor=1;
                          $this->assertEquals($this->object->Color,$parent->Color);
                          $this->assertEquals($this->object->Color, '');

                          $parent->Color="#FF00FF";
                          $this->assertEquals($this->object->Color,'#FF00FF');
                }



                function test_readJsEvents()
                {
                        $this->assertEquals($this->object->readJsEvents(),'');
                        $this->object->jsOnActivate='dummy';
                        $this->assertEquals(trim($this->object->readJsEvents()),'onactivate="return dummy(event)"');
                }

                function test_dumpJsEvents()
                {
                        ob_start();
                        $this->stowner->insertComponent($this->object);
                        $this->object->jsOnActivate='DummyEvent';
                        $this->object->dumpJSEvents();
                        $c=ob_get_contents();
                        ob_end_clean();
                        $c=str_replace("\n","",$c);
                        $this->stowner->removeComponent($this->object);
                        $this->assertEquals(trim($c),'function DummyEvent(event){var event = event || window.event;var params=null;}','Testing if dumpJsEvents generate correct code');
                }

                function test_dumpJavascript()
                {
                        $this->setup();
                        ob_start();
                        $this->stowner->insertComponent($this->object);
                        $this->object->jsOnClick='AnotherEvent';
                        $this->object->dumpJavascript();
                        $c=ob_get_contents();
                        //print_r($c);
                        ob_end_clean();
                        $c=str_replace("\n","",$c);
                        $this->stowner->removeComponent($this->object);
                        $this->assertEquals(trim($c),'function AnotherEvent(event){var event = event || window.event;var params=null;}','Testing if dump javascript generate the right code');
                }

                function test_Caption()
                {
                        $this->assertEquals($this->object->Caption,'');
                        $this->assertEquals($this->object->Caption,$this->object->defaultCaption());
                        $this->object->Caption="holaaaa";
                        $this->assertEquals($this->object->Caption,'holaaaa');

                        //TODO: Check this, properties changing other properties on the IDE
                        $this->object->Name="Label1";
                        $this->assertEquals($this->object->Caption,'holaaaa');
                }

                function test_Attributes()
                {
                        $this->assertEquals($this->object->Attributes,array());
                        $this->assertEquals($this->object->Attributes,$this->object->defaultAttributes());
                        $this->object->Attributes=array("size"=>'10');
                        $this->assertEquals($this->object->Attributes,array('size'=>'10'));
                }

                function test_Hidden()
                {
                        $this->assertEquals($this->object->Hidden,0);
                        $this->assertEquals($this->object->Hidden,$this->object->defaultHidden());
                        $this->object->Hidden=true;
                        $this->assertEquals($this->object->Hidden,true);
                }

                function test_Color()
                {
                        $this->assertEquals($this->object->Color,'');
                        $this->assertEquals($this->object->defaultColor(),$this->object->Color);
                        $this->object->Color="#FF00FF";
                        $this->assertEquals($this->object->Color,'#FF00FF');
                }

                function test_Font()
                {
                        $this->assertEquals(is_object($this->object->Font),true);
                        $font_string=$this->object->Font->FontString;
                        $this->assertEquals(trim($font_string),trim('font-family: Verdana; font-size: 10px;'));
                }

                function test_ParentFont()
                {
                        //Initially set
                        $this->assertEquals($this->object->ParentFont,1);
                        $this->assertEquals($this->object->ParentFont,$this->object->defaultParentFont());

                      //Modifying the font, must modify ParentFont
                        $font=$this->object->Font;
                        $font->Color="#FF0000";
                        $this->assertEquals($this->object->ParentFont,0);

                        //Now the font string must be modified
                        $font_string=$this->object->Font->FontString;
                        $this->assertEquals(trim($font_string),trim('font-family: Verdana; font-size: 10px; color: #FF0000;'),'Font must be the modified one, including color');
                        //Setting parentFont must work
                        $this->object->ParentFont=1;

                        $this->assertEquals($this->object->ParentFont,1);
                        //Setting the parent of the object, the fontstring must be the parent's
                        $parent=new Panel();
                        $parent->Name="testParent";
                        $this->object->Parent=$parent;
                        $font_string=$this->object->Font->FontString;
                        $this->assertEquals(trim($font_string),trim('font-family: Verdana; font-size: 10px;'),'Font must match with the parent font');

                        $font=$parent->Font;
                        $font->Color="#FF00FF";
                        $font_string=$this->object->Font->FontString;
                        $this->assertEquals(trim($font_string),trim('font-family: Verdana; font-size: 10px; color: #FF00FF;'),'Font must match with the modified parent font');
                }

                function test_IsLayer()
                {
                        $this->assertEquals($this->object->IsLayer,0);
                        $this->assertEquals($this->object->defaultIsLayer(),$this->object->IsLayer);
                        $this->object->IsLayer=1;
                        $this->assertEquals($this->object->IsLayer,1);
                }

                 function test_Layer()
                {
                        $this->assertEquals($this->object->Layer,0);
                        $this->assertEquals($this->object->defaultLayer(),$this->object->Layer);
                        $this->object->Layer=20;
                        $this->assertEquals($this->object->Layer,20);
                        $this->object->Layer=20000;
                        $this->assertEquals($this->object->Layer,20000);
                }

                function test_Align()
                {
                        //$this->assertEquals($this->object->Align,'alNone');
                        $this->assertEquals($this->object->Align,$this->object->defaultAlign());
                        $this->object->Align=alClient;
                        $this->assertEquals($this->object->Align,'alClient');
                }

                function test_Alignment()
                {
                        $this->assertEquals($this->object->Alignment,'agNone');
                        $this->assertEquals($this->object->Alignment,$this->object->defaultAlignment());
                        $this->object->Alignment=agLeft;
                        $this->assertEquals($this->object->Alignment,'agLeft');
                }

                function test_DesignColor()
                {
                        $this->assertEquals($this->object->DesignColor,'');
                        $this->assertEquals($this->object->defaultDesignColor(),$this->object->DesignColor);
                        $this->object->DesignColor="#FF00FF";
                        $this->assertEquals($this->object->DesignColor,'#FF00FF');
                }

                function test_ShowHint()
                {
                        $this->assertEquals($this->object->ShowHint,0);
                        $this->assertEquals($this->object->defaultShowHint(),$this->object->ShowHint);
                        $this->object->ShowHint=1;
                        $this->assertEquals($this->object->ShowHint,1);
                }

                function test_ParentShowHint()
                {
                        $this->assertEquals($this->object->ParentShowHint,1);
                        $this->assertEquals($this->object->defaultParentShowHint(),$this->object->ParentShowHint);
                        $this->object->ParentShowHint=0;
                        $this->assertEquals($this->object->ParentShowHint,0);

                        $this->object->ParentShowHint=1;
                        $this->object->Hint='That is a hint';
                        $this->object->ShowHint=1;
                        $this->assertEquals($this->object->ParentShowHint,0);
                }

                function test_Visible()
                {
                        $this->assertEquals($this->object->Visible,1);
                        $this->assertEquals($this->object->defaultVisible(),$this->object->Visible);
                        $this->object->Visible=0;
                        $this->assertEquals($this->object->Visible,0);
                }

                function test_Parent()
                {
                        $this->assertEquals($this->object->Parent,null);
                        $parent=new Panel();
                        $this->object->Parent=$parent;
                        $this->assertEquals(($this->object->Parent!=null),true);
                }

                function test_ControlStyle($styles=array())
                {
                        //class Control doesn´t add any controlstyle this way if we execute
                        //test from Control class $this->ControlStyle==array()
                        $this->assertEquals($this->object->ControlStyle,$styles,"a");

                }

                function test_Cursor()
                {
                        $this->assertEquals($this->object->Cursor,'');
                        $this->assertEquals($this->object->defaultCursor(),$this->object->Cursor);
                        $this->object->Cursor=crPointer;
                        $this->assertEquals($this->object->Cursor,'crPointer');
                        $this->object->Cursor=crCrossHair;
                        $this->assertEquals($this->object->Cursor,'crCrossHair');
                        $this->object->Cursor=crText;
                        $this->assertEquals($this->object->Cursor,'crText');
                        $this->object->Cursor=crNResize;
                        $this->assertEquals($this->object->Cursor,'crN-Resize');
                }

                function test_Hint()
                {
                        $this->assertEquals($this->object->Hint,'');
                        $this->assertEquals($this->object->defaultHint(),$this->object->Hint);
                        $this->object->Hint="heeee";
                        $this->assertEquals($this->object->Hint,'heeee');
                }

                function test_show()
                {
                }

                function test_OnShow()
                {
                        $this->assertEquals($this->object->OnShow,null);
                        $this->assertEquals($this->object->defaultOnShow(),$this->object->OnShow);
                        $this->object->OnShow="heeee";
                        $this->assertEquals($this->object->OnShow,'heeee');
                }

                function test_OnBeforeShow()
                {
                        $this->assertEquals($this->object->OnBeforeShow,null);
                        $this->assertEquals($this->object->defaultOnBeforeShow(),$this->object->OnBeforeShow);
                        $this->object->OnBeforeShow="heeee";
                        $this->assertEquals($this->object->OnBeforeShow,'heeee');
                }

                function test_OnAfterShow()
                {
                        $this->assertEquals($this->object->OnAfterShow,null);
                        $this->assertEquals($this->object->defaultOnAfterShow(),$this->object->OnAfterShow);
                        $this->object->OnAfterShow="heeee";
                        $this->assertEquals($this->object->OnAfterShow,'heeee');
                }


                function test_Height()
                {
                        $this->object->Height=100;
                        $this->assertEquals($this->object->Height,100);
                        $this->object->Height=0;
                        $this->assertEquals($this->object->Height,0);
                        $this->assertEquals($this->object->defaultHeight(),$this->object->Height);
                }

                function test_Width()
                {
                        $this->object->Width=100;
                        $this->assertEquals($this->object->Width,100);
                        $this->object->Width=0;
                        $this->assertEquals($this->object->Width,0);
                        $this->assertEquals($this->object->defaultWidth(),$this->object->Width);
                }


                function test_Top()
                {
                        $this->object->Top=100;
                        $this->assertEquals($this->object->Top,100);
                        $this->object->Top=0;
                        $this->assertEquals($this->object->Top,0);
                        $this->assertEquals($this->object->defaultTop(),$this->object->Top);
                }


                function test_Left()
                {
                        $this->object->Left=100;
                        $this->assertEquals($this->object->Left,100);
                        $this->object->Left=0;
                        $this->assertEquals($this->object->Left,0);
                        $this->assertEquals($this->object->defaultLeft(),$this->object->Left);
                }

              //Replaced from ButtonControl to control class.
                 function test_popupmenu()
                {
                        $obj=new Object();
                        $this->assertEquals($this->object->popupmenu,null);
                        $this->assertEquals($this->object->popupmenu,$this->object->defaultpopupmenu());
                        $this->object->popupmenu=$obj;
                        $this->assertEquals($this->object->popupmenu,$obj);
                }

                function test_updateParentProperties()
                {
                        $parent=new Button();
                        $this->object->Parent=$parent;

                        $this->object->ParentColor=1;
                        $this->object->ParentShowHint=1;
                        $this->object->ParentFont=1;

                        //Change some settings
                        $parent->Font->Size=16;
                        $parent->Color='#0FF0F';
                        $parent->ShowHint=1;

                        $this->object->updateParentProperties();

                        $parent_font=$parent->Font->FontString;
                        $parent_color=$parent->Color;
                        $parent_showhint=$parent->ShowHint;

                        //ShowHint, Font and Color of this object has to have
                        //the same values as assigned parent values.
                        $this->assertEquals($this->object->ShowHint, $parent_showhint);
                        $this->assertEquals($this->object->Font->FontString,$parent_font);
                        $this->assertEquals($this->object->Color,$parent_color);

                        $c=new Control();
                        $parent2=new Button();
                        $c->Parent=$parent2;

                        $c->ParentColor=0;
                        $c->ParentShowHint=0;
                        $c->ParentFont=0;

                          //Change some settings
                        $parent2->Font->Size=20;
                        $parent2->Color='#0F55F';
                        $parent2->ShowHint=1;

                        $c->updateParentProperties();

                        $parent_font=$parent2->Font->Size;
                        $parent_color=$parent2->Color;
                        $parent_showhint=$parent2->ShowHint;

                        //must still have old values and not from parent
                        $this->assertEquals($c->ShowHint, $c->defaultShowHint());
                        $this->assertEquals($c->Font->Size, '10px');
                        $this->assertEquals($c->Color,$c->defaultColor());

                }

                function test_Style()
                {
                        $defaultStyle=$this->object->Style;
                        $this->assertEquals($defaultStyle,"");
                        $myStyle="my style css file";
                        $this->object->Style=$myStyle;
                        $this->assertEquals($this->object->Style,$myStyle);
                }

                function test_AdjustToLayout()
                {
                        //php accepts strings "0" and "1" as booleans, but
                        //assertEquals checks first for class parameters so, we must
                        //supply strings and not booleans.
                        $default=$this->object->AdjustToLayout;
                        $this->assertEquals($default,"0");
                        $value="1";
                        $this->object->Style=$value;
                        $this->assertEquals($this->object->Style,"1");
                }

                function test_readStyleClass()
                {
                        $default=$this->object->readStyleClass();
                        $this->assertEquals($default,'');
                }

                function test_updateParentFont()
                {
                        $this->test_updateParentProperties();
                }

                function test_updateParentColor()
                {
                        $this->test_updateParentProperties();
                }

                function test_updateParentShowHint()
                {
                        $this->test_updateParentProperties();
                }


                function test_readDoParentReset()
                {
                        //Check default (this property seems not to be writable)
                        $this->assertEquals($this->object->DoParentReset ,true);
                }

                function test_dumpContents()
                {
                        if($this->object->className()!="Control")
                                $this->assertEquals(true,false);
                }

                function test_dumpChildren()
                {
                        //Do nothing here
                }

function test_jsOnActivate                () { $this->assertEquals($this->object->jsOnActivate                ,null); $this->assertEquals($this->object->defaultjsOnActivate                (),$this->object->jsOnActivate                ); $this->object->jsOnActivate                ="heeee"; $this->assertEquals($this->object->jsOnActivate                ,'heeee'); }
function test_jsOnDeactivate              () { $this->assertEquals($this->object->jsOnDeactivate              ,null); $this->assertEquals($this->object->defaultjsOnDeactivate              (),$this->object->jsOnDeactivate              ); $this->object->jsOnDeactivate              ="heeee"; $this->assertEquals($this->object->jsOnDeactivate              ,'heeee'); }
function test_jsOnBeforeCopy              () { $this->assertEquals($this->object->jsOnBeforeCopy              ,null); $this->assertEquals($this->object->defaultjsOnBeforeCopy              (),$this->object->jsOnBeforeCopy              ); $this->object->jsOnBeforeCopy              ="heeee"; $this->assertEquals($this->object->jsOnBeforeCopy              ,'heeee'); }
function test_jsOnBeforeCut               () { $this->assertEquals($this->object->jsOnBeforeCut               ,null); $this->assertEquals($this->object->defaultjsOnBeforeCut               (),$this->object->jsOnBeforeCut               ); $this->object->jsOnBeforeCut               ="heeee"; $this->assertEquals($this->object->jsOnBeforeCut               ,'heeee'); }
function test_jsOnBeforeDeactivate        () { $this->assertEquals($this->object->jsOnBeforeDeactivate        ,null); $this->assertEquals($this->object->defaultjsOnBeforeDeactivate        (),$this->object->jsOnBeforeDeactivate        ); $this->object->jsOnBeforeDeactivate        ="heeee"; $this->assertEquals($this->object->jsOnBeforeDeactivate        ,'heeee'); }
function test_jsOnBeforeEditfocus         () { $this->assertEquals($this->object->jsOnBeforeEditfocus         ,null); $this->assertEquals($this->object->defaultjsOnBeforeEditfocus         (),$this->object->jsOnBeforeEditfocus         ); $this->object->jsOnBeforeEditfocus         ="heeee"; $this->assertEquals($this->object->jsOnBeforeEditfocus         ,'heeee'); }
function test_jsOnBeforePaste             () { $this->assertEquals($this->object->jsOnBeforePaste             ,null); $this->assertEquals($this->object->defaultjsOnBeforePaste             (),$this->object->jsOnBeforePaste             ); $this->object->jsOnBeforePaste             ="heeee"; $this->assertEquals($this->object->jsOnBeforePaste             ,'heeee'); }
function test_jsOnBlur                    () { $this->assertEquals($this->object->jsOnBlur                    ,null); $this->assertEquals($this->object->defaultjsOnBlur                    (),$this->object->jsOnBlur                    ); $this->object->jsOnBlur                    ="heeee"; $this->assertEquals($this->object->jsOnBlur                    ,'heeee'); }
function test_jsOnChange() { $this->assertEquals($this->object->jsOnChange,null); $this->assertEquals($this->object->defaultjsOnChange(),$this->object->jsOnChange             ); $this->object->jsOnChange             ="heeee"; $this->assertEquals($this->object->jsOnChange             ,'heeee'); }

function test_jsOnClick                   () { $this->assertEquals($this->object->jsOnClick                   ,null); $this->assertEquals($this->object->defaultjsOnClick                   (),$this->object->jsOnClick                   ); $this->object->jsOnClick                   ="heeee"; $this->assertEquals($this->object->jsOnClick                   ,'heeee'); }
function test_jsOnContextMenu             () { $this->assertEquals($this->object->jsOnContextMenu             ,null); $this->assertEquals($this->object->defaultjsOnContextMenu             (),$this->object->jsOnContextMenu             ); $this->object->jsOnContextMenu             ="heeee"; $this->assertEquals($this->object->jsOnContextMenu             ,'heeee'); }
function test_jsOnControlSelect           () { $this->assertEquals($this->object->jsOnControlSelect           ,null); $this->assertEquals($this->object->defaultjsOnControlSelect           (),$this->object->jsOnControlSelect           ); $this->object->jsOnControlSelect           ="heeee"; $this->assertEquals($this->object->jsOnControlSelect           ,'heeee'); }
function test_jsOnCopy                    () { $this->assertEquals($this->object->jsOnCopy                    ,null); $this->assertEquals($this->object->defaultjsOnCopy                    (),$this->object->jsOnCopy                    ); $this->object->jsOnCopy                    ="heeee"; $this->assertEquals($this->object->jsOnCopy                    ,'heeee'); }
function test_jsOnCut                     () { $this->assertEquals($this->object->jsOnCut                     ,null); $this->assertEquals($this->object->defaultjsOnCut                     (),$this->object->jsOnCut                     ); $this->object->jsOnCut                     ="heeee"; $this->assertEquals($this->object->jsOnCut                     ,'heeee'); }
function test_jsOnDblClick                () { $this->assertEquals($this->object->jsOnDblClick                ,null); $this->assertEquals($this->object->defaultjsOnDblClick                (),$this->object->jsOnDblClick                ); $this->object->jsOnDblClick                ="heeee"; $this->assertEquals($this->object->jsOnDblClick                ,'heeee'); }
function test_jsOnDrag                    () { $this->assertEquals($this->object->jsOnDrag                    ,null); $this->assertEquals($this->object->defaultjsOnDrag                    (),$this->object->jsOnDrag                    ); $this->object->jsOnDrag                    ="heeee"; $this->assertEquals($this->object->jsOnDrag                    ,'heeee'); }
function test_jsOnDragEnter               () { $this->assertEquals($this->object->jsOnDragEnter               ,null); $this->assertEquals($this->object->defaultjsOnDragEnter               (),$this->object->jsOnDragEnter               ); $this->object->jsOnDragEnter               ="heeee"; $this->assertEquals($this->object->jsOnDragEnter               ,'heeee'); }
function test_jsOnDragLeave               () { $this->assertEquals($this->object->jsOnDragLeave               ,null); $this->assertEquals($this->object->defaultjsOnDragLeave               (),$this->object->jsOnDragLeave               ); $this->object->jsOnDragLeave               ="heeee"; $this->assertEquals($this->object->jsOnDragLeave               ,'heeee'); }
function test_jsOnDragOver                () { $this->assertEquals($this->object->jsOnDragOver                ,null); $this->assertEquals($this->object->defaultjsOnDragOver                (),$this->object->jsOnDragOver                ); $this->object->jsOnDragOver                ="heeee"; $this->assertEquals($this->object->jsOnDragOver                ,'heeee'); }
function test_jsOnDragStart               () { $this->assertEquals($this->object->jsOnDragStart               ,null); $this->assertEquals($this->object->defaultjsOnDragStart               (),$this->object->jsOnDragStart               ); $this->object->jsOnDragStart               ="heeee"; $this->assertEquals($this->object->jsOnDragStart               ,'heeee'); }
function test_jsOnDrop                    () { $this->assertEquals($this->object->jsOnDrop                    ,null); $this->assertEquals($this->object->defaultjsOnDrop                    (),$this->object->jsOnDrop                    ); $this->object->jsOnDrop                    ="heeee"; $this->assertEquals($this->object->jsOnDrop                    ,'heeee'); }
function test_jsOnFilterChange            () { $this->assertEquals($this->object->jsOnFilterChange            ,null); $this->assertEquals($this->object->defaultjsOnFilterChange            (),$this->object->jsOnFilterChange            ); $this->object->jsOnFilterChange            ="heeee"; $this->assertEquals($this->object->jsOnFilterChange            ,'heeee'); }
function test_jsOnFocus                   () { $this->assertEquals($this->object->jsOnFocus                   ,null); $this->assertEquals($this->object->defaultjsOnFocus                   (),$this->object->jsOnFocus                   ); $this->object->jsOnFocus                   ="heeee"; $this->assertEquals($this->object->jsOnFocus                   ,'heeee'); }
function test_jsOnHelp                    () { $this->assertEquals($this->object->jsOnHelp                    ,null); $this->assertEquals($this->object->defaultjsOnHelp                    (),$this->object->jsOnHelp                    ); $this->object->jsOnHelp                    ="heeee"; $this->assertEquals($this->object->jsOnHelp                    ,'heeee'); }
function test_jsOnKeyDown                 () { $this->assertEquals($this->object->jsOnKeyDown                 ,null); $this->assertEquals($this->object->defaultjsOnKeyDown                 (),$this->object->jsOnKeyDown                 ); $this->object->jsOnKeyDown                 ="heeee"; $this->assertEquals($this->object->jsOnKeyDown                 ,'heeee'); }
function test_jsOnKeyPress                () { $this->assertEquals($this->object->jsOnKeyPress                ,null); $this->assertEquals($this->object->defaultjsOnKeyPress                (),$this->object->jsOnKeyPress                ); $this->object->jsOnKeyPress                ="heeee"; $this->assertEquals($this->object->jsOnKeyPress                ,'heeee'); }
function test_jsOnKeyUp                   () { $this->assertEquals($this->object->jsOnKeyUp                   ,null); $this->assertEquals($this->object->defaultjsOnKeyUp                   (),$this->object->jsOnKeyUp                   ); $this->object->jsOnKeyUp                   ="heeee"; $this->assertEquals($this->object->jsOnKeyUp                   ,'heeee'); }
function test_jsOnLoseCapture             () { $this->assertEquals($this->object->jsOnLoseCapture             ,null); $this->assertEquals($this->object->defaultjsOnLoseCapture             (),$this->object->jsOnLoseCapture             ); $this->object->jsOnLoseCapture             ="heeee"; $this->assertEquals($this->object->jsOnLoseCapture             ,'heeee'); }
function test_jsOnMouseDown               () { $this->assertEquals($this->object->jsOnMouseDown               ,null); $this->assertEquals($this->object->defaultjsOnMouseDown               (),$this->object->jsOnMouseDown               ); $this->object->jsOnMouseDown               ="heeee"; $this->assertEquals($this->object->jsOnMouseDown               ,'heeee'); }
function test_jsOnMouseUp                 () { $this->assertEquals($this->object->jsOnMouseUp                 ,null); $this->assertEquals($this->object->defaultjsOnMouseUp                 (),$this->object->jsOnMouseUp                 ); $this->object->jsOnMouseUp                 ="heeee"; $this->assertEquals($this->object->jsOnMouseUp                 ,'heeee'); }
function test_jsOnMouseEnter              () { $this->assertEquals($this->object->jsOnMouseEnter              ,null); $this->assertEquals($this->object->defaultjsOnMouseEnter              (),$this->object->jsOnMouseEnter              ); $this->object->jsOnMouseEnter              ="heeee"; $this->assertEquals($this->object->jsOnMouseEnter              ,'heeee'); }
function test_jsOnMouseLeave              () { $this->assertEquals($this->object->jsOnMouseLeave              ,null); $this->assertEquals($this->object->defaultjsOnMouseLeave              (),$this->object->jsOnMouseLeave              ); $this->object->jsOnMouseLeave              ="heeee"; $this->assertEquals($this->object->jsOnMouseLeave              ,'heeee'); }
function test_jsOnMouseMove               () { $this->assertEquals($this->object->jsOnMouseMove               ,null); $this->assertEquals($this->object->defaultjsOnMouseMove               (),$this->object->jsOnMouseMove               ); $this->object->jsOnMouseMove               ="heeee"; $this->assertEquals($this->object->jsOnMouseMove               ,'heeee'); }
function test_jsOnMouseOut                () { $this->assertEquals($this->object->jsOnMouseOut                ,null); $this->assertEquals($this->object->defaultjsOnMouseOut                (),$this->object->jsOnMouseOut                ); $this->object->jsOnMouseOut                ="heeee"; $this->assertEquals($this->object->jsOnMouseOut                ,'heeee'); }
function test_jsOnMouseOver               () { $this->assertEquals($this->object->jsOnMouseOver               ,null); $this->assertEquals($this->object->defaultjsOnMouseOver               (),$this->object->jsOnMouseOver               ); $this->object->jsOnMouseOver               ="heeee"; $this->assertEquals($this->object->jsOnMouseOver               ,'heeee'); }
function test_jsOnPaste                   () { $this->assertEquals($this->object->jsOnPaste                   ,null); $this->assertEquals($this->object->defaultjsOnPaste                   (),$this->object->jsOnPaste                   ); $this->object->jsOnPaste                   ="heeee"; $this->assertEquals($this->object->jsOnPaste                   ,'heeee'); }
function test_jsOnPropertyChange          () { $this->assertEquals($this->object->jsOnPropertyChange          ,null); $this->assertEquals($this->object->defaultjsOnPropertyChange          (),$this->object->jsOnPropertyChange          ); $this->object->jsOnPropertyChange          ="heeee"; $this->assertEquals($this->object->jsOnPropertyChange          ,'heeee'); }
function test_jsOnReadyStateChange        () { $this->assertEquals($this->object->jsOnReadyStateChange        ,null); $this->assertEquals($this->object->defaultjsOnReadyStateChange        (),$this->object->jsOnReadyStateChange        ); $this->object->jsOnReadyStateChange        ="heeee"; $this->assertEquals($this->object->jsOnReadyStateChange        ,'heeee'); }
function test_jsOnResize                  () { $this->assertEquals($this->object->jsOnResize                  ,null); $this->assertEquals($this->object->defaultjsOnResize                  (),$this->object->jsOnResize                  ); $this->object->jsOnResize                  ="heeee"; $this->assertEquals($this->object->jsOnResize                  ,'heeee'); }
function test_jsOnResizeEnd               () { $this->assertEquals($this->object->jsOnResizeEnd               ,null); $this->assertEquals($this->object->defaultjsOnResizeEnd               (),$this->object->jsOnResizeEnd               ); $this->object->jsOnResizeEnd               ="heeee"; $this->assertEquals($this->object->jsOnResizeEnd               ,'heeee'); }
function test_jsOnResizeStart             () { $this->assertEquals($this->object->jsOnResizeStart             ,null); $this->assertEquals($this->object->defaultjsOnResizeStart             (),$this->object->jsOnResizeStart             ); $this->object->jsOnResizeStart             ="heeee"; $this->assertEquals($this->object->jsOnResizeStart             ,'heeee'); }
function test_jsOnSelectStart             () { $this->assertEquals($this->object->jsOnSelectStart             ,null); $this->assertEquals($this->object->defaultjsOnSelectStart             (),$this->object->jsOnSelectStart             ); $this->object->jsOnSelectStart             ="heeee"; $this->assertEquals($this->object->jsOnSelectStart             ,'heeee'); }


        }

                    if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
                    else $script=$_GET['script'];

        if (basename($script)=='test_control.inc.php')
        {
                        echo "<html>";
                        echo "<head>";
                        echo "<title>PHP-Unit Results</title>";
                        echo "<STYLE TYPE=\"text/css\">";
                        include("phpunit/stylesheet.css");
                        echo "</STYLE>";
                        echo "</head>";
                        echo "<body>";
                        $suite = new TestSuite( "ControlTest" );
                        $testRunner = new TestRunner();
                        $testRunner->run( $suite );
                }


?>















