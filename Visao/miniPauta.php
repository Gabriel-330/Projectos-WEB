<?php
session_start();

require_once 'dompdf/autoload.inc.php';
require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/AlunoDAO.php");
require_once("../Modelo/DAO/NotaDAO.php");
require_once("../Modelo/DTO/AlunoDTO.php");
require_once("../Modelo/DTO/NotaDTO.php");
require_once '../Modelo/DAO/MatriculaDAO.php';
require_once '../Modelo/DTO/MatriculaDTO.php';
require_once '../Modelo/DAO/DocumentoDAO.php';
use Dompdf\Dompdf;
use Dompdf\Options;
$DAO = new DocumentoDAO();
$alunoDAO = new AlunoDAO();
$notaDAO = new NotaDAO();
$matriculasDAO = new MatriculaDAO();
$doc = $DAO->mostrarDocumentoPorCPCT();

$anoAtual = date("Y");
$anoAnterior = $anoAtual - 1;
$anoLectico = "$anoAnterior/$anoAtual";
foreach($doc as $documento){
$classe = $documento->getClasseDocumento();
$periodo =$documento->getPeriodoDocumento() ;
$curso = $documento->getCursoDocumento();
$turma = $documento->getTurmaDocumento();
$disciplinaFiltrada = $documento->getDisciplinaDocumento();
}


 // <-- Altere para a disciplina desejada

// Obter lista de todos os alunos por classe, periodo, curso e turma
$matriculas = $matriculasDAO->listarMatriculaPorCPCT($classe, $periodo, $curso, $turma);

// Obter notas de todos os alunos
$notas = $notaDAO->listarPorDC($disciplinaFiltrada, $curso);

// Mapa de notas no formato: $mapaNotas[disciplina][trimestre][tipo]
$mapaNotas = [];

$matricula = !empty($matriculas) ? $matriculas[0] : null;

if ($matricula) {
    $nomeCurso = $matricula->getNomeCurso();
    $nomeTurma = $matricula->getNomeTurma();
    $classeMatricula = $matricula->getClasseMatricula();
    $periodoMatricula = $matricula->getPeriodoMatricula();
}

foreach ($notas as $nota) {

    $disciplina = $nota->getNomeDisciplina();
    $idAluno = $nota->getIdAluno();
    $trimestre = $nota->getTrimestreNota();
    $tipo = $nota->getTipoNota();
    $valor = $nota->getValorNota();

    $mapaNotas[$idAluno][$disciplina][$trimestre][$tipo] = $valor;

    // Se for tipo final (MT1, MT2, MFD etc), armazena também
    if (in_array($tipo, ["MT1P", "MT2", "MT3", "MFD", "MPP", "MFA", "MEC"])) {
        $mapaNotas[$idAluno][$disciplina]['final'][$tipo] = $valor;
    }
}


foreach ($mapaNotas as $idAluno => &$disciplinas) {
    foreach ($disciplinas as $disciplina => &$trimestres) {
        foreach (["1º Trimestre", "2º Trimestre", "3º Trimestre"] as $i => $trimestre) {
            $mac = $trimestres[$trimestre]['MAC'] ?? null;
            $npp = $trimestres[$trimestre]['NPP'] ?? null;
            $npt = $trimestres[$trimestre]['NPT'] ?? null;

            if (is_numeric($mac) && is_numeric($npp) && is_numeric($npt)) {
                $media = ($mac + $npp + $npt) / 3;
                $trimestres[$trimestre]["MT"] = round($media);
                $trimestres['final']["MT" . ($i + 1)] = round($media); // MT1, MT2, MT3
            } else {
                $trimestres[$trimestre]["MT"] = null;
            }
        }
    }
}

// Calcular MFD (Média Final do Discente)
foreach ($mapaNotas as $idAluno => &$disciplinas) {
    foreach ($disciplinas as $disciplina => &$trimestres) {
        $mt1 = $trimestres['final']['MT1'] ?? null;
        $mt2 = $trimestres['final']['MT2'] ?? null;
        $mt3 = $trimestres['final']['MT3'] ?? null;

        if (is_numeric($mt1) && is_numeric($mt2) && is_numeric($mt3)) {
            $mediaFinal = ($mt1 + $mt2 + $mt3) / 3;
            $trimestres['final']['MFD'] = round($mediaFinal); // arredondamento padrão
        } else {
            $trimestres['final']['MFD'] = null;
        }
    }
}

// Função segura para obter nota
function obterNota($mapaNotas, $disciplina, $trimestre, $tipo)
{
    return $mapaNotas[$disciplina][$trimestre][$tipo] ?? '-';
}

// Função para formatar a nota com cor
function formatarNota($nota)
{
    if ($nota === '-' || $nota === null) {
        return "<span style='color: #6c757d;'>-</span>";
    }
    $cor = ($nota >= 10) ? "#0d6efd" : "#dc3545";
    return "<span style='color: $cor; font-weight: bold'>$nota</span>";
}

