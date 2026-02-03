<?php
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/menu.inc.php';
include_once('protectAdmin.php');
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
                <span>Cadastrar</span>
            </div>
            <div class="header-date" id="headerDate"></div>
        </div>

        <!-- Main Row -->
        <div class="header-main-row">
            <div class="header-left">
                <div class="header-icon-box">
                    <div class="icon-box-pulse"></div>
                    <ion-icon name="add-circle-outline"></ion-icon>
                </div>
                <div class="header-title-group">
                    <h1>Cadastrar Convênio</h1>
                    <p class="header-subtitle">
                        <ion-icon name="document-text-outline"></ion-icon>
                        Preencha os dados para cadastrar um novo convênio
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
        <form action="bd/convenios/create.php" method="post" id="formCadConvenio">
            
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
                        <input type="text" id="nomeConvenio" name="nomeConvenio" placeholder="Digite o nome do órgão" required>
                    </div>
                    <div class="form-group col-span-4">
                        <label for="numConvenio">
                            <ion-icon name="document-outline"></ion-icon>
                            Número do Convênio <span class="required">*</span>
                        </label>
                        <input type="text" id="numConvenio" name="numConvenio" placeholder="Ex: 001/2024" required>
                    </div>
                    <div class="form-group col-span-2">
                        <label for="dtIniConvenio">
                            <ion-icon name="calendar-outline"></ion-icon>
                            Data Início <span class="required">*</span>
                        </label>
                        <input type="date" id="dtIniConvenio" name="dtIniConvenio" required>
                    </div>
                    <div class="form-group col-span-2">
                        <label for="dtFimConvenio">
                            <ion-icon name="calendar-outline"></ion-icon>
                            Data Fim <span class="required">*</span>
                        </label>
                        <input type="date" id="dtFimConvenio" name="dtFimConvenio" required>
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
                        <input type="text" id="nomeContatoAdm" name="nomeContatoAdm" placeholder="Nome do contato" required>
                    </div>
                    <div class="form-group col-span-4">
                        <label for="emailContatoAdm">
                            <ion-icon name="mail-outline"></ion-icon>
                            E-mail <span class="required">*</span>
                        </label>
                        <input type="email" id="emailContatoAdm" name="emailContatoAdm" placeholder="email@exemplo.com" required>
                    </div>
                    <div class="form-group col-span-4">
                        <label for="telContatoAdm">
                            <ion-icon name="call-outline"></ion-icon>
                            Telefone <span class="required">*</span>
                        </label>
                        <input type="text" id="telContatoAdm" name="telContatoAdm" placeholder="(27) 00000-0000" required>
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
                        <input type="text" id="nomeContatoTI" name="nomeContatoTI" placeholder="Nome do contato" required>
                    </div>
                    <div class="form-group col-span-4">
                        <label for="emailContatoTI">
                            <ion-icon name="mail-outline"></ion-icon>
                            E-mail <span class="required">*</span>
                        </label>
                        <input type="email" id="emailContatoTI" name="emailContatoTI" placeholder="email@exemplo.com" required>
                    </div>
                    <div class="form-group col-span-4">
                        <label for="telContatoTI">
                            <ion-icon name="call-outline"></ion-icon>
                            Telefone <span class="required">*</span>
                        </label>
                        <input type="text" id="telContatoTI" name="telContatoTI" placeholder="(27) 00000-0000" required>
                    </div>
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
                    Cadastrar Convênio
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
</script>

<?php include_once 'includes/footer.inc.php'; ?>