<?php
        require_once "interbasecfg.inc.php";

        $isql=$argv[1];
        $database=$argv[2];

        $str="CREATE DATABASE '$database'   USER 'SYSDBA'
           PASSWORD 'masterkey'
           PAGE_SIZE 4096;


           /* Structure for the `TABLA_PRUEBA` table :  */

           CREATE TABLE TABLA_PRUEBA (
                ID SMALLINT NOT NULL,
                NOMBRE VARCHAR(50) CHARACTER SET WIN1252 NOT NULL COLLATE WIN1252,
                DIRECCION VARCHAR(50) CHARACTER SET WIN1252 NOT NULL COLLATE WIN1252);


           CREATE INDEX IDX_TABLA_PRUEBA ON TABLA_PRUEBA(NOMBRE);

           /* Privilegies for the `TABLA_PRUEBA` :  */

           GRANT SELECT, INSERT, DELETE, REFERENCES, UPDATE ON TABLA_PRUEBA TO SYSDBA WITH GRANT OPTION;";

        $conn=@ibase_connect(GetIBDataBaseName(), "SYSDBA", "masterkey");
        if(!$conn)
        {
                $ibok=true;

        }
        else
        {
                $ibok=false;
                ibase_close($conn);
        }

        //must we create database?
        if ($ibok)
        {
                $fp=fopen("testsource/ibdump.sql","w");
                fwrite($fp,$str,strlen($str));
                fclose($fp);
                $cmd='"'.$isql .'"'. " -i testsource/ibdump.sql";

                /*if(*!*/exec($cmd);//)
                        //echo "problems runnign interbase database commands";
        }


?>
