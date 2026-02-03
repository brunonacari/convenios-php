<?php
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/menu.inc.php';
include_once('protectPerfil.php');
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
                <span>Consulta de Dados</span>
            </div>
            <div class="header-date" id="headerDate"></div>
        </div>

        <!-- Main Row -->
        <div class="header-main-row">
            <div class="header-left">
                <div class="header-icon-box">
                    <div class="icon-box-pulse"></div>
                    <ion-icon name="search-outline"></ion-icon>
                </div>
                <div class="header-title-group">
                    <h1>Consultar Dados de Clientes</h1>
                    <p class="header-subtitle">
                        <ion-icon name="document-text-outline"></ion-icon>
                        Pesquise informações dos clientes pelos filtros disponíveis
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="filters-card">
        <div class="filters-header">
            <div class="filters-title">
                <ion-icon name="funnel-outline"></ion-icon>
                Filtros de Pesquisa
            </div>
        </div>
        
        <form action="" method="POST" id="formFiltrar">
            <div style="margin-bottom: 16px; padding: 10px 14px; background: #fef3c7; border: 1px solid #fcd34d; border-radius: 8px;">
                <p style="font-size: 13px; color: #92400e; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <ion-icon name="warning-outline" style="font-size: 18px;"></ion-icon>
                    Não utilize acentuação na pesquisa.
                </p>
            </div>

            <div class="filters-grid">
                <div class="form-group">
                    <label class="form-label">
                        <ion-icon name="person-outline"></ion-icon>
                        Nome
                    </label>
                    <input type="text" name="nmCliente" id="nmCliente" class="form-control" 
                           placeholder="Nome do cliente" maxlength="100" autocomplete="off"
                           value="<?php echo isset($_POST['nmCliente']) ? htmlspecialchars($_POST['nmCliente']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <ion-icon name="location-outline"></ion-icon>
                        Rua / Avenida
                    </label>
                    <input type="text" name="endCliente" id="endCliente" class="form-control" 
                           placeholder="Endereço" maxlength="100" autocomplete="off"
                           value="<?php echo isset($_POST['endCliente']) ? htmlspecialchars($_POST['endCliente']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <ion-icon name="map-outline"></ion-icon>
                        Bairro
                    </label>
                    <input type="text" name="bairroCliente" id="bairroCliente" class="form-control" 
                           placeholder="Bairro" maxlength="100" autocomplete="off"
                           value="<?php echo isset($_POST['bairroCliente']) ? htmlspecialchars($_POST['bairroCliente']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <ion-icon name="business-outline"></ion-icon>
                        Município
                    </label>
                    <input type="text" name="municipioCliente" id="municipioCliente" class="form-control" 
                           placeholder="Município" maxlength="100" autocomplete="off"
                           value="<?php echo isset($_POST['municipioCliente']) ? htmlspecialchars($_POST['municipioCliente']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <ion-icon name="card-outline"></ion-icon>
                        Documento
                    </label>
                    <input type="text" name="documentoCliente" id="documentoCliente" class="form-control" 
                           placeholder="CPF / CNPJ / RG / CNH / CTPS" maxlength="100" autocomplete="off"
                           value="<?php echo isset($_POST['documentoCliente']) ? htmlspecialchars($_POST['documentoCliente']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <ion-icon name="layers-outline"></ion-icon>
                        Total de resultados
                    </label>
                    <select name="maxFiltro" id="maxFiltro" class="form-control">
                        <option value='100' <?php echo (isset($_POST['maxFiltro']) && $_POST['maxFiltro'] == '100') ? 'selected' : ''; ?>>100 resultados</option>
                        <option value='1000' <?php echo (isset($_POST['maxFiltro']) && $_POST['maxFiltro'] == '1000') ? 'selected' : ''; ?>>1.000 resultados</option>
                        <option value='10000' <?php echo (isset($_POST['maxFiltro']) && $_POST['maxFiltro'] == '10000') ? 'selected' : ''; ?>>10.000 resultados</option>
                    </select>
                </div>
            </div>

            <div style="margin-top: 20px; display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary">
                    <ion-icon name="search-outline"></ion-icon>
                    Pesquisar
                </button>
                <button type="button" id="btnLimpar" class="btn btn-secondary" onclick="window.location.href='consultarDados.php'">
                    <ion-icon name="refresh-outline"></ion-icon>
                    Limpar Filtros
                </button>
            </div>
        </form>
    </div>

    <!-- Results Card -->
    <div class="content-card">
        <div class="content-card-header">
            <div class="content-card-title">
                <ion-icon name="list-outline"></ion-icon>
                Dados do Cliente
            </div>
        </div>
        <div class="content-card-body">
            <?php
            // Verifica se o formulário foi submetido
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                include_once 'bd/dados/read.php';
            } else {
            ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <ion-icon name="search-outline"></ion-icon>
                </div>
                <h3>Realize uma pesquisa</h3>
                <p>Preencha ao menos um dos filtros acima e clique em "Pesquisar".</p>
            </div>
            <?php } ?>
        </div>
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
</script>

<?php include_once 'includes/footer.inc.php'; ?>
