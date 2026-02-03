<?php

include_once 'bd/conexao.php';
include_once 'redirecionar.php';

$lgnCriador = $_SESSION['login'];

$nmCliente = filter_input(INPUT_POST, 'nmCliente', FILTER_SANITIZE_SPECIAL_CHARS);
$endCliente = filter_input(INPUT_POST, 'endCliente', FILTER_SANITIZE_SPECIAL_CHARS);
$bairroCliente = filter_input(INPUT_POST, 'bairroCliente', FILTER_SANITIZE_SPECIAL_CHARS);
$municipioCliente = filter_input(INPUT_POST, 'municipioCliente', FILTER_SANITIZE_SPECIAL_CHARS);
$documentoCliente = filter_input(INPUT_POST, 'documentoCliente', FILTER_SANITIZE_SPECIAL_CHARS);
$maxFiltro = filter_input(INPUT_POST, 'maxFiltro', FILTER_SANITIZE_SPECIAL_CHARS);

// var_dump($tipoLicitacao);

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

$querySelect2 = "SELECT top $maxFiltro 
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
                        --CL.VALIDADO,
                        --CL.ID_RESP_VALIDACAO
                        FROM
                        CDACL CL (NOLOCK)
                        WHERE
                        CL.MAINT <> 'D'
                        
                        $filtroSQL

                        UNION 

                        SELECT top $maxFiltro
                        --CL.CD_CLIENTE,
                        --CL.DV,
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
                        --CL.VALIDADO,
                        --CL.ID_RESP_VALIDACAO
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

// // Executa a consulta
$querySelect = $pdoSICAT->query($querySelect2);
// var_dump($querySelect);
// exit();

$variables = [
    'nmCliente' => $nmCliente,
    'endCliente' => $endCliente,
    'bairroCliente' => $bairroCliente,
    'municipioCliente' => $municipioCliente,
    'documentoCliente' => $documentoCliente,
];

$filledVariables = [];

foreach ($variables as $key => $value) {
    switch ($key) {
        case 'nmCliente':
            if (!empty($value)) {
                $filledVariables[] = '(nmCliente) ' . $value;
            }
            break;
        case 'endCliente':
            if (!empty($value)) {
                $filledVariables[] = '(endCliente) ' . $value;
            }
            break;
        case 'bairroCliente':
            if (!empty($value)) {
                $filledVariables[] = '(bairroCliente) ' . $value;
            }
            break;
        case 'municipioCliente':
            if (!empty($value)) {
                $filledVariables[] = '(municipioCliente) ' . $value;
            }
            break;
        case 'documentoCliente':
            if (!empty($value)) {
                $filledVariables[] = '(documentoCliente) ' . $value;
            }
            break;
        default:
            break;
    }
}

$resultString = implode(' | ', $filledVariables);

$login = $_SESSION['login'];
$tela = 'Dados';
$acao = 'Consulta: ' . $resultString;
$idEvento = 0;
$queryLOG = $pdoCAT->query("INSERT INTO AUDITORIA VALUES('$login', GETDATE(), '$tela', '$acao', $idEvento)");

// var_dump($resultString);
// exit;

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
            $nmCliente = $registros['NOME'];
            $endCliente = $registros['Logradouro'];
            $numEndCliente = $registros['NUM_END_CORRESP'];
            $complementoCliente = $registros['COMPL_END_CORRESP'];
            $bairroCliente = $registros['BAIRRO_CORRESP'];
            $municipioCliente = $registros['DC_CIDADE_CORRESP'];
            $ufCliente = $registros['ESTADO_CORRESP'];
            $cepCliente = $registros['CEP_CORRESP'];
        ?>
        <tr>
            <td>
                <strong><?php echo htmlspecialchars($nmCliente); ?></strong>
            </td>
            <td><?php echo htmlspecialchars($endCliente); ?></td>
            <td class="text-center"><?php echo htmlspecialchars($numEndCliente); ?></td>
            <td><?php echo htmlspecialchars($complementoCliente); ?></td>
            <td><?php echo htmlspecialchars($bairroCliente); ?></td>
            <td><?php echo htmlspecialchars($municipioCliente); ?></td>
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
<?php endif; ?>