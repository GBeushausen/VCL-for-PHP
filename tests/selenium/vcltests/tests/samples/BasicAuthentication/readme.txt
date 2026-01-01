This sample features the BasicAuthentication component, which allows you to use
HTTP authentication with ease, without creating files on your server, just by
sending the right headers to the browser.

To use the component, just place it on a form and call the execute method on the
OnBeforeShow event of the Page. That will make the component to show the authentication
dialog if the user has not been already validated.

You can set the UserName and Password properties of the component to specify which
combination is going to be valid, or you can use the OnAuthenticate event to provide
more complicated authentication, like searching on a database.

To know which user and password has entered the user, check the $params parameter
of the event.
