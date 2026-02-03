<?php
include_once 'bd/conexao.php';
include_once 'redirecionar.php';

include_once('../../protectAdmin.php');

$querySelect2 = "SELECT * FROM [convenios].[dbo].[convenios]
                    WHERE NOME_CONVENIO NOT LIKE 'ADMINISTRADOR' 
                    AND DT_FIM_CONVENIO >= GETDATE()
                    ORDER BY [nome_convenio]";

// Executa a consulta
$querySelect = $pdoCAT->query($querySelect2);

echo "<table class='rTablePublico'>";
echo "<thead>";
echo "<tr>";

echo "<th>Órgão</th>";
echo "<th>Nº</th>";
echo "<th>Data Início</th>";
echo "<th>Data Fim</th>";

echo "<th style='text-align: center;'></th>";
echo "<th style='text-align: center;'></th>";

echo "<th style='text-align: center;'><a href='../cadConvenios.php' title='Criar Convênio' ><i class='bi bi-plus-square-fill'></i></a></th>";

echo "</tr>";
echo "</thead>";
echo "<tbody>";

// echo "<script>alert($currentDate);</script>";

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $idConvenio = $registros['ID_CONVENIO'];
    $nomeConvenio = $registros['NOME_CONVENIO'];
    $numConvenio = $registros['NUM_CONVENIO'];
    $dtIniConvenio = $registros['DT_INI_CONVENIO'];
    $dtFimConvenio = $registros['DT_FIM_CONVENIO'];

    if ($dtFimConvenio > date('Y-m-d')) {
        echo "<td>$nomeConvenio</td>";
    } else {
        echo "<td><s>$nomeConvenio</s></td>";
    }
    echo "<td>$numConvenio</td>";
    echo "<td>$dtIniConvenio</td>";
    echo "<td>$dtFimConvenio</td>";
    
    if ($nomeConvenio != 'ADMINISTRADOR') {

        echo "<td style='text-align: center;'><a href='editarConvenios.php?idConvenio=$idConvenio' title='Editar Convênio' ><i class='bi bi-gear'></i></a></td>";

        echo "<td style='text-align: center;'>
                <a href='bd/convenios/desativa.php?idConvenio=<?php echo $idConvenio; ?>' 
                   title='Desativar Convênio' 
                   style='color: red;' 
                   onclick='return confirmDesativar()'>
                    <i class='bi bi-x-circle'></i>
                </a>
            </td>";
    }
    echo "</tr>";
endwhile;

echo "</tbody>";
echo "</table>";
?>

<script>
    function confirmDesativar() {
        return confirm("Tem certeza que deseja desativar este convênio?");

    }
</script>