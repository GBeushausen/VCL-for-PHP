This sample shows you how to use ajax to work with databases, it uses ajax to
send to the server the table the user wants to browse, and change the table
properties to open the right one.

We are using here the javascript even OnChange of the ListBox to perform the
ajax call, this line:

params=document.AjaxDatabase.ListBox1.options[document.AjaxDatabase.ListBox1.value].text;

Set params to the name of the table the user has clicked on, and will be sent to
the server. After that:

echo $this->ListBox1->ajaxCall("changeTable",array(),array("ddaddress_book1"));

This dumps the ajax javascript code to perform the call to the changeTable method
which also receives the instruction to update only the DBGrid, the rest of controls
won't get updated. This is useful to reduce the amount of traffic on the ajax return.