// Gerar HTML
ob_start();
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Mini Pauta</title>
    <style>
        body {

            font-family: Arial;
            font-size: 12px;
            margin: 10px;
        }

        .titulo {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo {
            width: 60px;
        }

        .dados {
            border: 0.5px solid #000;
            padding: 10px;
            margin-bottom: 15px;
            justify-content: space-between;
        }

        .dados .col {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }

        .dados .col+.col {
            border-left: 0.5px solid #000;
            padding-left: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 0.5px solid #000;
            padding: 3px;
            text-align: center;
        }

        .assinaturas {
            margin-top: 30px;
            width: 100%;
        }

        .assinatura {
            width: 30%;
            margin-top: 15px;
            display: inline-block;
            text-align: center;
        }

        .assinatura hr {
            margin-top: 32px;
            border: none;
            border-top: 0.5px solid #000;
        }

        .header-tabela th {
            background-color: #d9efff;
        }
    </style>
</head>

<body class="container mt-4">
    <div class="titulo">
        <img src="http://localhost/Sistema%20de%20Gest%c3%a3o%20de%20Alunos/Visao/imagens/logo.jpg" class="logo" alt="Logo"><br>
        <strong>República de Angola</strong><br>
        Ministério da Educação<br>
        Governo da Província de Luanda<br>
        Instituto Politécnico Privado Estrela Dourada de Belas<br>
        <h3>Mini Pauta</h3>
    </div>

    <div class="dados">
        <div class="col">
            <strong>Disciplina:</strong> <?= $disciplinaFiltrada ?><br>
            <strong>Turma:</strong> <?= $turma ?><br>
            <strong>Classe:</strong> <?= $classe ?><br>

        </div>
        <div class="col">

            <strong>Curso:</strong> <?= $curso ?><br>
            <strong>Periodo:</strong> <?= $periodo?><br>
            <strong>Ano lectivo:</strong> <?= $anoLectico ?>
        </div>

    </div>
    <table class="header-tabela">
        <thead>
            <tr>
                <th rowspan="2">Aluno</th>
                <th colspan="4">1º Trimestre</th>
                <th colspan="4">2º Trimestre</th>
                <th colspan="4">3º Trimestre</th>
                <th colspan="4">Classificação Geral</th>
            </tr>
            <tr>
                <th>MAC</th>
                <th>NPP</th>
                <th>NPT</th>
                <th>MT1</th>
                <th>MAC</th>
                <th>NPP</th>
                <th>NPT</th>
                <th>MT2</th>
                <th>MAC</th>
                <th>NPP</th>
                <th>NPT</th>
                <th>MT3</th>
                <th>MT1</th>
                <th>MT2</th>
                <th>MT3</th>
                <th>MFD</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($matriculas as $aluno): ?>
                <?php
                $idAluno = $aluno->getIdAluno();
                $nomeAluno = $aluno->getNomeAluno();
                ?>
                <tr>
                    <td><?= htmlspecialchars($nomeAluno) ?></td>

                    <?php foreach (["1º Trimestre", "2º Trimestre", "3º Trimestre"] as $i => $trimestre): ?>
                        <td><?= formatarNota($mapaNotas[$idAluno][$disciplinaFiltrada][$trimestre]['MAC'] ?? null) ?></td>
                        <td><?= formatarNota($mapaNotas[$idAluno][$disciplinaFiltrada][$trimestre]['NPP'] ?? null) ?></td>
                        <td><?= formatarNota($mapaNotas[$idAluno][$disciplinaFiltrada][$trimestre]['NPT'] ?? null) ?></td>
                        <td style="background-color: yellow;"><?= formatarNota($mapaNotas[$idAluno][$disciplinaFiltrada][$trimestre]['MT'] ?? null) ?></td>
                    <?php endforeach; ?>

                    <td><?= formatarNota($mapaNotas[$idAluno][$disciplinaFiltrada]['final']['MT1'] ?? null) ?></td>
                    <td><?= formatarNota($mapaNotas[$idAluno][$disciplinaFiltrada]['final']['MT2'] ?? null) ?></td>
                    <td><?= formatarNota($mapaNotas[$idAluno][$disciplinaFiltrada]['final']['MT3'] ?? null) ?></td>
                    <td><?= formatarNota($mapaNotas[$idAluno][$disciplinaFiltrada]['final']['MFD'] ?? null) ?></td>

                <?php endforeach; ?>
        </tbody>
    </table>

    <div class="assinaturas">
        <div class="assinatura">
            Professor
            <hr>
        </div>
        <div class="assinatura" style="float: right;">
            Director Pedagógico
            <hr>
        </div>
    </div>
</body>

</html>
<?php
$html = ob_get_clean();

// Configurações Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Arial');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape'); // Horizontal
$dompdf->render();
$dompdf->stream("miniPauta.pdf", ["Attachment" => false]);
