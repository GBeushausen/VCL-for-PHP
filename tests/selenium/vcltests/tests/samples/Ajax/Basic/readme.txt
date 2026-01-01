This sample shows you how to use ajax in the easiest way, by using the ajaxCall
method. This sample does two things:
-In a Javascript event, generates the code to perform the ajaxCall
-In a PHP method, it changes several properties of components, which will get
updated when the ajax call is finished

Button1JSClick is a javascript event, which is called when the button is clicked,
and here you find the following line:

    echo $this->Button1->ajaxCall("Button1Click");

This code dumps the javascript code required to make an ajax call to a PHP method
in the server called "Button1Click", the code inside that method is PHP code, which
changes the Caption for several controls.

Running the application and clicking on the button will update the captions and
color of the label without posting the form to the server.
