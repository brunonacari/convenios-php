<?php
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/menu.inc.php';
include('protectPerfil.php');
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
                <span>Trocar Senha</span>
            </div>
            <div class="header-date" id="headerDate"></div>
        </div>

        <div class="header-main-row">
            <div class="header-left">
                <div class="header-icon-box">
                    <div class="icon-box-pulse"></div>
                    <ion-icon name="lock-closed-outline"></ion-icon>
                </div>
                <div class="header-title-group">
                    <h1>Trocar Senha</h1>
                    <p class="header-subtitle">
                        <ion-icon name="shield-checkmark-outline"></ion-icon>
                        Atualize sua senha de acesso
                    </p>
                </div>
            </div>
            <div class="header-right">
                <a href="consultarDados.php" class="btn-secundario">
                    <ion-icon name="arrow-back-outline"></ion-icon>
                    Voltar
                </a>
            </div>
        </div>
    </div>

    <!-- Content Card -->
    <div class="content-card" style="max-width: 500px;">
        <form action="bd/usuario/trocaSenha.php" method="post" id="formTrocaSenha">
            
            <div class="content-card-header">
                <div class="content-card-title">
                    <ion-icon name="key-outline"></ion-icon>
                    Alterar Senha
                </div>
            </div>
            <div class="content-card-body">
                <div class="form-group">
                    <label for="senhaAtual">
                        <ion-icon name="lock-open-outline"></ion-icon>
                        Senha Atual <span class="required">*</span>
                    </label>
                    <input type="password" id="senhaAtual" name="senhaAtual" placeholder="Digite sua senha atual" required>
                </div>

                <div class="form-group">
                    <label for="senhaNova">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        Nova Senha <span class="required">*</span>
                    </label>
                    <input type="password" id="senhaNova" name="senhaNova" maxlength="12" placeholder="Máximo 12 caracteres" required>
                </div>

                <div class="form-group">
                    <label for="senhaNova2">
                        <ion-icon name="checkmark-circle-outline"></ion-icon>
                        Confirmar Nova Senha <span class="required">*</span>
                    </label>
                    <input type="password" id="senhaNova2" name="senhaNova2" maxlength="12" placeholder="Repita a nova senha" required>
                </div>
            </div>

            <!-- Botões -->
            <div class="form-actions">
                <a href="consultarDados.php" class="btn btn-secondary">
                    <ion-icon name="close-outline"></ion-icon>
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <ion-icon name="checkmark-outline"></ion-icon>
                    Trocar Senha
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

    // Validação de confirmação de senha
    document.getElementById('formTrocaSenha').addEventListener('submit', function(e) {
        var senhaNova = document.getElementById('senhaNova').value;
        var senhaNova2 = document.getElementById('senhaNova2').value;
        
        if (senhaNova !== senhaNova2) {
            e.preventDefault();
            alert('As senhas não conferem!');
        }
    });
</script>

<?php include_once 'includes/footer.inc.php'; ?>