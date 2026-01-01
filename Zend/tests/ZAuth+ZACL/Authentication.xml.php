<?php
<object class="AuthenticationPage" name="AuthenticationPage" baseclass="page">
  <property name="Background"></property>
  <property name="Caption">AuthenticationPage</property>
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
  <property name="Name">AuthenticationPage</property>
  <property name="Width">800</property>
  <property name="OnBeforeShow">AuthenticationPageBeforeShow</property>
  <object class="Label" name="lbAll" >
    <property name="Alignment">agCenter</property>
    <property name="Caption">This label should be seen by both users</property>
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
    <property name="Left">219</property>
    <property name="Name">lbAll</property>
    <property name="Top">87</property>
    <property name="Width">357</property>
  </object>
  <object class="Label" name="lbAdministrator" >
    <property name="Alignment">agCenter</property>
    <property name="Caption">This label should be seen only by Administrator</property>
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
    <property name="Left">219</property>
    <property name="Name">lbAdministrator</property>
    <property name="Top">135</property>
    <property name="Width">357</property>
  </object>
</object>
?>
