<?php
include_once __DIR__ . '/../ConexaoMysql.php';

function atendenteRegister($nome, $email, $hash, $endereco, $cpf, $dataNasc)
{

    $con = new ConexaoMysql();

    $con->Conectar();

    $sql = 'INSERT INTO atendente (cpfAt, nomeAt, enderecoAt, email, senha, dataNasc) values ("' . $cpf . '","' . $nome . '","' . $endereco . '","' . $email . '","' . $hash . '","' . $dataNasc . '")';

    $result = $con->Executar($sql);

    $con->Desconectar();

    return $result;
}

function verifyAtendente($cpf, $email)
    {

        $con = new ConexaoMysql();
        $con->Conectar();

        // Verificar se o CPF ou o email já estão cadastrados
        $sql = 'SELECT COUNT(*) AS total FROM atendente WHERE cpfAt = "' . $cpf . '" OR email = "' . $email . '"';
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

?>