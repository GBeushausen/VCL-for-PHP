<?php
        ini_set('display_errors',1);
        error_reporting(E_ALL);

        require_once "phpunit/phpunit.php";
        require_once "vcl/vcl.inc.php";
        require_once "test_focuscontrol.inc.php";
        use_unit("webservices.inc.php");



        class ServiceTest extends ComponentTest
        {
                function setup()
                {
                        parent::setup();
                        $this->object=new Service();
                        $this->object->Name="myobject";
                }

                function test_Active()
                {
                        $this->assertEquals($this->object->getActive(),false);
                        $this->assertEquals($this->object->defaultActive(),$this->object->getActive());
                        $this->object->Active=0;
                        $this->assertEquals($this->object->Active,0);
                }

                function test_ServiceName()
                {
                        $this->assertEquals($this->object->getServiceName(),"VCL");
                        $this->assertEquals($this->object->defaultServiceName(),$this->object->getServiceName());
                        $this->object->ServiceName=0;
                        $this->assertEquals($this->object->ServiceName,0);
                }

                function test_NameSpace()
                {
                        $this->assertEquals($this->object->getNameSpace(),'http://localhost' );
                        $this->object->NameSpace=false;
                        $this->assertEquals($this->object->defaultNameSpace(),$this->object->NameSpace);
                        $this->object->NameSpace=0;
                        $this->assertEquals($this->object->NameSpace,0);
                }

                function test_SchemaTargetNamespace()
                {
                        $this->assertEquals($this->object->getSchemaTargetNamespace(),'http://localhost/xsd' );
                        $this->object->SchemaTargetNamespace='';
                        $this->assertEquals($this->object->defaultSchemaTargetNamespace(),$this->object->SchemaTargetNamespace);
                        $this->object->SchemaTargetNamespace=0;
                        $this->assertEquals($this->object->SchemaTargetNamespace,0);
                }
                function test_getSchemaTargetOnRegisterServices()
                {
                        //tested within test_OnRegisterServices
                }
                function test_setOnRegisterServices()
                {
                        //tested within test_OnRegisterServices
                }
                function test_OnRegisterServices()
                {
                        $this->assertEquals($this->object->getOnRegisterServices(),null);
                        $this->assertEquals($this->object->defaultOnRegisterServices(),$this->object->getOnRegisterServices());
                        $this->object->OnRegisterServices=0;
                        $this->assertEquals($this->object->OnRegisterServices,0);
                }
                function test_getSchemaTargetOnAddComplexTypes()
                {
                        //tested within test_OnAddComplexTypes
                }

                function test_OnAddComplexTypes()
                {
                        $this->assertEquals($this->object->getOnAddComplexTypes(),null);
                        $this->assertEquals($this->object->defaultOnAddComplexTypes(),$this->object->getOnAddComplexTypes());
                        $this->object->OnAddComplexTypes=0;
                        $this->assertEquals($this->object->OnAddComplexTypes,0);
                }
                function test_register()
                {
                        $this->object->Active=true;
                        ob_start();
                        $this->object->init();
                        $c=$this->cleanString(ob_get_contents());
                        ob_end_clean();
                        $this->assertEquals('<html><head><title>NuSOAP:VCL</title><styletype="text/css">body{font-family:arial;color:#000000;background-color:#ffffff;margin:0px0px0px0px;}p{font-family:arial;color:#000000;margin-top:0px;margin-bottom:12px;}pre{background-color:silver;padding:5px;font-family:CourierNew;font-size:x-small;color:#000000;}ul{margin-top:10px;margin-left:20px;}li{list-style-type:none;margin-top:10px;color:#000000;}.content{margin-left:0px;padding-bottom:2em;}.nav{padding-top:10px;padding-bottom:10px;padding-left:15px;font-size:.70em;margin-top:10px;margin-left:0px;color:#000000;background-color:#ccccff;width:20%;margin-left:20px;margin-top:20px;}.title{font-family:arial;font-size:26px;color:#ffffff;background-color:#999999;width:105%;margin-left:0px;padding-top:10px;padding-bottom:10px;padding-left:15px;}.hidden{position:absolute;visibility:hidden;z-index:200;left:250px;top:100px;font-family:arial;overflow:hidden;width:600;padding:20px;font-size:10px;background-color:#999999;layer-background-color:#FFFFFF;}a,a:active{color:charcoal;font-weight:bold;}a:visited{color:#666666;font-weight:bold;}a:hover{color:cc3300;font-weight:bold;}</style><scriptlanguage="JavaScript"type="text/javascript"><!--//POP-UPCAPTIONS...functionlib_bwcheck(){//Browsercheck(needed)this.ver=navigator.appVersionthis.agent=navigator.userAgentthis.dom=document.getElementById?1:0this.opera5=this.agent.indexOf("Opera5")>-1this.ie5=(this.ver.indexOf("MSIE5")>-1&&this.dom&&!this.opera5)?1:0;this.ie6=(this.ver.indexOf("MSIE6")>-1&&this.dom&&!this.opera5)?1:0;this.ie4=(document.all&&!this.dom&&!this.opera5)?1:0;this.ie=this.ie4||this.ie5||this.ie6this.mac=this.agent.indexOf("Mac")>-1this.ns6=(this.dom&&parseInt(this.ver)>=5)?1:0;this.ns4=(document.layers&&!this.dom)?1:0;this.bw=(this.ie6||this.ie5||this.ie4||this.ns4||this.ns6||this.opera5)returnthis}varbw=newlib_bwcheck()//Makescrossbrowserobject.functionmakeObj(obj){this.evnt=bw.dom?document.getElementById(obj):bw.ie4?document.all[obj]:bw.ns4?document.layers[obj]:0;if(!this.evnt)returnfalsethis.css=bw.dom||bw.ie4?this.evnt.style:bw.ns4?this.evnt:0;this.wref=bw.dom||bw.ie4?this.evnt:bw.ns4?this.css.document:0;this.writeIt=b_writeIt;returnthis}//Aunitofmeasurethatwillbeaddedwhensettingthepositionofalayer.//varpx=bw.ns4||window.opera?"":"px";functionb_writeIt(text){if(bw.ns4){this.wref.write(text);this.wref.close()}elsethis.wref.innerHTML=text}//ShowsthemessagesvaroDesc;functionpopup(divid){if(oDesc=newmakeObj(divid)){oDesc.css.visibility="visible"}}functionpopout(){//Hidesmessageif(oDesc)oDesc.css.visibility="hidden"}//--></script></head><body><divclass=content><br><br><divclass=title>VCL</div><divclass=nav><p>Viewthe<ahref="test_service.inc.php?wsdl">WSDL</a>fortheservice.Clickonanoperationnametoviewit\'sdetails.</p><ul><ul></div></div></body></html>',$c);
                }

                function test_addComplexType()
                {
                        //nothing to test?
                }
        }
      if ($_SERVER['PHP_SELF']!='') $script=$_SERVER['PHP_SELF'];
        else $script=$_GET['script'];

       if (basename($script)=='test_service.inc.php')
        {
                echo "<html>";
                echo "<head>";
                echo "<title>PHP-Unit Results</title>";
                echo "<STYLE TYPE=\"text/css\">";
                include("phpunit/stylesheet.css");
                echo "</STYLE>";
                echo "</head>";
                echo "<body>";
                $suite = new TestSuite( "ServiceTest" );
                $testRunner = new TestRunner();
                $testRunner->run( $suite );
        }


?>
