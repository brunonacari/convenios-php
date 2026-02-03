<?php

include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/menu.inc.php';

include('protectAdmin.php');

$email = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_SPECIAL_CHARS);

$queryAdmin = "SELECT * FROM USUARIO WHERE EMAIL_USU like '$email'";
$querySelect = $pdoCAT->query($queryAdmin);

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $existeUsuario = $registros['EMAIL_USU'];
    $idUsuario = $registros['ID_USU'];
endwhile;

if (isset($existeUsuario)) {
    $queryLE = "SELECT U.*, C.*
    FROM USUARIO U 
    LEFT JOIN CONVENIOS C ON C.ID_CONVENIO = U.ID_CONVENIO 
    WHERE U.ID_USU = $idUsuario";

    $querySelectLE = $pdoCAT->query($queryLE);

    $perfilUsuario = array();
    while ($registrosLE = $querySelectLE->fetch(PDO::FETCH_ASSOC)) :
        $nmPerfil = $registrosLE['NOME_CONVENIO'];
        $idPerfil = $registrosLE['ID_CONVENIO'];
        $nmUsuario = $registrosLE['NM_USU'];
        $orgao = $registrosLE['ORGAO_USU'];
        $telUsuario = $registrosLE['TEL_USU'];
        $lgnUsuario = $registrosLE['LGN_USU'];

        $registroPU = array(
            'NOME_CONVENIO' => $nmPerfil,
            'ID_CONVENIO' => $idPerfil
        );

        $perfilUsuario[] = $registroPU;
    endwhile;
} else {
    $queryInsert = "SELECT [ID]
                    ,[sAMAccountName]
                    ,[initials]
                    ,[department]
                    ,[physicalDeliveryOfficeName]
                    ,[displayName]
                    ,[telephoneNumber]
                    ,[mobile]
                    ,[mail]
                    ,[accountExpires]
                    ,[IsEnabled]
                    ,[objectCategory]
                FROM [ADCache].[dbo].[Users]
                where mail like '$email'";

    $queryInsert2 = $pdoCAT->query($queryInsert);

    while ($registros = $queryInsert2->fetch(PDO::FETCH_ASSOC)) :
        $matricula = $registros['initials'];
        $nmUsuario = $registros['displayName'];
        $mail = $registros['mail'];
        $login = $registros['sAMAccountName'];
    endwhile;

    $loginCriador = $_SESSION['login'];
    $querySelect2 = "INSERT INTO USUARIO VALUES ($matricula, '$nmUsuario', '$mail', GETDATE(), 'A', '$loginCriador', '$login', NULL, NULL, NULL, NULL)";
    $querySelect = $pdoCAT->query($querySelect2);

    // REGISTRO DE LOG
    $login = $_SESSION['login'];
    $tela = 'Usuário';
    $acao = 'Perfil CRIADO para ' . $nmUsuario;
    $idEvento = $matricula;
    $queryLOG = $pdoCAT->query("INSERT INTO auditoria VALUES('$login', GETDATE(), '$tela', '$acao', $idEvento)");
}

?>

<!-- CSS específico da página -->
<link rel="stylesheet" href="style/css/convenios.css">

