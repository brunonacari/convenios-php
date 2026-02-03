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
                <span>Convênios</span>
            </div>
            <div class="header-date" id="headerDate"></div>
        </div>

        <!-- Main Row -->
        <div class="header-main-row">
            <div class="header-left">
                <div class="header-icon-box">
                    <div class="icon-box-pulse"></div>
                    <ion-icon name="business-outline"></ion-icon>
                </div>
                <div class="header-title-group">
                    <h1>Gerenciar Convênios</h1>
                    <p class="header-subtitle">
                        <ion-icon name="document-text-outline"></ion-icon>
                        Cadastre e gerencie os convênios ativos no sistema
                    </p>
                </div>
            </div>
            <div class="header-right">
                <a href="cadConvenios.php" class="btn-novo-pro">
                    <ion-icon name="add-outline"></ion-icon>
                    Novo Convênio
                </a>
            </div>
        </div>
    </div>

    <!-- Content Card -->
    <div class="content-card">
        <div class="content-card-header">
            <div class="content-card-title">
                <ion-icon name="list-outline"></ion-icon>
                Convênios Vigentes
            </div>
        </div>
        <div class="content-card-body">
            <?php
            $totalRegistros = 0;
            $querySelect = null;
            
            if (isset($pdoCAT) && $pdoCAT !== null) {
                try {
                    $querySelect2 = "SELECT * FROM [convenios].[dbo].[convenios]
                                        WHERE NOME_CONVENIO NOT LIKE 'ADMINISTRADOR' 
                                        AND DT_FIM_CONVENIO >= GETDATE()
                                        ORDER BY [nome_convenio]";

                    $querySelect = $pdoCAT->query($querySelect2);
                    $totalRegistros = $querySelect ? $querySelect->rowCount() : 0;
                } catch (PDOException $e) {
                    $totalRegistros = 0;
                    // Para debug, descomente a linha abaixo:
                    // echo "<p style='color: red;'>Erro na consulta: " . $e->getMessage() . "</p>";
                }
            }

            if ($totalRegistros > 0):
            ?>
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Órgão Conveniado</th>
                        <th>Nº Convênio</th>
                        <th>Data Início</th>
                        <th>Data Fim</th>
                        <th style="text-align: center; width: 120px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                        $idConvenio = $registros['ID_CONVENIO'];
                        $nomeConvenio = $registros['NOME_CONVENIO'];
                        $numConvenio = $registros['NUM_CONVENIO'];
                        $dtIniConvenio = $registros['DT_INI_CONVENIO'];
                        $dtFimConvenio = $registros['DT_FIM_CONVENIO'];
                        
                        // Formatar datas
                        $dtIniFormatada = date('d/m/Y', strtotime($dtIniConvenio));
                        $dtFimFormatada = date('d/m/Y', strtotime($dtFimConvenio));
                        
                        // Verificar se está próximo do vencimento (30 dias)
                        $diasRestantes = (strtotime($dtFimConvenio) - time()) / (60 * 60 * 24);
                        $statusClass = $diasRestantes < 30 ? 'pending' : 'active';
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($nomeConvenio); ?></strong>
                        </td>
                        <td><?php echo htmlspecialchars($numConvenio); ?></td>
                        <td><?php echo $dtIniFormatada; ?></td>
                        <td>
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <?php echo $dtFimFormatada; ?>
                                <?php if ($diasRestantes < 30): ?>
                                    <ion-icon name="alert-circle-outline"></ion-icon>
                                <?php endif; ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <?php if ($nomeConvenio != 'ADMINISTRADOR'): ?>
                            <div class="table-actions">
                                <a href="editarConvenios.php?idConvenio=<?php echo $idConvenio; ?>" 
                                   class="btn-icon edit" 
                                   title="Editar Convênio">
                                    <ion-icon name="create-outline"></ion-icon>
                                </a>
                                <span></span>
                                <a href="javascript:void(0)" 
                                   onclick="confirmDesativar(<?php echo $idConvenio; ?>)" 
                                   class="btn-icon delete" 
                                   title="Desativar Convênio">
                                    <ion-icon name="close-circle-outline"></ion-icon>
                                </a>
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <ion-icon name="business-outline"></ion-icon>
                </div>
                <h3>Nenhum convênio encontrado</h3>
                <p>Clique em "Novo Convênio" para cadastrar o primeiro convênio.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- Modal de Confirmação -->
<div id="modalConfirm" class="modal-overlay">
    <div class="modal-container" style="max-width: 420px;">
        <div class="modal-header">
            <h3>
                <ion-icon name="alert-circle-outline" style="color: #ef4444; font-size: 24px;"></ion-icon>
                Confirmar Desativação
            </h3>
            <button type="button" class="modal-close" onclick="fecharModal()">
                <ion-icon name="close-outline"></ion-icon>
            </button>
        </div>
        <div class="modal-body">
            <p style="color: #475569; font-size: 14px; line-height: 1.6;">
                Tem certeza que deseja desativar este convênio? Esta ação pode ser revertida pelo administrador.
            </p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="fecharModal()">
                <ion-icon name="close-outline"></ion-icon>
                Cancelar
            </button>
            <button type="button" class="btn btn-danger" id="btnConfirmDesativar">
                <ion-icon name="trash-outline"></ion-icon>
                Desativar
            </button>
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

    let idConvenioParaDesativar = null;

    function confirmDesativar(id) {
        idConvenioParaDesativar = id;
        document.getElementById('modalConfirm').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function fecharModal() {
        document.getElementById('modalConfirm').classList.remove('active');
        document.body.style.overflow = '';
        idConvenioParaDesativar = null;
    }

    document.getElementById('btnConfirmDesativar').addEventListener('click', function() {
        if (idConvenioParaDesativar) {
            window.location.href = 'bd/convenios/desativa.php?idConvenio=' + idConvenioParaDesativar;
        }
    });

    // Fechar modal com ESC ou clicando fora
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            fecharModal();
        }
    });

    document.getElementById('modalConfirm').addEventListener('click', function(e) {
        if (e.target === this) {
            fecharModal();
        }
    });
</script>

<?php include_once 'includes/footer.inc.php'; ?>
