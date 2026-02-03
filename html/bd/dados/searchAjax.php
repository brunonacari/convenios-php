<?php
session_start();

// DEBUG - Ativa exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verifica se está logado
if (!isset($_SESSION['login'])) {
    echo '<div class="alert alert-error"><ion-icon name="alert-circle-outline"></ion-icon> Sessão expirada. Faça login novamente.</div>';
    echo '<!-- DEBUG: Sessão não encontrada -->';
    exit;
}

echo '<!-- DEBUG: Sessão OK - Login: ' . $_SESSION['login'] . ' -->';

include_once '../conexao.php';

// DEBUG - Verifica conexões
echo '<!-- DEBUG: pdoSICAT = ' . (isset($pdoSICAT) && $pdoSICAT !== null ? 'OK' : 'NULL') . ' -->';
echo '<!-- DEBUG: pdoCAT = ' . (isset($pdoCAT) && $pdoCAT !== null ? 'OK' : 'NULL') . ' -->';

$lgnCriador = $_SESSION['login'];

$nmCliente = filter_input(INPUT_POST, 'nmCliente', FILTER_SANITIZE_SPECIAL_CHARS);
$endCliente = filter_input(INPUT_POST, 'endCliente', FILTER_SANITIZE_SPECIAL_CHARS);
$bairroCliente = filter_input(INPUT_POST, 'bairroCliente', FILTER_SANITIZE_SPECIAL_CHARS);
$municipioCliente = filter_input(INPUT_POST, 'municipioCliente', FILTER_SANITIZE_SPECIAL_CHARS);
$documentoCliente = filter_input(INPUT_POST, 'documentoCliente', FILTER_SANITIZE_SPECIAL_CHARS);
$maxFiltro = filter_input(INPUT_POST, 'maxFiltro', FILTER_SANITIZE_SPECIAL_CHARS);

// Verifica se pelo menos um filtro foi preenchido
if (empty($nmCliente) && empty($endCliente) && empty($bairroCliente) && empty($municipioCliente) && empty($documentoCliente)) {
    echo '<div class="empty-state">
            <div class="empty-state-icon">
                <ion-icon name="search-outline"></ion-icon>
            </div>
            <h3>Realize uma pesquisa</h3>
            <p>Preencha ao menos um dos filtros acima para buscar dados de clientes.</p>
          </div>';
    exit;
}

// Define maxFiltro padrão se não informado
if (empty($maxFiltro)) {
    $maxFiltro = 100;
}

$filtroSQL = "";

if (!empty($nmCliente)) {
    $filtroSQL .= "AND CL.NOME like '%$nmCliente%' ";
}

if (!empty($endCliente)) {
    $filtroSQL .= "AND CL.LOGRADOURO_CORRESP like '%$endCliente%' ";
}

if (!empty($bairroCliente)) {
    $filtroSQL .= "AND CL.BAIRRO_CORRESP like '%$bairroCliente%' ";
}

if (!empty($municipioCliente)) {
    $filtroSQL .= "AND CL.LOCALIDADE_CORRESP like '%$municipioCliente%' ";
}

if (!empty($documentoCliente)) {
    $filtroSQL .= "AND (CL.CPF_CNPJ like '%$documentoCliente%' OR CL.CNH like '%$documentoCliente%' OR CL.RG like '%$documentoCliente%' OR CL.CTPS like '%$documentoCliente%' )";
}

$querySelect2 = "use SICAT SELECT top $maxFiltro 
                        CL.CPF_CNPJ,
                        CL.CNH, CL.RG,
                        CL.CTPS,
                        CL.CTPS_SERIE,
                        CL.CTPS_UF,
                        CL.NOME AS NOME,
                        UPPER(CL.LOGRADOURO_CORRESP) COLLATE Latin1_General_CI_AS AS Logradouro,
                        CL.NUM_END_CORRESP,
                        CL.COMPL_END_CORRESP COLLATE Latin1_General_CI_AS AS COMPL_END_CORRESP,
                        CL.CEP AS CEP,
                        CL.CEP_CORRESP AS CEP_CORRESP,
                        CL.BAIRRO_CORRESP COLLATE Latin1_General_CI_AS AS BAIRRO_CORRESP,
                        UPPER(CL.LOCALIDADE_CORRESP) COLLATE Latin1_General_CI_AS AS DC_CIDADE_CORRESP,
                        CL.ESTADO_CORRESP COLLATE Latin1_General_CI_AS AS ESTADO_CORRESP
                        FROM
                        CDACL CL (NOLOCK)
                        WHERE
                        CL.MAINT <> 'D'
                        
                        $filtroSQL

                        UNION 

                        SELECT top $maxFiltro
                        CL.CPF_CNPJ,
                        CL.CNH, CL.RG,
                        CL.CTPS,
                        CL.CTPS_SERIE,
                        CL.CTPS_UF,
                        CL.NOME AS NOME,
                        UPPER(LO.SIGLA_LOGRADOURO+' '+LO.DC_LOGRADOURO) as Logradouro,
                        IM.NUM_ENDERECO AS NUM_END_CORRESP,
                        IM.COMPL_ENDERECO AS COMPL_END_CORRESP,
                        CL.CEP, 
                        CL.CEP_CORRESP,
                        BA.DC_BAIRRO AS BAIRRO_CORRESP,
                        UPPER(CI.DC_CIDADE) AS DC_CIDADE_CORRESP,
                        CL.ESTADO_CORRESP
                        FROM
                        CDACL CL (NOLOCK)
                        INNER JOIN CDAIM IM (NOLOCK) ON CL.CD_CLIENTE = IM.CD_CLIENTE AND IM.MAINT <> 'D'
                        LEFT JOIN CDACI CI (NOLOCK) ON IM.CD_CIDADE = CI.CD_CIDADE
                        LEFT JOIN CDABA BA (NOLOCK) ON IM.CD_CIDADE = BA.CD_CIDADE AND IM.CD_BAIRRO = BA.CD_BAIRRO
                        LEFT JOIN CDALO LO (NOLOCK) ON IM.CD_CIDADE = LO.CD_CIDADE AND IM.CD_LOGRADOURO = LO.CD_LOGRADOURO
                        WHERE
                        CL.MAINT <> 'D'
                                
                        $filtroSQL
                            
                        ORDER BY CL.NOME, CL.CPF_CNPJ
                ";

