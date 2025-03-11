<?php

session_start();
include_once '../adm/config/conexao.php';

// Obtém e sanitiza os dados do formulário
$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// Verifica se o botão de login foi pressionado
if (!empty($dados['bt_login'])) {
    
    $email = filter_var($dados['email'], FILTER_SANITIZE_EMAIL);
    $senha = filter_var($dados['senha'], FILTER_SANITIZE_STRING);

    if (!empty($email) && !empty($senha)) {
        $query_usuarios = "SELECT id, nome, email, senha, telefone, complemento, nivel_acesso_id, cep_id, created, modified 
                           FROM usuarios WHERE email = :email LIMIT 1";
        $result_usuario = $conn->prepare($query_usuarios);
        $result_usuario->bindValue(':email', $email);
        $result_usuario->execute();

        if ($result_usuario->rowCount() > 0) {
            $row_usuario = $result_usuario->fetch(PDO::FETCH_ASSOC);

            if (password_verify($senha, $row_usuario['senha'])) {
                // Define os dados da sessão
                $_SESSION['id'] = $row_usuario['id'];
                $_SESSION['nome'] = $row_usuario['nome'];
                $_SESSION['email'] = $row_usuario['email'];
                $_SESSION['nivel_acesso_id'] = $row_usuario['nivel_acesso_id'];

                // Redirecionamento baseado no nível de acesso
                switch ($row_usuario['nivel_acesso_id']) {
                    case 1:
                        header("Location: ../pags/clientelogged.php");
                        break;
                    case 2:
                        header("Location: ../pags/colaborador.php");
                        break;
                    case 3:
                        header("Location: ../pags/admin.php");
                        break;
                    default:
                        $_SESSION['msg'] = "<p style='color:red'>Entre em contato com o administrador!</p>";
                        header("Location: ../index.php");
                        break;
                }
                exit;
            } else {
                $_SESSION['msg'] = "<p style='color:red'>Usuário ou senha incorretos!</p>";
                header("Location: ../index.php");
                exit;
            }
        } else {
            $_SESSION['msg'] = "<p style='color:red'>Usuário não encontrado!</p>";
            header("Location: ../index.php");
            exit;
        }
    } else {
        $_SESSION['msg'] = "<p style='color:red'>Preencha todos os campos!</p>";
        header("Location: ../index.php");
        exit;
    }
} else {
    $_SESSION['msg'] = "<p style='color:red'>Não recebi valores do formulário!</p>";
    header("Location: ../index.php");
    exit;
}
