<?php
include_once __DIR__ . '/../ConexaoMysql.php';

function AllservRegistrados()
{

    $con = new ConexaoMysql();

    $con->Conectar();

    $sql = 'SELECT * FROM servico';

    $result = $con->Consultar($sql);

    $con->Desconectar();

    return $result;
}

function buscarPetsByID($id)
{
    $con = new ConexaoMysql();

    $con->Conectar();

    $sql = 'SELECT nomePet FROM pet WHERE idPet = "' . $id . '"';

    $result = $con->Consultar($sql);

    $con->Desconectar();

    return $result;
}

?>