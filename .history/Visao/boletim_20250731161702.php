<?php
session_start();

require_once 'dompdf/autoload.inc.php';
require_once '../Modelo/DAO/NotaDAO.php';
require_once '../Modelo/DTO/NotaDTO.php';
require_once '../Modelo/DAO/DisciplinaDAO.php';
require_once '../Modelo/DTO/DisciplinaDTO.php';
require_once '../Modelo/DAO/MatriculaDAO.php';
require_once '../Modelo/DTO/MatriculaDTO.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$notaDAO = new NotaDAO();
$disciplinaDAO = new DisciplinaDAO();
$matriculasDAO = new MatriculaDAO();

$idAluno = 1; // Exemplo: ID do aluno
$notas = $notaDAO->boletim($idAluno); // Retorna array com estrutura correta

$anoAtual = date("Y");
$anoAnterior = $anoAtual - 1;
$anoLectico = "$anoAnterior/$anoAtual";

$mapaNotas = [];
$nomeAluno = $responsavelAluno = $nomeCurso = $dataNascimentoAluno = "";
$nomeTurma = $classeMatricula = $periodoMatricula = "";

// Buscar matrícula uma única vez fora do loop
$matriculas = $matriculasDAO->listarPorAluno($idAluno);
$matricula = !empty($matriculas) ? $matriculas[0] : null;

if ($matricula) {
    $nomeTurma = $matricula->getNomeTurma();
    $classeMatricula = $matricula->getClasseMatricula();
    $periodoMatricula = $matricula->getPeriodoMatricula();
}

// Processar notas
foreach ($notas as $nota) {
    $nomeAluno = $nota->getNomeAluno();
    $responsavelAluno = $nota->getResponsavelAluno();
    $nomeCurso = $nota->getNomeCurso();
    $dataNascimentoAluno = $nota->getDataNascimentoAluno();

    $disciplina = $nota->getNomeDisciplina();
    $trimestre = $nota->getTrimestreNota(); // 1, 2 ou 3
    $tipoNota = $nota->getTipoNota();       // Ex: "MAC", "NP1", etc.
    $valorNota = $nota->getValorNota();

    $mapaNotas[$disciplina][$trimestre][$tipoNota] = $valorNota;

    if (in_array($tipoNota, ["MFD", "NE", "MEC", "MFA"])) {
        $mapaNotas[$disciplina]["final"][$tipoNota] = $valorNota;
    }
}

// Buscar todas as disciplinas da classe e curso
$disciplinasTodas = $disciplinaDAO->listarPorClasseCurso($classeMatricula, $nomeCurso);

// Preencher o mapa com disciplinas que não têm nota
foreach ($disciplinasTodas as $disciplinaDTO) {
    $nomeDisciplina = $disciplinaDTO->getNome();
    if (!isset($mapaNotas[$nomeDisciplina])) {
        $mapaNotas[$nomeDisciplina] = [];
    }
}

// Gerar lista final com todas as disciplinas (ordenada)
$listaDisciplinas = array_keys($mapaNotas);
sort($listaDisciplinas);

// Função segura para obter nota
function obterNota($mapaNotas, $disciplina, $trimestre, $tipo)
{
    return $mapaNotas[$disciplina][$trimestre][$tipo] ?? '-';
}

function formatarNota($nota)
{
    if ($nota === '-' || $nota === null) {
        return "<span style='color: #6c757d;'>-</span>";
    }
    $cor = ($nota >= 10) ? "#0d6efd" : "#dc3545";
    return "<span style='color: $cor; font-weight: bold'>$nota</span>";
}

// Gera HTML
ob_start();
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
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

