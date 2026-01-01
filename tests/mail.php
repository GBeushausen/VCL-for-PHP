<?php
    
    $email=$argv[1];

    if($email)	
    {
        $output=file_get_contents('output.html');
        mail($email,"PHPUnit Test Results",$output,"Content-type: text/html\r\n");

        $errors=file_get_contents('errors.txt');
        mail($email,"PHPUnit Test Results [error output]",$errors);
    }
?>
