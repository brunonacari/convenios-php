<?php
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/menu.inc.php';
include_once('protectAdmin.php');

foreach ($_SESSION['perfil'] as $perfil) {
    $idPerfil[] = $perfil['idPerfil'];

    if ($perfil['idPerfil'] == 9) {
        $isAdmin = 1;
    }
}

$idPerfilFinal = implode(',', $idPerfil);

$idConvenio = $_GET['idConvenio'];

$querySelect2 = "SELECT * FROM [CONVENIOS].[dbo].[CONVENIOS] WHERE ID_CONVENIO = $idConvenio";

$querySelect = $pdoCAT->query($querySelect2);

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $idConvenio =       $registros['ID_CONVENIO'];
    $nomeConvenio =     $registros['NOME_CONVENIO'];
    $numConvenio =      $registros['NUM_CONVENIO'];
    $dtIniConvenio =    $registros['DT_INI_CONVENIO'];
    $dtFimConvenio =    $registros['DT_FIM_CONVENIO'];

    $dtIniConvenio = (new DateTime($dtIniConvenio))->format('Y-m-d');
    $dtFimConvenio = (new DateTime($dtFimConvenio))->format('Y-m-d');

    $nomeContatoAdm =   $registros["NOME_CONTATO_ADM"];
    $emailContatoAdm =  $registros["EMAIL_CONTATO_ADM"];
    $telContatoAdm =    $registros["TEL_CONTATO_ADM"];
    $nomeContatoTI =    $registros["NOME_CONTATO_TI"];
    $emailContatoTI =   $registros["EMAIL_CONTATO_TI"];
    $telContatoTI =     $registros["TEL_CONTATO_TI"];
endwhile;

?>

<!-- CSS específico da página -->
<link rel="stylesheet" href="style/css/convenios.css">

