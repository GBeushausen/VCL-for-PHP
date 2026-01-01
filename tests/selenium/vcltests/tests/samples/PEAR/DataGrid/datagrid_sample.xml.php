<?php
<object class="Unit293" name="Unit293" baseclass="page">
  <property name="Background"></property>
  <property name="Caption">Unit293</property>
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
  </property>
  <property name="Name">Unit293</property>
  <property name="Width">800</property>
  <property name="OnAfterShow"></property>
  <property name="OnAfterShowFooter"></property>
  <property name="OnBeforeShow"></property>
  <property name="OnBeforeShowHeader"></property>
  <property name="OnCreate"></property>
  <property name="OnShow"></property>
  <property name="OnShowHeader"></property>
  <property name="OnStartBody"></property>
  <property name="OnTemplate"></property>
  <object class="PearDataGrid" name="PearDataGrid1" >
    <property name="DSN">mysql://root:test@localhost/oscommerce</property>
    <property name="Height">283</property>
    <property name="Left">115</property>
    <property name="Name">PearDataGrid1</property>
    <property name="SQL"><![CDATA[a:1:{i:0;s:69:&quot;select products_id, products_model, products_date_added from products&quot;;}]]></property>
    <property name="Top">77</property>
    <property name="Width">549</property>
    <property name="OnAfterShow"></property>
    <property name="OnBeforeShow"></property>
    <property name="OnShow"></property>
  </object>
</object>
?>
