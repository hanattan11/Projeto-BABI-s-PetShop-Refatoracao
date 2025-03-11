<?php

session_start();

include_once '../adm/config/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

if (!empty($dados['bt_cadpet'])) {
    // Valida os dados antes de processar
    $pet_nome = filter_var($dados['pet_nome'], FILTER_SANITIZE_STRING);
    $especie_id = filter_var($dados['especie_id'], FILTER_SANITIZE_NUMBER_INT);
    $porte_id = filter_var($dados['porte_id'], FILTER_SANITIZE_NUMBER_INT);

    if (empty($pet_nome) || empty($especie_id) || empty($porte_id)) {
        $_SESSION['msg'] = "<h2><p style='color:red'>Por favor, preencha todos os campos!</p></h2>";
        header("Location: ../pags/formlogged.php");
        exit;
    }
    $query_cadastro = "INSERT INTO pets (nome, especie_id, porte_id, created) 
                       VALUES (:nome, :especie_id, :porte_id, NOW())";

    try {
        $result_usuario = $conn->prepare($query_cadastro);
        $result_usuario->bindParam(':nome', $pet_nome);
        $result_usuario->bindParam(':especie_id', $especie_id);
        $result_usuario->bindParam(':porte_id', $porte_id);

        // Executa a query
        $result_usuario->execute();

        if ($result_usuario->rowCount() > 0) {
            $_SESSION['msg'] = "<h2><p style='color:green'>Pet cadastrado com sucesso!</p></h2>";
            header("Location: ../pags/clientelogged.php");
        } else {
            $_SESSION['msg'] = "<h2><p style='color:red'>Não foi possível inserir o pet!</p></h2>";
            header("Location: ../pags/formlogged.php");
        }
    } catch (PDOException $e) {
        // Em caso de erro com a query, exibe mensagem
        $_SESSION['msg'] = "<h2><p style='color:red'>Erro ao cadastrar pet: " . $e->getMessage() . "</p></h2>";
        header("Location: ../pags/formlogged.php");
    }
} else {
    $_SESSION['msg'] = "<h2><p style='color:red'>Não foi possível cadastrar seu pet!</p></h2>";
    header("Location: ../pags/formlogged.php");
}