<div class="page-container">
    
    <!-- Page Header Unified (Tema Claro) -->
    <div class="page-header-unified">
        <div class="header-decoration">
            <div class="decoration-circle-1"></div>
            <div class="decoration-circle-2"></div>
        </div>

        <div class="header-top-row">
            <div class="header-breadcrumb">
                <a href="index.php"><ion-icon name="home-outline"></ion-icon> Início</a>
                <ion-icon name="chevron-forward-outline" class="breadcrumb-sep"></ion-icon>
                <a href="consultarUsuario.php">Usuários</a>
                <ion-icon name="chevron-forward-outline" class="breadcrumb-sep"></ion-icon>
                <span>Editar</span>
            </div>
            <div class="header-date" id="headerDate"></div>
        </div>

        <div class="header-main-row">
            <div class="header-left">
                <div class="header-icon-box">
                    <div class="icon-box-pulse"></div>
                    <ion-icon name="person-outline"></ion-icon>
                </div>
                <div class="header-title-group">
                    <h1>Editar Usuário</h1>
                    <p class="header-subtitle">
                        <ion-icon name="mail-outline"></ion-icon>
                        <?php echo htmlspecialchars($email); ?>
                    </p>
                </div>
            </div>
            <div class="header-right">
                <a href="consultarUsuario.php" class="btn-secundario">
                    <ion-icon name="arrow-back-outline"></ion-icon>
                    Voltar
                </a>
            </div>
        </div>
    </div>

    <!-- Content Card -->
    <div class="content-card">
        <form action="bd/usuario/update.php" method="post" id="formEditUsuario">
            <input type="hidden" id="idUsuario" name="idUsuario" value="<?php echo $idUsuario ?>" data-id="<?php echo $idUsuario; ?>">
            
            <!-- Dados do Usuário -->
            <div class="content-card-header">
                <div class="content-card-title">
                    <ion-icon name="person-outline"></ion-icon>
                    Dados do Usuário
                </div>
            </div>
            <div class="content-card-body">
                <div class="form-grid">
                    <div class="form-group col-span-4">
                        <label>
                            <ion-icon name="person-outline"></ion-icon>
                            Nome
                        </label>
                        <input type="text" value="<?= htmlspecialchars($nmUsuario ?? '') ?>" readonly class="input-readonly">
                    </div>
                    <div class="form-group col-span-4">
                        <label>
                            <ion-icon name="key-outline"></ion-icon>
                            Login
                        </label>
                        <input type="text" value="<?= htmlspecialchars($lgnUsuario ?? '') ?>" readonly class="input-readonly">
                    </div>
                    <div class="form-group col-span-4">
                        <label for="email">
                            <ion-icon name="mail-outline"></ion-icon>
                            E-mail
                        </label>
                        <?php if (strpos($email, '@cesan.com.br') !== false) { ?>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" readonly class="input-readonly">
                        <?php } else { ?>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>">
                        <?php } ?>
                    </div>
                    <div class="form-group col-span-4">
                        <label>
                            <ion-icon name="call-outline"></ion-icon>
                            Telefone
                        </label>
                        <input type="text" value="<?= htmlspecialchars($telUsuario ?? '') ?>" readonly class="input-readonly">
                    </div>
                    <div class="form-group col-span-4">
                        <label>
                            <ion-icon name="business-outline"></ion-icon>
                            Órgão Desejado
                        </label>
                        <input type="text" value="<?= htmlspecialchars($orgao ?? '') ?>" readonly class="input-readonly">
                    </div>
                    <div class="form-group col-span-4">
                        <label for="perfilUsuario">
                            <ion-icon name="ribbon-outline"></ion-icon>
                            Convênio Associado
                        </label>
                        <select name="perfilUsuario[]" id="perfilUsuario">
                            <?php
                            $querySelect2 = "SELECT * FROM CONVENIOS WHERE DT_FIM_CONVENIO >= GETDATE() OR NOME_CONVENIO  LIKE 'ADMINISTRADOR' ORDER BY NOME_CONVENIO";
                            $querySelect = $pdoCAT->query($querySelect2);

                            $queryPerfilUsuario = "SELECT C.*, U.*
                                                    FROM USUARIO U 
                                                    LEFT JOIN CONVENIOS C ON C.ID_CONVENIO = U.ID_CONVENIO 
                                                    WHERE U.ID_USU = $idUsuario";
                            $queryPerfisUsuario = $pdoCAT->query($queryPerfilUsuario);

                            $perfisUsuario = array();
                            while ($row = $queryPerfisUsuario->fetch(PDO::FETCH_ASSOC)) {
                                $perfisUsuario[] = $row["ID_CONVENIO"];
                            }

                            echo "<option value='NULL'>Nenhum convênio associado</option>";

                            while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                                $valorLE = $registros["ID_CONVENIO"];
                                $descricaoLE = $registros["NOME_CONVENIO"];
                                $selecionadoLE = in_array($valorLE, $perfisUsuario) ? 'selected' : '';
                                echo "<option value='" . $valorLE . "' $selecionadoLE>" . htmlspecialchars($descricaoLE) . "</option>";
                            endwhile;
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Anexos -->
            <div class="content-card-header" style="border-top: 1px solid #e2e8f0; margin-top: 24px;">
                <div class="content-card-title">
                    <ion-icon name="attach-outline"></ion-icon>
                    Anexos
                </div>
            </div>
            <div class="content-card-body">
                <div class="dropzone-container">
                    <div id="drop-zone" class="dropzone-modern" onclick="handleClick(event)" ondrop="handleDrop(event)" ondragover="handleDragOver(event)">
                        <ion-icon name="cloud-upload-outline" class="dropzone-icon"></ion-icon>
                        <p class="dropzone-text">Arraste e solte os arquivos aqui</p>
                        <p class="dropzone-hint">ou clique para selecionar</p>
                        <span class="dropzone-formats">Somente PDF</span>
                    </div>
                </div>

                <div id="filelist" class="file-list-container">
                    <?php
                    $directory = "anexos/usuario/" . $idUsuario;

                    if (is_dir($directory)) {
                        $files = scandir($directory);
                        $files = array_diff($files, array('.', '..'));

                        if (!empty($files)) {
                            echo '<div class="file-list-header">';
                            echo '<span><ion-icon name="document-text-outline"></ion-icon> Arquivos anexados</span>';
                            echo '</div>';
                            echo '<div class="file-list">';
                            
                            foreach ($files as $file) {
                                $filePath = $directory . '/' . $file;
                                echo '<div class="file-item">';
                                echo '<div class="file-info">';
                                echo '<ion-icon name="document-outline" class="file-icon"></ion-icon>';
                                echo '<a href="' . $filePath . '" target="_blank" class="file-name">' . htmlspecialchars($file) . '</a>';
                                echo '</div>';
                                echo '<button type="button" class="file-delete" onclick="confirmDelete(\'' . $file . '\', \'' . $directory . '\', \'' . $idUsuario . '\')" title="Excluir arquivo">';
                                echo '<ion-icon name="trash-outline"></ion-icon>';
                                echo '</button>';
                                echo '</div>';
                            }
                            
                            echo '</div>';
                        }
                    }
                    ?>
                </div>
            </div>

            <!-- Botões -->
            <div class="form-actions">
                <a href="consultarUsuario.php" class="btn btn-secondary">
                    <ion-icon name="close-outline"></ion-icon>
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <ion-icon name="checkmark-outline"></ion-icon>
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    // Data e hora do header
    function atualizarDataHeader() {
        const now = new Date();
        const options = { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' };
        const dataFormatada = now.toLocaleDateString('pt-BR', options);
        const headerDate = document.getElementById('headerDate');
        if (headerDate) {
            headerDate.textContent = dataFormatada.charAt(0).toUpperCase() + dataFormatada.slice(1);
        }
    }
    atualizarDataHeader();

    var idUsuario = document.getElementById('idUsuario').dataset.id;

    function handleDrop(event) {
        event.preventDefault();
        var files = event.dataTransfer.files;
        handleFiles(files, idUsuario);
        document.getElementById('drop-zone').classList.remove('dragover');
    }

    function handleClick(event) {
        var inputElement = document.createElement("input");
        inputElement.type = "file";
        inputElement.multiple = true;
        inputElement.addEventListener("change", function() {
            handleFiles(this.files, idUsuario);
        });
        inputElement.click();
    }

    function handleFiles(files, idUsuario) {
        if (files.length > 0) {
            if (validateFiles(files)) {
                var formData = new FormData();
                for (var i = 0; i < files.length; i++) {
                    formData.append('files[]', files[i]);
                }
                formData.append('idUsuario', idUsuario);

                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'upload.php', true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        $('#filelist').load(window.location.href + ' #filelist');
                    } else {
                        alert('Erro ao enviar os arquivos.');
                    }
                };
                xhr.send(formData);
            } else {
                alert('Só serão aceitos arquivos no formato PDF.');
            }
        } else {
            alert('Por favor, selecione um ou mais arquivos.');
        }
    }

    function validateFiles(files) {
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            if (!file.name.toLowerCase().endsWith('.pdf')) {
                alert(file.name + ' não é suportado.');
                return false;
            }
        }
        return true;
    }

    function handleDragOver(event) {
        event.preventDefault();
        document.getElementById('drop-zone').classList.add('dragover');
    }

    document.getElementById('drop-zone').addEventListener('dragleave', function(event) {
        event.preventDefault();
        document.getElementById('drop-zone').classList.remove('dragover');
    });

    function confirmDelete(file, directory, idUsuario) {
        if (confirm('Tem certeza que deseja excluir o arquivo?')) {
            $.ajax({
                url: 'excluir_arquivo.php',
                type: 'GET',
                data: {
                    file: file,
                    directory: directory,
                    idUsuario: idUsuario
                },
                success: function(response) {
                    $('#filelist').load(window.location.href + ' #filelist');
                },
                error: function(xhr, status, error) {
                    alert('Erro ao excluir o arquivo.');
                }
            });
        }
    }
</script>

<?php include_once 'includes/footer.inc.php'; ?>