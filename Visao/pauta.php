<?php
session_start();

require_once 'dompdf/autoload.inc.php';
require_once("../Modelo/DAO/conn.php");
require_once("../Modelo/DAO/AlunoDAO.php");
require_once("../Modelo/DAO/NotaDAO.php");
require_once("../Modelo/DTO/AlunoDTO.php");
require_once '../Modelo/DTO/DisciplinaDTO.php';
require_once '../Modelo/DAO/DisciplinaDAO.php';
require_once '../Modelo/DAO/MatriculaDAO.php';
require_once("../Modelo/DTO/NotaDTO.php");
require_once '../Modelo/DTO/MatriculaDTO.php';
require_once '../Modelo/DAO/CursoDAO.php';
require_once '../Modelo/DTO/CursoDTO.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$alunoDAO = new AlunoDAO();
$notaDAO = new NotaDAO();
$matriculasDAO = new MatriculaDAO();
$disciplinaDAO = new DisciplinaDAO();
$cursoDAO = new CursoDAO();

$anoAtual = date("Y");
$anoAnterior = $anoAtual - 1;
$anoLectico = "$anoAnterior/$anoAtual";

$classe = "12";
$periodo = "Tarde";
$curso = "Informática";
$turma = "A";

$idCurso = $cursoDAO->buscarIdPorNomeCurso($curso);
$disciplinasFixas = $disciplinaDAO->listarDisciplinaPorCurso($idCurso);

// Obter alunos
$matriculas = $matriculasDAO->listarMatriculaPorCPCT($classe, $periodo, $curso, $turma);

// Obter todas as notas para o curso
$notas = $notaDAO->listarPorCurso($curso);

// Construir o mapa de notas
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

    if (in_array($tipo, ["MT1P", "MT2", "MT3", "MFD", "MPP", "MFA", "MEC"])) {
        $mapaNotas[$idAluno][$disciplina]['final'][$tipo] = $valor;
    }
}

// Cálculo das médias
foreach ($matriculas as $aluno) {
    $idAluno = $aluno->getIdAluno();

    foreach ($disciplinasFixas as $disciplina) {
        if (!isset($mapaNotas[$idAluno][$disciplina->getNomeDisciplina()])) {
            $mapaNotas[$idAluno][$disciplina->getNomeDisciplina()] = [];
        }

        $trimestres = &$mapaNotas[$idAluno][$disciplina->getNomeDisciplina()];

        foreach (["1º Trimestre", "2º Trimestre", "3º Trimestre"] as $i => $trimestre) {
            $mac = $trimestres[$trimestre]['MAC'] ?? null;
            $npp = $trimestres[$trimestre]['NPP'] ?? null;
            $npt = $trimestres[$trimestre]['NPT'] ?? null;

            if (is_numeric($mac) && is_numeric($npp) && is_numeric($npt)) {
                $media = ($mac + $npp + $npt) / 3;
                $trimestres[$trimestre]["MT"] = round($media);
                $trimestres['final']["MT" . ($i + 1)] = round($media);
            } else {
                $trimestres[$trimestre]["MT"] = null;
            }

            if (isset($trimestres[$trimestre]['PG']) && is_numeric($trimestres[$trimestre]['PG'])) {
                $trimestres['final']['PG'] = $trimestres[$trimestre]['PG'];
            }
        }

        $mt2 = $trimestres['final']['MT2'] ?? null;
        $mt3 = $trimestres['final']['MT3'] ?? null;
        $pg = $trimestres['final']['PG'] ?? null;

        if (is_numeric($mt2) && is_numeric($mt3)) {
            $cf = ($mt2 + $mt3) / 2;
            $cfx = $cf * 0.6;
            $trimestres['final']['CF'] = round($cf);
            $trimestres['final']['CFX'] = round($cfx);
        }

        if (is_numeric($pg)) {
            $pgx = $pg * 0.4;
            $trimestres['final']['PGX'] = round($pgx);
        }

        if (isset($trimestres['final']['CFX'], $trimestres['final']['PGX'])) {
            $pa = ($trimestres['final']['CFX'] + $trimestres['final']['PGX']);
            $trimestres['final']['CA'] = round($pa);
        }
    }
}

// Classificação final de cada aluno
$classificacoesAlunos = [];

