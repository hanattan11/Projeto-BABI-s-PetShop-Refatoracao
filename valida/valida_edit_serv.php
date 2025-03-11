<?php

session_start();
include_once '../adm/config/conexao.php';

if (isset($_SESSION['msg'])) {
    echo $_SESSION['msg'];
    unset($_SESSION['msg']);
}

$id_serv = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$nome = filter_input(INPUT_POST, 'tipo_servico', FILTER_SANITIZE_STRING);
$descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);
$imagem = $_FILES['imagens']['name'] ?? null;

if (!empty($id_serv)) {

    $query_edita_servicos = "UPDATE servicos 
        SET tipo_servico = :tipo_servico, 
            descricao = :descricao, 
            imagem = :imagem, 
            modified = NOW() 
        WHERE id = :id_serv";

    $stmt = $conn->prepare($query_edita_servicos);
    
    $stmt->bindParam(':tipo_servico', $nome);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':imagem', $imagem);
    $stmt->bindParam(':id_serv', $id_serv);

    if ($stmt->execute() && $stmt->rowCount() > 0) {
        $_SESSION['msg'] = "<p style='color:green'>Serviço atualizado!</p>";

        if (!empty($imagem)) {
            $pasta = "../imagem/" . $id_serv . "/";

            if (!is_dir($pasta)) {
                mkdir($pasta, 0777, true);
            }

            if (move_uploaded_file($_FILES['imagens']['tmp_name'], $pasta . $imagem)) {
                $_SESSION['msg'] .= "<p style='color:yellow'>Imagem alterada!</p>";
            } else {
                $_SESSION['msg'] .= "<p style='color:white'>Erro ao alterar imagem!</p>";
            }
        }
    } else {
        $_SESSION['msg'] = "<p style='color:red'>Erro ao atualizar o serviço ou nenhum dado foi alterado.</p>";
    }
} else {
    $_SESSION['msg'] = "<p style='color:red'>ID do serviço inválido.</p>";
}

header("Location: ../pags/admin.php");
exit;
