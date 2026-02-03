<?php
//ARQUIVO QUE FAZ A VALIDAÇÃO SE O USUÁRIO ESTÁ LOGADO NO SISTEMA
session_start();
include_once '../bd/conexao.php';

$login = $_SESSION['login'] ?? '';
$nomeUsuario = $_SESSION['nomeUsuario'] ?? $login;

// Processa perfis
$idPerfil = [];
$isAdmin = false;

if (!empty($_SESSION['perfil'])) {
    foreach ($_SESSION['perfil'] as $perfil) {
        $idPerfil[] = $perfil['idConvenio'];
        if ($perfil['nomeConvenio'] == 'ADMINISTRADOR') {
            $isAdmin = true;
        }
    }
    $_SESSION['idPerfilFinal'] = implode(',', $idPerfil);
}

// Detectar ambiente
function getAmbiente()
{
    if (
        strpos($_SERVER['HTTP_HOST'], 'vdesk') !== false
        || strpos($_SERVER['HTTP_HOST'], 'hom-') !== false
    ) {
        return "HOMOLOGAÇÃO";
    }
    return "PRODUÇÃO";
}

// Obter inicial do usuário para avatar
function getInitials($name)
{
    $parts = explode(' ', trim($name));
    if (count($parts) >= 2) {
        return strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1));
    }
    return strtoupper(substr($name, 0, 2));
}

function isMobile()
{
    $mobileKeywords = array('Android', 'iPhone', 'iPad', 'Windows Phone', 'BlackBerry', 'Opera Mini', 'Symbian', 'Mobile');
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    foreach ($mobileKeywords as $keyword) {
        if (stripos($userAgent, $keyword) !== false) {
            return true;
        }
    }
    return false;
}

$userInitials = getInitials($nomeUsuario);
$ambiente = getAmbiente();
$paginaAtual = basename($_SERVER['PHP_SELF'], '.php');