foreach ($matriculas as $aluno) {
    $idAluno = $aluno->getIdAluno();
    $contadorNegativas = 0;
    $recursos = [];
    $notasEmFalta = false;

    foreach ($disciplinasFixas as $disciplina) {
        $nomeDisciplina = $disciplina->getNomeDisciplina();
        $ca = $mapaNotas[$idAluno][$nomeDisciplina]['final']['CA'] ?? null;

        if (!is_numeric($ca)) {
            $notasEmFalta = true;
            break; // não precisa verificar mais disciplinas
        }

        if ($ca < 10) {
            $contadorNegativas++;
            $recursos[] = $nomeDisciplina;
        }
    }

    if ($notasEmFalta) {
        $classificacoesAlunos[$idAluno] = [
            "texto" => "Notas em falta",
            "cor" => "#dc3545"
        ];
    } elseif ($contadorNegativas > 4) {
        $classificacoesAlunos[$idAluno] = [
            "texto" => "Reprovado",
            "cor" => "#dc3545"
        ];
    } elseif ($contadorNegativas > 0) {
        $classificacoesAlunos[$idAluno] = [
            "texto" => "Recurso: " . implode(", ", $recursos),
            "cor" => "#dc3545"
        ];
    } else {
        $classificacoesAlunos[$idAluno] = [
            "texto" => "Aprovado",
            "cor" => "#0d6efd"
        ];
    }
}

// Função para formatar nota
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
    <title>Pauta</title>
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
        <h3>Pauta</h3>
    </div>

    <div class="dados">
        <div class="col">
            <strong>Classe:</strong> <?= $classeMatricula ?><br>
            <strong>Turma:</strong> <?= $nomeTurma ?><br>
        </div>
        <div class="col">
            <strong>Curso:</strong> <?= $nomeCurso ?><br>
            <strong>Período:</strong> <?= $periodoMatricula ?><br>
            <strong>Ano Lectivo:</strong> <?= $anoLectico ?><br>
        </div>
    </div>

    <table class="header-tabela">
        <thead>
            <tr>
                <th rowspan="2">Aluno</th>
                <?php foreach ($disciplinasFixas as $disciplina): ?>
                    <th colspan="6"><?= htmlspecialchars($disciplina->getNomeDisciplina()) ?></th>
                <?php endforeach; ?>
                <th rowspan="2">Classificação</th>
            </tr>
            <tr>
                <?php foreach ($disciplinasFixas as $disciplina): ?>
                    <th>MT1</th>
                    <th>MT2</th>
                    <th>MT3</th>
                    <th>CF</th>
                    <th>PG</th>
                    <th>CA</th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($matriculas as $aluno): ?>
                <?php
                $idAluno = $aluno->getIdAluno();
                $nomeAluno = $aluno->getNomeAluno();
                $classificacao = $classificacoesAlunos[$idAluno] ?? ['texto' => '-', 'cor' => '#6c757d'];
                ?>
                <tr>
                    <td style="width: 200px;"><?= htmlspecialchars($nomeAluno) ?></td>
                    <?php foreach ($disciplinasFixas as $disciplina): ?>
                        <td><?= formatarNota($mapaNotas[$idAluno][$disciplina->getNomeDisciplina()]['final']['MT1'] ?? null) ?></td>
                        <td><?= formatarNota($mapaNotas[$idAluno][$disciplina->getNomeDisciplina()]['final']['MT2'] ?? null) ?></td>
                        <td><?= formatarNota($mapaNotas[$idAluno][$disciplina->getNomeDisciplina()]['final']['MT3'] ?? null) ?></td>
                        <td><?= formatarNota($mapaNotas[$idAluno][$disciplina->getNomeDisciplina()]['final']['CF'] ?? null) ?></td>
                        <td><?= formatarNota($mapaNotas[$idAluno][$disciplina->getNomeDisciplina()]['final']['PG'] ?? null) ?></td>
                        <td style="background-color: yellow;"><?= formatarNota($mapaNotas[$idAluno][$disciplina->getNomeDisciplina()]['final']['CA'] ?? null) ?></td>
                    <?php endforeach; ?>
                    <td style="color: <?= $classificacao['cor'] ?>; font-weight: bold; text-transform: uppercase;">
                        <?= htmlspecialchars($classificacao['texto']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>


    <div class="assinaturas">
        <div class="assinatura">
            DIRECTOR DE TURMA
            <hr>
        </div>
        <div class="assinatura" style="float: right;">
            DIRECTOR PEDAGÓGICO
            <hr>
        </div>
    </div>
</body>

</html>
<?php
$html = ob_get_clean();

// Gerar PDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Arial');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A3', 'landscape');
$dompdf->render();
$dompdf->stream("pauta.pdf", ["Attachment" => false]);
