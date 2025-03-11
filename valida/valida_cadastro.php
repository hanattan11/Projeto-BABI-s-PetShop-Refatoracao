<?php

session_start();
include_once '../adm/config/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

if (!empty($dados['bt_cadastro'])) {
    // Criptografando a senha
    $dados['senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);

    // Query de inserção com placeholders
    $query_cadastro = "INSERT INTO usuarios 
        (nome, email, senha, telefone, complemento, nivel_acesso_id, cep_id, created) 
        VALUES 
        (:nome, :email, :senha, :telefone, :complemento, :nivel_acesso_id, :cep_id, NOW())";

    // Preparando a consulta
    $stmt = $conn->prepare($query_cadastro);
    
    // Bind dos parâmetros para segurança
    $stmt->bindParam(':nome', $dados['nome']);
    $stmt->bindParam(':email', $dados['email']);
    $stmt->bindParam(':senha', $dados['senha']);
    $stmt->bindParam(':telefone', $dados['telefone']);
    $stmt->bindParam(':complemento', $dados['complemento']);
    $stmt->bindParam(':nivel_acesso_id', $dados['nivel_acesso_id']);
    $stmt->bindParam(':cep_id', $dados['cep_id']);

    // Executando a inserção
    if ($stmt->execute()) {
        $_SESSION['msg'] = "<h2><p style='color:green'>Cadastrado com sucesso!</p></h2>";
        header("Location: ../pags/cliente.php");
    } else {
        $_SESSION['msg'] = "<h2><p style='color:red'>Não foi possível inserir usuário!</p></h2>";
        header("Location: ../adm/cadastrar.php");
    }
} else {
    $_SESSION['msg'] = "<h2><p style='color:red'>Não foi possível cadastrar!</p></h2>";
    header("Location: ../adm/cadastrar.php");
}

exit;