// Mensagem do sistema
$msgSistema = '';
if (isset($_SESSION['msg'])) {
    $msgSistema = $_SESSION['msg'];
    unset($_SESSION['msg']);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Convênios - CESAN</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    <style>
        /* ============================================
           RESET & BASE
           ============================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 60px 0 0 220px !important;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
            background-color: #f8fafc !important;
            min-height: 100vh;
            transition: padding-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body.sidebar-collapsed {
            padding-left: 70px !important;
        }

        /* ============================================
           HEADER PRINCIPAL - 60px
           ============================================ */
        .modern-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .modern-header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .btn-toggle-menu {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-toggle-menu:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }

        .btn-toggle-menu ion-icon {
            font-size: 20px;
        }

        .modern-header-left a {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .modern-header-logo {
            width: 32px;
            height: 38px;
            border-radius: 8px;
            object-fit: contain;
        }

        .modern-header-title {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .modern-header-title .brand-name {
            font-size: 16px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.02em;
            line-height: 1;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modern-header-title .system-fullname {
            font-size: 10px;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.4);
            letter-spacing: 0.02em;
            line-height: 1;
        }

        .ambiente-badge {
            font-size: 8px;
            font-weight: 700;
            color: #0f172a;
            background: #fbbf24;
            padding: 3px 8px;
            border-radius: 100px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .ambiente-badge.producao {
            background: #22c55e;
            color: white;
        }

        .modern-header-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 14px 6px 6px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 100px;
            font-size: 13px;
            font-weight: 600;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
        }

        .user-avatar.admin {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .admin-badge {
            background: rgba(251, 191, 36, 0.2);
            color: #fbbf24;
            font-size: 9px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 2px;
        }

        .btn-logout {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.25);
            color: #fca5a5;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.2);
            border-color: rgba(239, 68, 68, 0.4);
            color: #fef2f2;
        }

        .btn-logout ion-icon {
            font-size: 18px;
        }

        .btn-login {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.25);
            color: #93c5fd;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-login:hover {
            background: rgba(59, 130, 246, 0.2);
            border-color: rgba(59, 130, 246, 0.4);
            color: #bfdbfe;
        }

        .btn-login ion-icon {
            font-size: 18px;
        }

        /* ============================================
           SIDEBAR - 220px (70px collapsed)
           ============================================ */
        .modern-sidebar {
            position: fixed;
            top: 60px;
            left: 0;
            width: 220px;
            height: calc(100vh - 60px);
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            padding: 16px 0;
            z-index: 999;
            overflow-y: auto;
            overflow-x: hidden;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.04);
        }

        .modern-sidebar.collapsed {
            width: 70px;
        }

        .modern-sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .modern-sidebar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        /* Seção do Menu */
        .sidebar-section {
            margin-bottom: 8px;
        }

        /* Título da Seção */
        .sidebar-section-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 16px;
            margin: 0 12px 4px 12px;
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.2s ease;
            user-select: none;
        }

        .sidebar-section-title:hover {
            background: #f1f5f9;
            color: #475569;
        }

        .sidebar-section-title .section-icon {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sidebar-section-title .section-icon ion-icon {
            font-size: 14px;
            opacity: 0.7;
        }

        .sidebar-section-title .toggle-icon {
            font-size: 14px;
            transition: transform 0.3s ease;
            opacity: 0.5;
        }

        .sidebar-section-title.collapsed .toggle-icon {
            transform: rotate(-90deg);
        }

        /* Itens do Menu */
        .sidebar-section-content {
            max-height: 1000px;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .sidebar-section-content.collapsed {
            max-height: 0;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            margin: 2px 12px;
            color: #475569;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            border-radius: 10px;
            transition: all 0.15s ease;
            cursor: pointer;
        }

        .sidebar-item:hover {
            background: #f1f5f9;
            color: #1e293b;
        }

        .sidebar-item.active {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }

        .sidebar-item ion-icon,
        .sidebar-item i {
            font-size: 18px;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .sidebar-item span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Collapsed styles */
        .modern-sidebar.collapsed .sidebar-section-title span,
        .modern-sidebar.collapsed .sidebar-section-title .toggle-icon,
        .modern-sidebar.collapsed .sidebar-item span {
            display: none;
        }

        .modern-sidebar.collapsed .sidebar-section-title {
            justify-content: center;
            padding: 10px;
            margin: 0 8px 4px 8px;
        }

        .modern-sidebar.collapsed .sidebar-item {
            justify-content: center;
            padding: 10px;
            margin: 2px 8px;
        }

        /* ============================================
           SYSTEM MESSAGE
           ============================================ */
        .system-message {
            position: fixed;
            top: 70px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2000;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .system-message.success {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
        }

        .system-message.error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .system-message.warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .system-message.info {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        /* ============================================
           CONTAINER DO CONTEÚDO
           ============================================ */
        .container {
            padding: 24px;
            max-width: 1600px;
            margin: 0 auto;
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 768px) {
            body {
                padding-left: 0 !important;
            }

            .modern-sidebar {
                transform: translateX(-100%);
                width: 260px;
            }

            .modern-sidebar.mobile-open {
                transform: translateX(0);
            }

            .modern-header-title .brand-name {
                font-size: 14px;
            }

            .modern-header-title .system-fullname {
                display: none;
            }

            .user-info span:not(.user-avatar) {
                display: none;
            }

            .user-info {
                padding: 4px;
            }

            .container {
                padding: 16px;
            }
        }
    </style>
</head>

<body>

    <!-- HEADER PRINCIPAL -->
    <header class="modern-header">
        <div class="modern-header-left">
            <button class="btn-toggle-menu" id="btnToggleMenu" title="Menu">
                <ion-icon name="menu-outline"></ion-icon>
            </button>

            <a href="index.php">
                <img src="imagens/logo_icon.png" alt="Logo CESAN" class="modern-header-logo">
                <div class="modern-header-title">
                    <span class="brand-name">
                        Portal de Convênios
                        <?php if ($ambiente === 'HOMOLOGAÇÃO'): ?>
                            <span class="ambiente-badge">HOM</span>
                        <?php else: ?>
                            <span class="ambiente-badge producao">PRD</span>
                        <?php endif; ?>
                    </span>
                    <span class="system-fullname">Sistema de Gestão de Convênios</span>
                </div>
            </a>
        </div>

        <div class="modern-header-right">
            <?php if (isset($_SESSION['login'])): ?>
                <div class="user-info">
                    <div class="user-avatar <?php echo $isAdmin ? 'admin' : ''; ?>">
                        <?php echo $userInitials; ?>
                    </div>
                    <span>
                        <?php echo htmlspecialchars($nomeUsuario); ?>
                        <?php if ($isAdmin): ?>
                            <span class="admin-badge">ADMIN</span>
                        <?php endif; ?>
                    </span>
                </div>

                <?php if (strpos($login, '@') !== false): ?>
                    <a href="trocaSenhaUsuario.php" class="btn-login" title="Trocar Senha">
                        <ion-icon name="key-outline"></ion-icon>
                    </a>
                <?php endif; ?>

                <a href="logout.php" class="btn-logout" title="Sair do Sistema">
                    <ion-icon name="log-out-outline"></ion-icon>
                </a>
            <?php else: ?>
                <a href="login.php" class="btn-login" title="Entrar">
                    <ion-icon name="log-in-outline"></ion-icon>
                </a>
            <?php endif; ?>
        </div>
    </header>

    <!-- SIDEBAR -->
    <aside class="modern-sidebar" id="modernSidebar">
        <!-- Menu Principal -->
        <?php if (!empty($_SESSION['idPerfilFinal'])): ?>
            <div class="sidebar-section">
                <div class="sidebar-section-title">
                    <span class="section-icon">
                        <ion-icon name="apps-outline"></ion-icon>
                        <span>Menu Principal</span>
                    </span>
                    <ion-icon name="chevron-down-outline" class="toggle-icon"></ion-icon>
                </div>
                <div class="sidebar-section-content">
                    <a href="index.php" class="sidebar-item <?php echo $paginaAtual === 'index' ? 'active' : ''; ?>">
                        <ion-icon name="home-outline"></ion-icon>
                        <span>Início</span>
                    </a>
                    <a href="consultarDados.php" class="sidebar-item <?php echo $paginaAtual === 'consultarDados' ? 'active' : ''; ?>">
                        <ion-icon name="search-outline"></ion-icon>
                        <span>Pesquisar</span>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Menu Administrativo -->
        <?php if ($isAdmin): ?>
            <div class="sidebar-section">
                <div class="sidebar-section-title">
                    <span class="section-icon">
                        <ion-icon name="settings-outline"></ion-icon>
                        <span>Administração</span>
                    </span>
                    <ion-icon name="chevron-down-outline" class="toggle-icon"></ion-icon>
                </div>
                <div class="sidebar-section-content">
                    <a href="consultarConvenios.php" class="sidebar-item <?php echo $paginaAtual === 'consultarConvenios' ? 'active' : ''; ?>">
                        <ion-icon name="business-outline"></ion-icon>
                        <span>Adm Convênios</span>
                    </a>
                    <a href="consultarUsuario.php" class="sidebar-item <?php echo $paginaAtual === 'consultarUsuario' ? 'active' : ''; ?>">
                        <ion-icon name="people-outline"></ion-icon>
                        <span>Adm Usuários</span>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Menu Usuário -->
        <?php if (strpos($login, '@') !== false): ?>
            <div class="sidebar-section">
                <div class="sidebar-section-title">
                    <span class="section-icon">
                        <ion-icon name="person-outline"></ion-icon>
                        <span>Minha Conta</span>
                    </span>
                    <ion-icon name="chevron-down-outline" class="toggle-icon"></ion-icon>
                </div>
                <div class="sidebar-section-content">
                    <a href="trocaSenhaUsuario.php" class="sidebar-item <?php echo $paginaAtual === 'trocaSenhaUsuario' ? 'active' : ''; ?>">
                        <ion-icon name="key-outline"></ion-icon>
                        <span>Trocar Senha</span>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </aside>

    <!-- Mensagem do Sistema -->
    <?php if (!empty($msgSistema)): ?>
        <div class="system-message info" id="systemMessage">
            <ion-icon name="information-circle-outline"></ion-icon>
            <?php echo htmlspecialchars($msgSistema); ?>
        </div>
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('modernSidebar');
            const toggleBtn = document.getElementById('btnToggleMenu');
            const body = document.body;
            const isMobileDevice = window.innerWidth <= 768;

            // Toggle sidebar
            toggleBtn.addEventListener('click', function() {
                if (isMobileDevice) {
                    sidebar.classList.toggle('mobile-open');
                } else {
                    sidebar.classList.toggle('collapsed');
                    body.classList.toggle('sidebar-collapsed');
                }
            });

            // Toggle seções do menu
            document.querySelectorAll('.sidebar-section-title').forEach(title => {
                title.addEventListener('click', function() {
                    this.classList.toggle('collapsed');
                    const content = this.nextElementSibling;
                    if (content) {
                        content.classList.toggle('collapsed');
                    }
                });
            });

            // Fechar sidebar mobile ao clicar fora
            document.addEventListener('click', function(e) {
                if (isMobileDevice && sidebar.classList.contains('mobile-open')) {
                    if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                        sidebar.classList.remove('mobile-open');
                    }
                }
            });

            // Auto-hide mensagem do sistema
            const systemMessage = document.getElementById('systemMessage');
            if (systemMessage) {
                setTimeout(function() {
                    systemMessage.style.opacity = '0';
                    systemMessage.style.transform = 'translateX(-50%) translateY(-20px)';
                    setTimeout(function() {
                        systemMessage.remove();
                    }, 300);
                }, 5000);
            }

            // Ajustar para mobile no resize
            window.addEventListener('resize', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('collapsed');
                    body.classList.remove('sidebar-collapsed');
                }
            });
        });
    </script>
