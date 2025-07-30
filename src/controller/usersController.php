<?php
include_once __DIR__ . '/../ConexaoMysql.php';

function usersRegister($nome, $email, $hash, $endereco, $cpf, $dataNasc)
{

    $con = new ConexaoMysql();

    $con->Conectar();

    $sql = 'INSERT INTO administrador (cpf, nome, dataNasc, email, endereco, senha) values ("' . $cpf . '","' . $nome . '","' . $dataNasc . '","' . $email . '","' . $endereco . '","' . $hash . '")';

    $result = $con->Executar($sql);

    $con->Desconectar();

    return $result;
}

function usersLogin($email, $cpf)
{

    $con = new ConexaoMysql();

    $con->Conectar();

    $sql = 'SELECT * FROM administrador WHERE email="' . $email . '" AND cpf="' . $cpf . '"';

    $result = $con->Consultar($sql);

    $con->Desconectar();

    return $result;
}



function verifyUser($cpf ,$email)
{

    $con = new ConexaoMysql();
    $con->Conectar();

    // Verificar se o CPF ou o email já estão cadastrados
    $sql = 'SELECT COUNT(*) AS total FROM administrador WHERE cpf = "' . $cpf . '" OR email = "' . $email . '"';
    $result = $con->Consultar($sql);

    if ($result) {
        $row = $result->fetch_assoc();
        $con->Desconectar();
        
        // Se total for maior que 0, significa que já existe um usuário com esse CPF ou email
        return $row['total'] > 0 ? 1 : 0;
    }

    $con->Desconectar();
    return 0;

}

function usersFilter()
{

    $con = new ConexaoMysql();

    $con->Conectar();

    $sql = 'SELECT cpf FROM petshop.cliente';

    $result = $con->Consultar($sql);

    $con->Desconectar();

    return $result;
}


?>