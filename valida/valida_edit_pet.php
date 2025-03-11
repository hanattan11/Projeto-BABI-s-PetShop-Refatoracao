<?php

session_start();
include_once '../adm/config/conexao.php';

// Exibir mensagem de sessão (caso exista)
if (isset($_SESSION['msg'])) {
    echo $_SESSION['msg'];
    unset($_SESSION['msg']);
}

$id_pet = filter_input(INPUT_POST, 'id_pet', FILTER_SANITIZE_NUMBER_INT);
$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
$especie_id = filter_input(INPUT_POST, 'especie_id', FILTER_SANITIZE_NUMBER_INT);
$porte_id = filter_input(INPUT_POST, 'porte_id', FILTER_SANITIZE_NUMBER_INT);
$modified = date("Y-m-d H:i:s"); // Atualiza o campo de modificação

if (!empty($id_pet)) {

    $query_edita_pets = "UPDATE pets SET nome = :nome, especie_id = :especie_id, porte_id = :porte_id, modified = :modified WHERE id = :id_pet LIMIT 1";

    $stmt = $conn->prepare($query_edita_pets);
    
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':especie_id', $especie_id);
    $stmt->bindParam(':porte_id', $porte_id);
    $stmt->bindParam(':modified', $modified);
    $stmt->bindParam(':id_pet', $id_pet);

    if ($stmt->execute() && $stmt->rowCount() > 0) {
        $_SESSION['msg'] = "<p style='color:green'>Pet atualizado com sucesso!</p>";
    } else {
        $_SESSION['msg'] = "<p style='color:red'>Erro ao atualizar o pet ou nenhum dado foi alterado.</p>";
    }
} else {
    $_SESSION['msg'] = "<p style='color:red'>ID do pet inválido.</p>";
}

header("Location: ../pags/admin.php");
exit;
