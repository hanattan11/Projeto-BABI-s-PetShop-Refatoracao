<?php

session_start();
include_once '../adm/config/conexao.php';

if (isset($_SESSION['msg'])) {
    echo $_SESSION['msg'];
    unset($_SESSION['msg']);
}

$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);
$telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
$complemento = filter_input(INPUT_POST, 'complemento', FILTER_SANITIZE_STRING);
$nivel_acesso_id = filter_input(INPUT_POST, 'nivel_acesso_id', FILTER_SANITIZE_NUMBER_INT);
$cep_id = filter_input(INPUT_POST, 'cep_id', FILTER_SANITIZE_NUMBER_INT);

if (!empty($id) && !empty($nome) && !empty($email) && !empty($telefone)) {
    
    if (!empty($senha)) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $query_update = "UPDATE usuarios 
                         SET nome = :nome, email = :email, senha = :senha, telefone = :telefone, 
                             complemento = :complemento, nivel_acesso_id = :nivel_acesso_id, 
                             cep_id = :cep_id, modified = NOW() 
                         WHERE id = :id LIMIT 1";
    } else {
        $query_update = "UPDATE usuarios 
                         SET nome = :nome, email = :email, telefone = :telefone, 
                             complemento = :complemento, nivel_acesso_id = :nivel_acesso_id, 
                             cep_id = :cep_id, modified = NOW() 
                         WHERE id = :id LIMIT 1";
    }

    $stmt = $conn->prepare($query_update);

    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':complemento', $complemento);
    $stmt->bindParam(':nivel_acesso_id', $nivel_acesso_id);
    $stmt->bindParam(':cep_id', $cep_id);
    $stmt->bindParam(':id', $id);

    if (!empty($senha)) {
        $stmt->bindParam(':senha', $senha_hash);
    }

    if ($stmt->execute() && $stmt->rowCount() > 0) {
        $_SESSION['msg'] = "<p style='color:green'>Usuário atualizado com sucesso! :)</p>";
    } else {
        $_SESSION['msg'] = "<p style='color:red'>Nenhum dado foi alterado ou erro na atualização :(</p>";
    }
} else {
    $_SESSION['msg'] = "<p style='color:red'>Falha na atualização: dados inválidos.</p>";
}

header("Location: ../pags/admin.php");
exit;