<body>

    <div class="titulo">
        <img src="http://localhost/Sistema%20de%20Gest%c3%a3o%20de%20Alunos/Visao/imagens/logo.jpg" class="logo" alt="Logo"><br>
        <strong>República de Angola</strong><br>
        Ministério da Educação<br>
        Governo da Província de Luanda<br>
        Instituto Politécnico Privado Estrela Dourada de Belas<br>
        <h3>Boletim de Notas</h3>
    </div>

    <div class="dados">
        <div class="col">
            <strong>Aluno:</strong> <?= $nomeAluno ?><br>
            <strong>Data de Nascimento:</strong> <?= $dataNascimentoAluno ?><br>
            <strong>Encarregado:</strong> <?= $responsavelAluno ?><br>
            <strong>Ano Lectivo:</strong> <?= $anoLectico ?>
        </div>
        <div class="col">
            <strong>Curso:</strong> <?= $nomeCurso ?><br>
            <strong>Turma:</strong><?= $nomeTurma ?><br>
            <strong>Período:</strong> <?= $periodoMatricula ?><br>
            <strong>Classe:</strong> <?= $classeMatricula ?>
        </div>
    </div>




    <table class="header-tabela">
        <thead>
            <tr>
                <th rowspan="2">Disciplina</th>
                <th colspan="4">1º Trimestre</th>
                <th colspan="4">2º Trimestre</th>
                <th colspan="4">3º Trimestre</th>
                <th colspan="4">Classificação Anual</th>
            </tr>
            <tr>
                <th>MAC</th>
                <th>NP1</th>
                <th>NP2</th>
                <th style="background-color: yellow;">MT</th>
                <th>MAC</th>
                <th>NP1</th>
                <th>NP2</th>
                <th style="background-color: yellow;">MT</th>
                <th>MAC</th>
                <th>NP1</th>
                <th>NP2</th>
                <th style="background-color: yellow;">MT</th>
                <th>MFD</th>
                <th>NE</th>
                <th>MEC</th>
                <th>MFA</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listaDisciplinas as $disciplina): ?>
                <tr>
                    <td><?= $disciplina ?></td>
                    <!-- 1º Trimestre -->
                    <td><?= formatarNota(obterNota($mapaNotas, $disciplina, "1º Trimestre", "MAC")) ?></td>
                    <td><?= formatarNota(obterNota($mapaNotas, $disciplina, "1º Trimestre", "NP1")) ?></td>
                    <td><?= formatarNota(obterNota($mapaNotas, $disciplina, "1º Trimestre", "NP2")) ?></td>
                    <td style="background-color: yellow;"><?= formatarNota(obterNota($mapaNotas, $disciplina, 1, "MT")) ?></td>
                    <!-- 2º Trimestre -->
                    <td><?= formatarNota(obterNota($mapaNotas, $disciplina, "2º Trimestre", "MAC")) ?></td>
                    <td><?= formatarNota(obterNota($mapaNotas, $disciplina, "2º Trimestre", "NP1")) ?></td>
                    <td><?= formatarNota(obterNota($mapaNotas, $disciplina, "2º Trimestre", "NP2")) ?></td>
                    <td style="background-color: yellow;"><?= formatarNota(obterNota($mapaNotas, $disciplina, 2, "MT")) ?></td>
                    <!-- 3º Trimestre -->
                    <td><?= formatarNota(obterNota($mapaNotas, $disciplina, "3º Trimestre", "MAC")) ?></td>
                    <td><?= formatarNota(obterNota($mapaNotas, $disciplina, "3º Trimestre", "NP1")) ?></td>
                    <td><?= formatarNota(obterNota($mapaNotas, $disciplina, "3º Trimestre", "NP2")) ?></td>
                    <td style="background-color: yellow;"><?= formatarNota(obterNota($mapaNotas, $disciplina, 3, "MT")) ?></td>
                    <!-- Classificação Anual -->
                    <td><?= formatarNota(obterNota($mapaNotas, $disciplina, "final", "MFD")) ?></td>
                    <td><?= formatarNota(obterNota($mapaNotas, $disciplina, "final", "NE")) ?></td>
                    <td><?= formatarNota(obterNota($mapaNotas, $disciplina, "final", "MEC")) ?></td>
                    <td><?= formatarNota(obterNota($mapaNotas, $disciplina, "final", "MFA")) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>



    <div class="assinaturas">
        <div class="assinatura">
            Director de Turma
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
$dompdf->stream("boletim.pdf", ["Attachment" => false]);
