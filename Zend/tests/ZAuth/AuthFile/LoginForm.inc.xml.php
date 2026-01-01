<?php
<object class="LoginFormPage" name="LoginFormPage" baseclass="page">
  <property name="Background"></property>
  <property name="Caption">LoginFormPage</property>
  <property name="DocType">dtNone</property>
  <property name="Font">
    <property name="Align">taNone</property>
    <property name="Case"></property>
    <property name="Color"></property>
    <property name="Family">Verdana</property>
    <property name="LineHeight"></property>
    <property name="Size">10px</property>
    <property name="Style"></property>
    <property name="Variant"></property>
    <property name="Weight"></property>
  </property>
  <property name="Height">600</property>
  <property name="IsMaster">0</property>
  <property name="Layout">
    <property name="Cols">5</property>
    <property name="Rows">5</property>
    <property name="Type">ABS_XY_LAYOUT</property>
    <property name="UsePixelTrans">1</property>
  </property>
  <property name="Name">LoginFormPage</property>
  <property name="Width">800</property>
  <object class="Label" name="Label1" >
    <property name="Alignment">agRight</property>
    <property name="Caption">User</property>
    <property name="Font">
        <property name="Align">taNone</property>
        <property name="Case"></property>
        <property name="Color"></property>
        <property name="Family">Verdana</property>
        <property name="LineHeight"></property>
        <property name="Size">10px</property>
        <property name="Style"></property>
        <property name="Variant"></property>
        <property name="Weight"></property>
    </property>
    <property name="Height">13</property>
    <property name="Left">235</property>
    <property name="Name">Label1</property>
    <property name="Top">103</property>
    <property name="Width">75</property>
  </object>
  <object class="Label" name="Label2" >
    <property name="Alignment">agRight</property>
    <property name="Caption">Password</property>
    <property name="Font">
        <property name="Align">taNone</property>
        <property name="Case"></property>
        <property name="Color"></property>
        <property name="Family">Verdana</property>
        <property name="LineHeight"></property>
        <property name="Size">10px</property>
        <property name="Style"></property>
        <property name="Variant"></property>
        <property name="Weight"></property>
    </property>
    <property name="Height">13</property>
    <property name="Left">235</property>
    <property name="Name">Label2</property>
    <property name="Top">143</property>
    <property name="Width">75</property>
  </object>
  <object class="Edit" name="edUserName" >
    <property name="Font">
        <property name="Align">taNone</property>
        <property name="Case"></property>
        <property name="Color"></property>
        <property name="Family">Verdana</property>
        <property name="LineHeight"></property>
        <property name="Size">10px</property>
        <property name="Style"></property>
        <property name="Variant"></property>
        <property name="Weight"></property>
    </property>
    <property name="Height">21</property>
    <property name="Left">323</property>
    <property name="Name">edUserName</property>
    <property name="Text">hexdump</property>
    <property name="Top">95</property>
    <property name="Width">121</property>
  </object>
  <object class="Edit" name="edUserPassword" >
    <property name="Font">
        <property name="Align">taNone</property>
        <property name="Case"></property>
        <property name="Color"></property>
        <property name="Family">Verdana</property>
        <property name="LineHeight"></property>
        <property name="Size">10px</property>
        <property name="Style"></property>
        <property name="Variant"></property>
        <property name="Weight"></property>
    </property>
    <property name="Height">21</property>
    <property name="Left">323</property>
    <property name="Name">edUserPassword</property>
    <property name="Text">pass</property>
    <property name="Top">135</property>
    <property name="Width">121</property>
  </object>
  <object class="Button" name="Button1" >
    <property name="Caption">Log in</property>
    <property name="Font">
        <property name="Align">taNone</property>
        <property name="Case"></property>
        <property name="Color"></property>
        <property name="Family">Verdana</property>
        <property name="LineHeight"></property>
        <property name="Size">10px</property>
        <property name="Style"></property>
        <property name="Variant"></property>
        <property name="Weight"></property>
    </property>
    <property name="Height">25</property>
    <property name="Left">315</property>
    <property name="Name">Button1</property>
    <property name="Top">223</property>
    <property name="Width">75</property>
    <property name="OnClick">Button1Click</property>
  </object>
  <object class="Label" name="Label3" >
    <property name="Alignment">agRight</property>
    <property name="Caption">Role</property>
    <property name="Font">
        <property name="Align">taNone</property>
        <property name="Case"></property>
        <property name="Color"></property>
        <property name="Family">Verdana</property>
        <property name="LineHeight"></property>
        <property name="Size">10px</property>
        <property name="Style"></property>
        <property name="Variant"></property>
        <property name="Weight"></property>
    </property>
    <property name="Height">13</property>
    <property name="Left">235</property>
    <property name="Name">Label3</property>
    <property name="Top">183</property>
    <property name="Width">75</property>
  </object>
  <object class="Edit" name="edRealm" >
    <property name="Font">
        <property name="Align">taNone</property>
        <property name="Case"></property>
        <property name="Color"></property>
        <property name="Family">Verdana</property>
        <property name="LineHeight"></property>
        <property name="Size">10px</property>
        <property name="Style"></property>
        <property name="Variant"></property>
        <property name="Weight"></property>
    </property>
    <property name="Height">21</property>
    <property name="Left">323</property>
    <property name="Name">edRealm</property>
    <property name="Text">myrealm</property>
    <property name="Top">175</property>
    <property name="Width">121</property>
  </object>
</object>
?>
