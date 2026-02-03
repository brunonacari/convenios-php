<?php

include_once 'bd/conexao.php';
include_once('../../protectAdmin.php');

$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
$perfilUsuario = filter_input(INPUT_POST, 'perfilUsuario', FILTER_SANITIZE_SPECIAL_CHARS);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar e processar os dados do formulário
    // var_dump($perfilUsuario);

    // Verifique se o campo $nome está preenchido
    if (!empty($nome)) {
        $querySelect2 = "WITH UserLicitacao AS (
            SELECT 
                U.*,
                A.ID_CONVENIO AS USUARIO_ID_CONVENIO,
                A.NM_USU,
                A.LGN_USU,
                A.STATUS_USU,
                A.EMAIL_USU,
                C.ID_CONVENIO AS CONVENIO_ID_CONVENIO,
                C.NOME_CONVENIO
            FROM 
                [ADCache].[dbo].[Users] U
                FULL OUTER JOIN CONVENIOS.dbo.USUARIO A 
                    ON A.EMAIL_USU COLLATE SQL_Latin1_General_CP1_CI_AI = U.mail COLLATE SQL_Latin1_General_CP1_CI_AI
                LEFT JOIN CONVENIOS.dbo.CONVENIOS C 
                    ON C.ID_CONVENIO = A.ID_CONVENIO
            WHERE 
                (U.sAMAccountName LIKE '%$nome%' 
                OR U.displayName LIKE '%$nome%' 
                OR A.NM_USU LIKE '%$nome%' 
                OR A.EMAIL_USU LIKE '%$nome%')
                AND (initials IS NOT NULL OR A.MAT_USU IS NOT NULL)
                AND NM_USU NOT LIKE ''
                AND EMAIL_USU NOT LIKE ''
        ";

        if ($perfilUsuario != 0) {
            
            $querySelect2 .= " AND C.ID_CONVENIO = $perfilUsuario";
        }

        $querySelect2 .= ")
        SELECT distinct *
        FROM UserLicitacao";
    } else {
        // Se $nome estiver vazio e $perfilUsuario for nulo, trazer os usuários da tabela USUARIO cuja coluna ID_CONVENIO seja NULL
        if (empty($perfilUsuario)) {
            $querySelect2 = "
            SELECT *
            FROM 
                CONVENIOS.dbo.USUARIO
            WHERE 
                ID_CONVENIO IS NULL
                AND NM_USU NOT LIKE '' 
                AND EMAIL_USU NOT LIKE ''
                AND STATUS_USU NOT LIKE 'I'";
        } else if ($perfilUsuario == 999) {
            $querySelect2 = "
            SELECT *
            FROM 
                CONVENIOS.dbo.USUARIO
            WHERE 
                STATUS_USU LIKE 'I'
                AND NM_USU NOT LIKE ''
                ";
        
        } else {
            // QUANDO $nome está vazio e $perfilUsuario preenchido
            $querySelect2 = "WITH UserLicitacao AS (
                SELECT 
                    U.*,
                    A.ID_CONVENIO AS USUARIO_ID_CONVENIO,
                    A.NM_USU,
                    A.LGN_USU,
                    A.STATUS_USU,
                    A.EMAIL_USU,
                    C.ID_CONVENIO AS CONVENIO_ID_CONVENIO,
                    C.NOME_CONVENIO
                FROM 
                    [ADCache].[dbo].[Users] U
                    FULL OUTER JOIN CONVENIOS.dbo.USUARIO A 
                        ON A.EMAIL_USU COLLATE SQL_Latin1_General_CP1_CI_AI = U.mail COLLATE SQL_Latin1_General_CP1_CI_AI
                    LEFT JOIN CONVENIOS.dbo.CONVENIOS C 
                        ON C.ID_CONVENIO = A.ID_CONVENIO
                WHERE 
                    (initials IS NOT NULL OR A.MAT_USU IS NOT NULL)
                    AND C.ID_CONVENIO = $perfilUsuario
            )
            SELECT distinct *
            FROM UserLicitacao";
        }
    }
   
    // VAR_DUMP($querySelect2);
    // exit();

    $querySelect = $pdoCAT->query($querySelect2);

    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
        $matricula = $registros['initials'];

        if (isset($registros['displayName'])) {
            $nome = $registros['displayName'];
            $unidade = $registros['department'];
            $login = $registros['sAMAccountName'];
        } else {
            $nome = $registros['NM_USU'];
            $unidade = 'externo';
            $login = $registros['LGN_USU'];
        }


        if (isset($registros['mail'])) {
            $email = $registros['mail'];
        } else {
            $email = $registros['EMAIL_USU'];
        }

        $administrador = $registros['ID_USU'];
        $status = $registros['STATUS_USU'];
        $nmPerfil = $registros['NOME_CONVENIO'];
        $idPerfil = $registros['ID_CONVENIO'];
        $dtCadUsuario = $registros['DT_CADASTRO_USU'];
        
        // Formatar data
        $dtCadFormatada = $dtCadUsuario ? date('d/m/Y', strtotime($dtCadUsuario)) : '-';


        echo "<tr>";
        if ($status == 'A') {
            echo "<td>$dtCadFormatada</td>";
            echo "<td><strong>$login</strong></td>";
            echo "<td>$nome</td>";
            echo "<td>$email</td>";
            echo "<td><span class='status-badge active'>$nmPerfil</span></td>";
        } else {
            echo "<td>$dtCadFormatada</td>";
            echo "<td><s style='color:#94a3b8;'>$login</s></td>";
            echo "<td><s style='color:#94a3b8;'>$nome</s></td>";
            echo "<td><s style='color:#94a3b8;'>$email</s></td>";
            echo "<td><span class='status-badge inactive'>$nmPerfil</span></td>";
        }

        echo "<td class='text-center'>";
        echo "<div class='table-actions'>";
        
        foreach ($_SESSION['perfil'] as $perfil) {
            if ($perfil['nomeConvenio'] == 'ADMINISTRADOR') {
                if ($unidade == 'externo') {
                    if ($status == 'A') {
                        echo "<a href='bd/usuario/desativa.php?email=$email' class='btn-icon delete' title='Desativar Usuário'><ion-icon name='close-circle-outline'></ion-icon></a>";
                    } else {
                        echo "<a href='bd/usuario/ativa.php?email=$email' class='btn-icon view' title='Ativar Usuário'><ion-icon name='checkmark-circle-outline'></ion-icon></a>";
                    }
                } else {
                    echo "<span></span>";
                }
                break;
            }
        }

        foreach ($_SESSION['perfil'] as $perfil) {
            if ($perfil['nomeConvenio'] == 'ADMINISTRADOR') {
                echo "<span></span>";
                echo "<a href='editarUsuario.php?email=$email' class='btn-icon edit' title='Editar Usuário'><ion-icon name='create-outline'></ion-icon></a>";
                break;
            }
        }

        echo "</div>";
        echo "</td>";
        echo "</tr>";


    endwhile;
}
