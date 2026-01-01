<?php
<object class="dmAuthentication" name="dmAuthentication" baseclass="datamodule">
  <property name="Height">600</property>
  <property name="Name">dmAuthentication</property>
  <property name="Width">800</property>
  <object class="ZAuth" name="ZAuth1" >
        <property name="Left">168</property>
        <property name="Top">88</property>
    <property name="AuthAdapter">ZAuthDB1</property>
    <property name="Name">ZAuth1</property>
    <property name="OnLogin">ZAuth1Login</property>
  </object>
  <object class="ZAuthDB" name="ZAuthDB1" >
        <property name="Left">94</property>
        <property name="Top">87</property>
    <property name=""></property>
    <property name="DatabaseName">authentication</property>
    <property name="DriverName">mysql</property>
    <property name="Host">localhost</property>
    <property name="Name">ZAuthDB1</property>
    <property name="UserName">root</property>
    <property name="UserNameFieldName">username</property>
    <property name="UserPassword">root</property>
    <property name="UserPasswordFieldName">password</property>
    <property name="UserRealmFieldName">realm</property>
    <property name="UserTable">credentials</property>
  </object>
</object>
?>