try {
    // DEBUG - Mostra a query
    echo '<!-- DEBUG QUERY: ' . htmlspecialchars(substr($querySelect2, 0, 500)) . '... -->';
    
    // Verifica se conexão existe
    if (!isset($pdoSICAT) || $pdoSICAT === null) {
        echo '<div class="alert alert-error"><ion-icon name="alert-circle-outline"></ion-icon> Erro: Conexão com banco SICAT não disponível.</div>';
        exit;
    }
    
    // Executa a consulta
    $querySelect = $pdoSICAT->query($querySelect2);
    
    if (!$querySelect) {
        echo '<div class="alert alert-error"><ion-icon name="alert-circle-outline"></ion-icon> Erro: Query retornou false.</div>';
        print_r($pdoSICAT->errorInfo());
        exit;
    }
    
    echo '<!-- DEBUG: Query executada com sucesso -->';
    
    // Log de auditoria
    $variables = [
        'nmCliente' => $nmCliente,
        'endCliente' => $endCliente,
        'bairroCliente' => $bairroCliente,
        'municipioCliente' => $municipioCliente,
        'documentoCliente' => $documentoCliente,
    ];

    $filledVariables = [];
    foreach ($variables as $key => $value) {
        if (!empty($value)) {
            $filledVariables[] = "($key) $value";
        }
    }

    $resultString = implode(' | ', $filledVariables);
    $login = $_SESSION['login'];
    $tela = 'Dados';
    $acao = 'Consulta: ' . $resultString;
    $idEvento = 0;
    $queryLOG = $pdoCAT->query("INSERT INTO AUDITORIA VALUES('$login', GETDATE(), '$tela', '$acao', $idEvento)");

    $totalRegistros = $querySelect->rowCount();

    if ($totalRegistros > 0):
?>
<div class="table-info">
    <span class="results-count">
        <ion-icon name="checkmark-circle-outline"></ion-icon>
        <?php echo number_format($totalRegistros, 0, ',', '.'); ?> registro(s) encontrado(s)
    </span>
</div>

<table class="modern-table">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Endereço</th>
            <th>Nº</th>
            <th>Complemento</th>
            <th>Bairro</th>
            <th>Município</th>
            <th>CEP</th>
            <th>UF</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
            $nmClienteR = $registros['NOME'];
            $endClienteR = $registros['Logradouro'];
            $numEndCliente = $registros['NUM_END_CORRESP'];
            $complementoCliente = $registros['COMPL_END_CORRESP'];
            $bairroClienteR = $registros['BAIRRO_CORRESP'];
            $municipioClienteR = $registros['DC_CIDADE_CORRESP'];
            $ufCliente = $registros['ESTADO_CORRESP'];
            $cepCliente = $registros['CEP_CORRESP'];
        ?>
        <tr>
            <td>
                <strong><?php echo htmlspecialchars($nmClienteR); ?></strong>
            </td>
            <td><?php echo htmlspecialchars($endClienteR); ?></td>
            <td class="text-center"><?php echo htmlspecialchars($numEndCliente); ?></td>
            <td><?php echo htmlspecialchars($complementoCliente); ?></td>
            <td><?php echo htmlspecialchars($bairroClienteR); ?></td>
            <td><?php echo htmlspecialchars($municipioClienteR); ?></td>
            <td class="text-center"><?php echo htmlspecialchars($cepCliente); ?></td>
            <td class="text-center">
                <span class="badge-uf"><?php echo htmlspecialchars($ufCliente); ?></span>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
<div class="empty-state">
    <div class="empty-state-icon">
        <ion-icon name="alert-circle-outline"></ion-icon>
    </div>
    <h3>Nenhum resultado encontrado</h3>
    <p>Não foram encontrados registros com os filtros informados. Tente ajustar os critérios de busca.</p>
</div>
<?php 
    endif;
} catch (PDOException $e) {
    echo '<div class="alert alert-error">
            <ion-icon name="alert-circle-outline"></ion-icon>
            Erro ao realizar a consulta. Tente novamente.
          </div>';
    // Log do erro (comentado para produção)
    // error_log("Erro consulta dados: " . $e->getMessage());
}
?>
