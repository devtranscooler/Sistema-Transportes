<?php
session_start();

if(is_null($_SESSION['ID_USUARIO'])){
    //
    header ("Location: ../index.php");
}else{
    $id_usuario=$_SESSION['ID_USUARIO'];
    $completeName=$_SESSION['NAME'];
    $id_tipo_usuario=$_SESSION['ID_TIPO_USUARIO'];
    $id_sucursal=$_SESSION['ID_SUCURSAL'];
    $passphrase='SKYISTHELIMIT';
}
?>