<div class="page-container">
    
    <!-- Page Header Unified (Tema Claro) -->
    <div class="page-header-unified">
        <!-- Elementos decorativos -->
        <div class="header-decoration">
            <div class="decoration-circle-1"></div>
            <div class="decoration-circle-2"></div>
        </div>

        <!-- Breadcrumb + Data -->
        <div class="header-top-row">
            <div class="header-breadcrumb">
                <a href="index.php"><ion-icon name="home-outline"></ion-icon> Início</a>
                <ion-icon name="chevron-forward-outline" class="breadcrumb-sep"></ion-icon>
                <a href="consultarConvenios.php">Convênios</a>
                <ion-icon name="chevron-forward-outline" class="breadcrumb-sep"></ion-icon>
                <span>Editar</span>
            </div>
            <div class="header-date" id="headerDate"></div>
        </div>

        <!-- Main Row -->
        <div class="header-main-row">
            <div class="header-left">
                <div class="header-icon-box">
                    <div class="icon-box-pulse"></div>
                    <ion-icon name="create-outline"></ion-icon>
                </div>
                <div class="header-title-group">
                    <h1>Editar Convênio</h1>
                    <p class="header-subtitle">
                        <ion-icon name="business-outline"></ion-icon>
                        <?php echo htmlspecialchars($nomeConvenio); ?>
                    </p>
                </div>
            </div>
            <div class="header-right">
                <a href="consultarConvenios.php" class="btn-secundario">
                    <ion-icon name="arrow-back-outline"></ion-icon>
                    Voltar
                </a>
            </div>
        </div>
    </div>

    <!-- Content Card -->
    <div class="content-card">
        <form action="bd/convenios/update.php" method="post" id="formEditConvenio">
            <input type="hidden" id="idConvenio" name="idConvenio" value="<?php echo $idConvenio ?>">
            
            <!-- Dados do Convênio -->
            <div class="content-card-header">
                <div class="content-card-title">
                    <ion-icon name="business-outline"></ion-icon>
                    Dados do Convênio
                </div>
            </div>
            <div class="content-card-body">
                <div class="form-grid">
                    <div class="form-group col-span-4">
                        <label for="nomeConvenio">
                            <ion-icon name="bookmark-outline"></ion-icon>
                            Nome do Órgão Conveniado <span class="required">*</span>
                        </label>
                        <input type="text" id="nomeConvenio" name="nomeConvenio" value="<?php echo htmlspecialchars($nomeConvenio); ?>" required>
                    </div>
                    <div class="form-group col-span-4">
                        <label for="numConvenio">
                            <ion-icon name="document-outline"></ion-icon>
                            Número do Convênio <span class="required">*</span>
                        </label>
                        <input type="text" id="numConvenio" name="numConvenio" value="<?php echo htmlspecialchars($numConvenio); ?>" required>
                    </div>
                    <div class="form-group col-span-2">
                        <label for="dtIniConvenio">
                            <ion-icon name="calendar-outline"></ion-icon>
                            Data Início <span class="required">*</span>
                        </label>
                        <input type="date" id="dtIniConvenio" name="dtIniConvenio" value="<?php echo $dtIniConvenio; ?>" required>
                    </div>
                    <div class="form-group col-span-2">
                        <label for="dtFimConvenio">
                            <ion-icon name="calendar-outline"></ion-icon>
                            Data Fim <span class="required">*</span>
                        </label>
                        <input type="date" id="dtFimConvenio" name="dtFimConvenio" value="<?php echo $dtFimConvenio; ?>" required>
                    </div>
                </div>
            </div>

            <!-- Contato Administrativo -->
            <div class="content-card-header" style="border-top: 1px solid #e2e8f0; margin-top: 24px;">
                <div class="content-card-title">
                    <ion-icon name="person-outline"></ion-icon>
                    Contato Administrativo
                </div>
            </div>
            <div class="content-card-body">
                <div class="form-grid">
                    <div class="form-group col-span-4">
                        <label for="nomeContatoAdm">
                            <ion-icon name="person-outline"></ion-icon>
                            Nome <span class="required">*</span>
                        </label>
                        <input type="text" id="nomeContatoAdm" name="nomeContatoAdm" value="<?php echo htmlspecialchars($nomeContatoAdm); ?>" required>
                    </div>
                    <div class="form-group col-span-4">
                        <label for="emailContatoAdm">
                            <ion-icon name="mail-outline"></ion-icon>
                            E-mail <span class="required">*</span>
                        </label>
                        <input type="email" id="emailContatoAdm" name="emailContatoAdm" value="<?php echo htmlspecialchars($emailContatoAdm); ?>" required>
                    </div>
                    <div class="form-group col-span-4">
                        <label for="telContatoAdm">
                            <ion-icon name="call-outline"></ion-icon>
                            Telefone <span class="required">*</span>
                        </label>
                        <input type="text" id="telContatoAdm" name="telContatoAdm" value="<?php echo htmlspecialchars($telContatoAdm); ?>" required>
                    </div>
                </div>
            </div>

            <!-- Contato TI -->
            <div class="content-card-header" style="border-top: 1px solid #e2e8f0; margin-top: 24px;">
                <div class="content-card-title">
                    <ion-icon name="code-slash-outline"></ion-icon>
                    Contato da Área de Informática
                </div>
            </div>
            <div class="content-card-body">
                <div class="form-grid">
                    <div class="form-group col-span-4">
                        <label for="nomeContatoTI">
                            <ion-icon name="person-outline"></ion-icon>
                            Nome <span class="required">*</span>
                        </label>
                        <input type="text" id="nomeContatoTI" name="nomeContatoTI" value="<?php echo htmlspecialchars($nomeContatoTI); ?>" required>
                    </div>
                    <div class="form-group col-span-4">
                        <label for="emailContatoTI">
                            <ion-icon name="mail-outline"></ion-icon>
                            E-mail <span class="required">*</span>
                        </label>
                        <input type="email" id="emailContatoTI" name="emailContatoTI" value="<?php echo htmlspecialchars($emailContatoTI); ?>" required>
                    </div>
                    <div class="form-group col-span-4">
                        <label for="telContatoTI">
                            <ion-icon name="call-outline"></ion-icon>
                            Telefone <span class="required">*</span>
                        </label>
                        <input type="text" id="telContatoTI" name="telContatoTI" value="<?php echo htmlspecialchars($telContatoTI); ?>" required>
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
                        <span class="dropzone-formats">PDF ou Imagem</span>
                    </div>
                </div>

                <div id="filelist" class="file-list-container">
                    <?php
                    $directory = "anexos" . '/' . $idConvenio;

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
                                $fileExt = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                $iconName = $fileExt === 'pdf' ? 'document-outline' : 'image-outline';
                                
                                echo '<div class="file-item">';
                                echo '<div class="file-info">';
                                echo '<ion-icon name="' . $iconName . '" class="file-icon"></ion-icon>';
                                echo '<a href="' . $filePath . '" target="_blank" class="file-name">' . htmlspecialchars($file) . '</a>';
                                echo '</div>';
                                echo '<button type="button" class="file-delete" onclick="confirmDelete(\'' . $file . '\', \'' . $directory . '\', \'' . $idConvenio . '\')" title="Excluir arquivo">';
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
                <a href="consultarConvenios.php" class="btn btn-secondary">
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

    // Máscara de telefone
    $(document).ready(function() {
        var maskBehavior = function(val) {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
        },
        options = {
            onKeyPress: function(val, e, field, options) {
                field.mask(maskBehavior.apply({}, arguments), options);
            }
        };

        $('#telContatoAdm, #telContatoTI').mask(maskBehavior, options);
    });

    var idConvenio = document.getElementById('idConvenio').value;

    function handleDrop(event) {
        event.preventDefault();
        var files = event.dataTransfer.files;
        handleFiles(files, idConvenio);
        document.getElementById('drop-zone').classList.remove('dragover');
    }

    function handleClick(event) {
        var inputElement = document.createElement("input");
        inputElement.type = "file";
        inputElement.multiple = true;
        inputElement.addEventListener("change", function() {
            handleFiles(this.files, idConvenio);
        });
        inputElement.click();
    }

    function handleFiles(files, idConvenio) {
        idConvenio = idConvenio.value || idConvenio;

        if (files.length > 0) {
            if (validateFiles(files)) {
                var formData = new FormData();

                for (var i = 0; i < files.length; i++) {
                    formData.append('files[]', files[i]);
                }

                formData.append('idConvenio', idConvenio);

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
                alert('Só serão aceitos arquivos no formato PDF ou Imagem.');
            }
        } else {
            alert('Por favor, selecione um ou mais arquivos.');
        }
    }

    function validateFiles(files) {
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            if (file.type.startsWith('image/')) {
                // É uma imagem
            } else if (file.name.toLowerCase().endsWith('.pdf')) {
                // É um PDF
            } else {
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

    function confirmDelete(file, directory, idConvenio) {
        if (confirm('Tem certeza que deseja excluir o arquivo?')) {
            $.ajax({
                url: 'excluir_arquivo.php',
                type: 'GET',
                data: {
                    file: file,
                    directory: directory,
                    idConvenio: idConvenio
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