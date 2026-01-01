<?php
<object class="Unit1" name="Unit1" baseclass="page">
  <property name="Background"></property>
  <property name="Caption">Unit1</property>
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
  <property name="Name">Unit1</property>
  <property name="Width">800</property>
  <property name="OnAfterShow"></property>
  <property name="OnAfterShowFooter"></property>
  <property name="OnBeforeShow"></property>
  <property name="OnBeforeShowHeader"></property>
  <property name="OnCreate">Unit1Create</property>
  <property name="OnShow"></property>
  <property name="OnShowHeader"></property>
  <property name="OnStartBody"></property>
  <property name="OnTemplate"></property>
  <object class="ListView" name="ListView1" >
    <property name="Columns"><![CDATA[a:4:{i:0;s:7:&quot;Caption&quot;;i:1;s:8:&quot;Column 2&quot;;i:2;s:11:&quot;Bool column&quot;;i:3;s:2:&quot;c4&quot;;}]]></property>
    <property name="Height">327</property>
    <property name="Hint">this is a test hint</property>
    <property name="Items">a:0:{}</property>
    <property name="Left">21</property>
    <property name="Name">ListView1</property>
    <property name="ParentColor">0</property>
    <property name="SelectionType">selMultiInterval</property>
    <property name="SortAscending">0</property>
    <property name="Tag">2</property>
    <property name="Top">25</property>
    <property name="Width">435</property>
    <property name="OnAfterShow"></property>
    <property name="OnBeforeShow"></property>
    <property name="OnShow"></property>
    <property name="OnSubmit">ListView1Submit</property>
  </object>
  <object class="Button" name="Button1" >
    <property name="Caption">Submit</property>
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
    <property name="Left">544</property>
    <property name="Name">Button1</property>
    <property name="Top">24</property>
    <property name="Width">75</property>
    <property name="OnAfterShow"></property>
    <property name="OnBeforeShow"></property>
    <property name="OnClick">Button1Click</property>
    <property name="OnShow"></property>
  </object>
  <object class="ListBox" name="ListBox1" >
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
    <property name="Height">240</property>
    <property name="Items">a:0:{}</property>
    <property name="Left">544</property>
    <property name="Name">ListBox1</property>
    <property name="Top">96</property>
    <property name="Width">192</property>
    <property name="OnAfterShow"></property>
    <property name="OnBeforeShow"></property>
    <property name="OnShow"></property>
    <property name="OnSubmit"></property>
  </object>
  <object class="Label" name="Label1" >
    <property name="Caption">Selected items:</property>
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
    <property name="Left">544</property>
    <property name="Name">Label1</property>
    <property name="Top">80</property>
    <property name="Width">168</property>
    <property name="OnAfterShow"></property>
    <property name="OnBeforeShow"></property>
    <property name="OnShow"></property>
  </object>
</object>
?>
