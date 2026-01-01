<?php
<object class="dmAuthentication" name="dmAuthentication" baseclass="datamodule">
  <property name="Height">600</property>
  <property name="Name">dmAuthentication</property>
  <property name="Width">800</property>
  <object class="ZAuth" name="ZAuth1" >
        <property name="Left">224</property>
        <property name="Top">96</property>
    <property name="AuthAdapter">ZAuthDigest1</property>
    <property name="Name">ZAuth1</property>
    <property name="UserName">user</property>
    <property name="UserPassword">pass</property>
    <property name="OnLogin">ZAuth1Login</property>
  </object>
  <object class="ZAuthDigest" name="ZAuthDigest1" >
        <property name="Left">124</property>
        <property name="Top">130</property>
    <property name="FileName">testFile.txt</property>
    <property name="Name">ZAuthDigest1</property>
  </object>
</object>
?>
