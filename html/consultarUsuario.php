<?php
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/menu.inc.php';
include('protectAdmin.php');
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
                <span>Usuários</span>
            </div>
            <div class="header-date" id="headerDate"></div>
        </div>

        <!-- Main Row -->
        <div class="header-main-row">
            <div class="header-left">
                <div class="header-icon-box">
                    <div class="icon-box-pulse"></div>
                    <ion-icon name="people-outline"></ion-icon>
                </div>
                <div class="header-title-group">
                    <h1>Administrar Usuários</h1>
                    <p class="header-subtitle">
                        <ion-icon name="shield-checkmark-outline"></ion-icon>
                        Gerencie os usuários e suas permissões de acesso
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
        
        <form action="consultarUsuario.php" method="post" id="formFiltrar">
            <div class="filters-grid">
                <div class="form-group">
                    <label class="form-label">
                        <ion-icon name="person-outline"></ion-icon>
                        Nome/Login do Usuário
                    </label>
                    <input type="text" name="nome" id="nome" class="form-control" 
                           placeholder="Digite o nome ou login" maxlength="100" 
                           style="text-transform: uppercase" autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <ion-icon name="business-outline"></ion-icon>
                        Convênio/Status
                    </label>
                    <select name="perfilUsuario" id="perfilUsuario" class="form-control">
                        <option value='0' selected>Aguardando liberação</option>
                        <option value='999'>Liberação Indeferida</option>
                        <?php
                        $querySelect2 = "SELECT * FROM CONVENIOS ORDER BY NOME_CONVENIO";
                        $querySelect = $pdoCAT->query($querySelect2);
                        while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                            echo "<option value='" . $registros["ID_CONVENIO"] . "'>" . $registros["NOME_CONVENIO"] . "</option>";
                        endwhile;
                        ?>
                    </select>
                </div>

                <div class="form-group" style="align-self: end;">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <ion-icon name="search-outline"></ion-icon>
                        Pesquisar
                    </button>
                </div>
            </div>

            <div style="margin-top: 16px; padding: 12px 16px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                <p style="font-size: 13px; color: #64748b; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <ion-icon name="information-circle-outline" style="font-size: 18px; color: #3b82f6;"></ion-icon>
                    Ao pesquisar sem preencher nenhum campo, serão exibidos os usuários "aguardando" autorização.
                </p>
            </div>
        </form>
    </div>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST") { ?>
    <!-- Results Card -->
    <div class="content-card">
        <div class="content-card-header">
            <div class="content-card-title">
                <ion-icon name="list-outline"></ion-icon>
                Usuários Encontrados
            </div>
        </div>
        <div class="content-card-body">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Data Solicitação</th>
                        <th>Login</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Órgão</th>
                        <th style="text-align: center; width: 150px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php include_once 'bd/usuario/read.php'; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php } else { ?>
    <!-- Empty State -->
    <div class="content-card">
        <div class="content-card-body">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <ion-icon name="search-outline"></ion-icon>
                </div>
                <h3>Realize uma pesquisa</h3>
                <p>Utilize os filtros acima para buscar usuários no sistema.</p>
            </div>
        </div>
    </div>
    <?php } ?>

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
