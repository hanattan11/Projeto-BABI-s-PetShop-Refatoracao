<?php
session_start();
include_once '../adm/config/conexao.php';

// Exibir e limpar mensagem de sessão
if (isset($_SESSION['msg'])) {
    echo $_SESSION['msg'];
    unset($_SESSION['msg']);
}
$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

if (!empty($dados['tb_cadastro'])) {
    try {
        // Criptografa a senha
        $dados['senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);
        
        // Query parametrizada para evitar SQL Injection
        $query_cadastro = "INSERT INTO usuarios (nome, email, senha, telefone, complemento, nivel_acesso_id, cep_id, created) 
                           VALUES (:nome, :email, :senha, :telefone, :complemento, :nivel_acesso_id, :cep_id, NOW())";

        $stmt = $conn->prepare($query_cadastro);
        
        // Bind dos parâmetros
        $stmt->bindParam(':nome', $dados['nome']);
        $stmt->bindParam(':email', $dados['email']);
        $stmt->bindParam(':senha', $dados['senha']);
        $stmt->bindParam(':telefone', $dados['telefone']);
        $stmt->bindParam(':complemento', $dados['complemento']);
        $stmt->bindParam(':nivel_acesso_id', $dados['nivel_acesso_id']);
        $stmt->bindParam(':cep_id', $dados['cep_id']);

        if ($stmt->execute()) {
            $_SESSION['msg'] = "<h2><p style='color:green'>Cadastrado com sucesso!</p></h2>";
            header("Location: ../pags/admin.php");
            exit;
        } else {
            throw new Exception("Erro ao inserir usuário.");
        }
    } catch (Exception $e) {
        $_SESSION['msg'] = "<h2><p style='color:red'>" . $e->getMessage() . "</p></h2>";
        header("Location: ../adm/admin.php");
        exit;
    }
} else {
    $_SESSION['msg'] = "<h2><p style='color:red'>Não foi possível cadastrar!</p></h2>";
    header("Location: ../adm/admin.php");
    exit;
}

