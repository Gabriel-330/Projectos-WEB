<?php
session_start();
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");

// Verifica se o utilizador está autenticado
if (!isset($_SESSION['idUtilizador']) || !isset($_SESSION['acesso'])) {
    header("Location: index.php"); // Redireciona para login se não estiver autenticado
    exit();
}

$acesso = strtoupper($_SESSION['acesso']);
$professorDAO = new ProfessorDAO();
$professor = $professorDAO->buscarPorUtilizador($_SESSION['idUtilizador']);

// Verifica se o acesso é email de admin válido (ex: termina com @admin.estrela.com)
if (!preg_match('/^[0-9]{9}[A-Z]{2}[0-9]{3}$/', $acesso)) {    // Se não for admin, redireciona para página de acesso negado ou login
    // Se não for admin, redireciona para página de acesso negado ou login
    $_SESSION['success'] = "Acesso negado! Apenas professores podem aceder.";
    $_SESSION['icon'] = "error";
    header("Location: index.php");
    exit();
}

// Se chegou aqui, é admin autenticado e pode continuar
$usuarioId = $_SESSION['idUtilizador'];

?>


<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <!-- Icones do site-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Estilo da página-->
    <link rel="stylesheet" href="css/style-home.css">

    <!--Icone da página-->
    <link rel="icon" href="imagens/graduate-cap-icone-head.png" type="image/png">
    <script src="js/alertsMessage.js"></script>
    <script src="js/sweetalert.js"></script>

    <link href="assets/vendor/chartist/css/chartist.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.css" rel="stylesheet" type="text/css" />

    <style>
        .campo-condicional {
            opacity: 0;
            max-height: 0;
            overflow: hidden;
            transition: opacity 2s ease, max-height 2s ease;
        }

        .campo-condicional.visivel {
            opacity: 1;
            max-height: 500px;
            /* ou um valor suficiente para o conteúdo */
        }

        .menu-user ul li.active a {
            background-color: #0b5ed7;
            /* cor de fundo ao clicar */
            color: white;
            /* cor do ícone ao clicar */
            border-radius: 5px;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const menuItems = document.querySelectorAll(".menu-user ul li");

            menuItems.forEach(function(item) {
                item.addEventListener("click", function() {
                    // Remove a classe 'active' de todos os itens
                    menuItems.forEach(i => i.classList.remove("active"));
                    // Adiciona a classe 'active' ao item clicado
                    item.classList.add("active");
                });
            });
        });
    </script>

</head>

<body onload="initProgressBars()">
    <?php
    // Verifica se a variável de sessão com a mensagem está definida
    if (isset($_SESSION['success']) && $_SESSION['success'] != '') {

    ?>
        <script>
            swal({
                title: '<?php echo $_SESSION['success']; ?>',
                icon: '<?php echo $_SESSION['icon']; ?>',
                button: "Ok",
            });
        </script>
    <?php

        unset($_SESSION['success']);
    }
    ?>
    <div class="header">
        <div class="header-content">
            <nav class="navbar navbar-expand">
                <div class="collapse navbar-collapse justify-content-between">
                    <div class="header-left">
                    </div>
                    <?php
                    function traduzMes($mesNumero)
                    {
                        $meses = [
                            '01' => 'Janeiro',
                            '02' => 'Fevereiro',
                            '03' => 'Março',
                            '04' => 'Abril',
                            '05' => 'Maio',
                            '06' => 'Junho',
                            '07' => 'Julho',
                            '08' => 'Agosto',
                            '09' => 'Setembro',
                            '10' => 'Outubro',
                            '11' => 'Novembro',
                            '12' => 'Dezembro',
                        ];

                        return $meses[$mesNumero] ?? $mesNumero;
                    }
                    $dao = new NotificacoesDAO();
                    $n_Notificacoes = $dao->contarNotificacaoes();
                    $mensagens = $dao->mostrarProfessorNotificacoes($_SESSION['perfilUtilizador']);

                    ?>
                    <ul class="navbar-nav header-right">
                        <li class="nav-item dropdown notification_dropdown">
                            <a class="nav-link  ai-icon" href="javascript:void(0)" role="button" data-bs-toggle="dropdown">
                                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M22.75 15.8385V13.0463C22.7471 10.8855 21.9385 8.80353 20.4821 7.20735C19.0258 5.61116 17.0264 4.61555 14.875 4.41516V2.625C14.875 2.39294 14.7828 2.17038 14.6187 2.00628C14.4546 1.84219 14.2321 1.75 14 1.75C13.7679 1.75 13.5454 1.84219 13.3813 2.00628C13.2172 2.17038 13.125 2.39294 13.125 2.625V4.41534C10.9736 4.61572 8.97429 5.61131 7.51794 7.20746C6.06159 8.80361 5.25291 10.8855 5.25 13.0463V15.8383C4.26257 16.0412 3.37529 16.5784 2.73774 17.3593C2.10019 18.1401 1.75134 19.1169 1.75 20.125C1.75076 20.821 2.02757 21.4882 2.51969 21.9803C3.01181 22.4724 3.67904 22.7492 4.375 22.75H9.71346C9.91521 23.738 10.452 24.6259 11.2331 25.2636C12.0142 25.9013 12.9916 26.2497 14 26.2497C15.0084 26.2497 15.9858 25.9013 16.7669 25.2636C17.548 24.6259 18.0848 23.738 18.2865 22.75H23.625C24.321 22.7492 24.9882 22.4724 25.4803 21.9803C25.9724 21.4882 26.2492 20.821 26.25 20.125C26.2486 19.117 25.8998 18.1402 25.2622 17.3594C24.6247 16.5786 23.7374 16.0414 22.75 15.8385Z" fill="#007bff" />
                                </svg>

                                <span class="badge light text-white bg-primary"><?= $n_Notificacoes ?></span>

                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <div id="DZ_W_Notification1" class="widget-media dz-scroll p-3 height380">

                                    <ul class="timeline m-0 p-0">
                                        <?php foreach ($mensagens as $mensagem): ?>
                                            <?php
                                            $texto = strtolower($mensagem->getMensagemNotificacoes());
                                            $icone = 'fa-info-circle'; // padrão
                                            $cor = 'media-secondary'; // cor padrão

                                            switch (true) {
                                                case str_contains($texto, 'curso'):
                                                    $icone = 'fa-book';
                                                    $cor = 'media-primary';
                                                    break;
                                                case str_contains($texto, 'evento'):
                                                    $icone = 'fa-calendar';
                                                    $cor = 'media-info';
                                                    break;
                                                case str_contains($texto, 'nota'):
                                                    $icone = 'fa-clipboard-check';
                                                    $cor = 'media-warning';
                                                    break;
                                                case str_contains($texto, 'aluno'):
                                                    $icone = 'fa-user-graduate';
                                                    $cor = 'media-success';
                                                    break;
                                                case str_contains($texto, 'horario'):
                                                    $icone = 'fa-clock';
                                                    $cor = 'media-danger';
                                                    break;
                                                case str_contains($texto, 'disciplina'):
                                                    $icone = 'fa-book-open';
                                                    $cor = 'media-primary';
                                                    break;
                                                case str_contains($texto, 'matricula'):
                                                    $icone = 'fa-id-badge';
                                                    $cor = 'media-warning';
                                                    break;
                                                case str_contains($texto, 'professor'):
                                                    $icone = 'fa-chalkboard-teacher';
                                                    $cor = 'media-dark';
                                                    break;
                                            }
                                            ?>

                                            <li>
                                                <div class="timeline-panel d-flex align-items-start">
                                                    <div class="media me-2 <?= $cor ?>">
                                                        <i class="fa <?= $icone ?>"></i>
                                                    </div>
                                                    <div class="media-body">
                                                        <h6 class="mb-1"><?= $mensagem->getMensagemNotificacoes() ?></h6>
                                                        <small class="d-block">
                                                            <?php
                                                            $data = new DateTime($mensagem->getDataNotificacoes());
                                                            echo $data->format('d') . ' ' . traduzMes($data->format('m')) . ' ' . $data->format('Y');
                                                            ?>
                                                        </small>

                                                    </div>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>


                                </div>
                                <a class="all-notification" href="javascript:void(0)">See all notifications <i class="ti-arrow-right"></i></a>
                            </div>
                        </li>
                        <li class="nav-item dropdown header-profile">
                            <a class="nav-link" href="javascript:void(0)" role="button" data-bs-toggle="dropdown">
                                <img src="imagens/perfil.png" width="20" alt="">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="editarPerfil.html" class="dropdown-item ai-icon">
                                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M22.75 15.8385V13.0463C22.7471 10.8855 21.9385 8.80353 20.4821 7.20735C19.0258 5.61116 17.0264 4.61555 14.875 4.41516V2.625C14.875 2.39294 14.7828 2.17038 14.6187 2.00628C14.4546 1.84219 14.2321 1.75 14 1.75C13.7679 1.75 13.5454 1.84219 13.3813 2.00628C13.2172 2.17038 13.125 2.39294 13.125 2.625V4.41534C10.9736 4.61572 8.97429 5.61131 7.51794 7.20746C6.06159 8.80361 5.25291 10.8855 5.25 13.0463V15.8383C4.26257 16.0412 3.37529 16.5784 2.73774 17.3593C2.10019 18.1401 1.75134 19.1169 1.75 20.125C1.75076 20.821 2.02757 21.4882 2.51969 21.9803C3.01181 22.4724 3.67904 22.7492 4.375 22.75H9.71346C9.91521 23.738 10.452 24.6259 11.2331 25.2636C12.0142 25.9013 12.9916 26.2497 14 26.2497C15.0084 26.2497 15.9858 25.9013 16.7669 25.2636C17.548 24.6259 18.0848 23.738 18.2865 22.75H23.625C24.321 22.7492 24.9882 22.4724 25.4803 21.9803C25.9724 21.4882 26.2492 20.821 26.25 20.125C26.2486 19.117 25.8998 18.1402 25.2622 17.3594C24.6247 16.5786 23.7374 16.0414 22.75 15.8385Z" fill="#007bff" />
                                    </svg>
                                    <span class="ms-2">Perfil </span>
                                </a>
                                <a href="../Controle/terminarSessao.php" class="dropdown-item ai-icon">
                                    <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                        <polyline points="16 17 21 12 16 7"></polyline>
                                        <line x1="21" y1="12" x2="9" y2="12"></line>
                                    </svg>
                                    <span class="ms-2">Logout </span>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        <nav class="menu-user">
            <div class="menu-content">
                <i class="fa-solid fa-user-graduate user-photo"></i>
            
            </div>
        </nav>
    </div>

    <div class="content-body" style="margin-top: -30px;">
        <div class="container-fluid">
            <div class="page-titles">
                <h4>Lançar Nota</h4>

                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Professor</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Lançar Nota</a></li>

                </ol>
            </div>

            <?php
            require_once("../Modelo/DAO/NotaDAO.php");

            $dao = new NotaDAO();
            $palavra = $_GET['palavra'] ?? '';

            // Verificar se algum filtro foi aplicado
            if ($palavra) {
                // Se filtros estiverem preenchidos, realizar pesquisa
                $notas = $dao->pesquisar($palavra);
            } else {
                // Caso contrário, exibir todos os notas
                $notas = $dao->buscarPorProfessor($professor);
            }
            ?>
            <form action="notaProfessorBase.php" method="GET">
                <div class="col-md-5">
                    <div class="form-group d-flex">
                        <input type="text" class="form-control input-rounded flex-grow-1 me-2" style="width: 200px;" placeholder="Pesquisar notas..." name="palavra" value="<?= htmlspecialchars($palavra) ?>">

                        <button type="submit" class="btn btn-primary btn-rounded" style="flex-shrink: 0;">
                            <i class="fa fa-search mr-2"></i> Buscar
                        </button>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <!-- Cabeçalho com botão -->
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Notas por Trimestre</h4>
                            <a href="../Controle/crudNota.php" class="btn btn-primary btn-rounded" data-bs-toggle="modal" data-bs-target="#modalNotaCadastrar">+ Adicionar nota</a>
                        </div>

                        <div class="card-body">
                            <?php
                            $notasPorTrimestre = [];

                            function normalizarTrimestre($valor)
                            {
                                $valor = trim(strtolower($valor));
                                return match ($valor) {
                                    '1', '1º', 'primeiro', '1º trimestre', 'primeiro trimestre' => '1º Trimestre',
                                    '2', '2º', 'segundo', '2º trimestre', 'segundo trimestre' => '2º Trimestre',
                                    '3', '3º', 'terceiro', '3º trimestre', 'terceiro trimestre' => '3º Trimestre',
                                    '-', 'exame', 'exame final' => 'Exame',
                                    default => $valor ?: 'Outro',
                                };
                            }

                            function idSeguro($str)
                            {
                                return preg_replace('/[^a-z0-9]/', '', strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $str)));
                            }

                            foreach ($notas as $nota) {
                                $trimestreFormatado = normalizarTrimestre($nota->getTrimestreNota());
                                $notasPorTrimestre[$trimestreFormatado][] = $nota;
                            }

                            uksort($notasPorTrimestre, function ($a, $b) {
                                $ordem = ['1º Trimestre' => 1, '2º Trimestre' => 2, '3º Trimestre' => 3, 'Exame' => 4];
                                return ($ordem[$a] ?? 99) <=> ($ordem[$b] ?? 99);
                            });
                            ?>

                            <!-- Nav Tabs -->
                            <ul class="nav nav-tabs mt-3" id="trimestreTabs" role="tablist">
                                <?php $active = true;
                                foreach ($notasPorTrimestre as $trimestre => $notasTrimestre):
                                    $idTab = idSeguro($trimestre); ?>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link <?= $active ? 'active' : '' ?>" id="tab-<?= $idTab ?>-tab"
                                            data-bs-toggle="tab" data-bs-target="#tab-<?= $idTab ?>"
                                            type="button" role="tab">
                                            <?= htmlspecialchars($trimestre) ?>
                                        </button>
                                    </li>
                                <?php $active = false;
                                endforeach; ?>
                            </ul>

                            <!-- Tab Contents -->
                            <div class="tab-content p-3 border border-top-0 rounded-bottom shadow-sm bg-white" id="trimestreTabsContent">
                                <?php $active = true;
                                foreach ($notasPorTrimestre as $trimestre => $notasTrimestre):
                                    $idTab = idSeguro($trimestre); ?>
                                    <div class="tab-pane fade <?= $active ? 'show active' : '' ?>" id="tab-<?= $idTab ?>" role="tabpanel">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>ALUNO</th>
                                                        <th>CURSO</th>
                                                        <th>DISCIPLINA</th>
                                                        <th>VALOR</th>
                                                        <th>DATA DE AVALIAÇÃO</th>
                                                        <th>TIPO DE AVALIAÇÃO</th>
                                                        <th>TIPO NOTA</th>
                                                        <th>PERÍODO</th>
                                                        <th>AÇÕES</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $cont = 1;
                                                    foreach ($notasTrimestre as $nota):
                                                        $valor = floatval($nota->getValorNota());

                                                        if ($valor >= 10) {
                                                            $classeNota = 'badge bg-primary';
                                                            $tooltip = 'Nota positiva';
                                                            $icone = '<i class="bi bi-check-circle-fill me-1"></i>';
                                                        } else {
                                                            $classeNota = 'badge bg-danger';
                                                            $tooltip = 'Nota negativa';
                                                            $icone = '<i class="bi bi-x-circle-fill me-1"></i>';
                                                        }
                                                    ?>
                                                        <tr>
                                                            <td><strong><?= $cont++; ?></strong></td>
                                                            <td><?= htmlspecialchars($nota->getNomeAluno()); ?></td>
                                                            <td><?= htmlspecialchars($nota->getNomeCurso()); ?></td>
                                                            <td><?= htmlspecialchars($nota->getNomeDisciplina()); ?></td>
                                                            <td>
                                                                <span class="<?= $classeNota ?>" data-bs-toggle="tooltip" title="<?= $tooltip ?>">
                                                                    <?= $icone ?><?= htmlspecialchars($nota->getValorNota()); ?>
                                                                </span>
                                                            </td>
                                                            <td><?= htmlspecialchars($nota->getDataValorNota()); ?></td>
                                                            <td><?= htmlspecialchars($nota->getTipoAvaliacaoNota()); ?></td>
                                                            <td><?= htmlspecialchars($nota->getTipoNota()); ?></td>
                                                            <td><?= htmlspecialchars($nota->getTrimestreNota()); ?></td>
                                                            <td>
                                                                <div class="dropdown">
                                                                    <button type="button" class="btn btn-primary light sharp" data-bs-toggle="dropdown" aria-expanded="false">
                                                                        <svg width="20px" height="20px" viewBox="0 0 24 24">
                                                                            <circle fill="#000" cx="5" cy="12" r="2"></circle>
                                                                            <circle fill="#000" cx="12" cy="12" r="2"></circle>
                                                                            <circle fill="#000" cx="19" cy="12" r="2"></circle>
                                                                        </svg>
                                                                    </button>
                                                                    <div class="dropdown-menu">
                                                                        <a class="dropdown-item btn-apagar-nota" data-bs-toggle="modal" data-bs-target="#modalNotaApagar" href="#" data-id="<?= $nota->getIdNota() ?>">Apagar</a>
                                                                        <a class="dropdown-item btn-editar-nota" data-bs-toggle="modal" data-bs-target="#modalNotaEditar" href="#" data-id="<?= $nota->getIdNota() ?>">Editar</a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php $active = false;
                                endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="modal fade" id="modalNotaCadastrar">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cadastrar Nota</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="../Controle/crudNota.php" method="POST">
                            <input type="hidden" name="idProfessor" value="<?= $professor ?>">

                            <?php
                            require_once("../Modelo/DAO/AlunoDAO.php");
                            require_once("../Modelo/DAO/DisciplinaDAO.php");
                            require_once("../Modelo/DAO/CursoDAO.php");
                            require_once("../Modelo/DAO/TurmaDAO.php");

                            $daoAluno = new AlunoDAO();
                            $alunos = $daoAluno->listarTodos();

                            $daoCurso = new CursoDAO();
                            $cursos = $daoCurso->Mostrar();

                            $daoDisciplina = new DisciplinaDAO();
                            $disciplinas = $daoDisciplina->listarPorProfessor($professor);

                            $daoTurma = new TurmaDAO();
                            $turmas = $daoTurma->listarTodos();
                            ?>

                            <div class="row">
                                <div class="col-md-3">
                                    <label><strong>Classe</strong></label>
                                    <select id="classeSelect" class="form-control input-rounded">
                                        <option value="">Selecione</option>
                                        <option value="10">10ª</option>
                                        <option value="11">11ª</option>
                                        <option value="12">12ª</option>
                                        <option value="13">13ª</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label><strong>Curso</strong></label>
                                    <select id="cursoSelect" name="idCurso" class="form-control input-rounded">
                                        <option value="">Selecione</option>
                                        <?php foreach ($cursos as $curso): ?>
                                            <option value="<?= $curso->getIdCurso() ?>"><?= htmlspecialchars($curso->getNomeCurso()) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label><strong>Período</strong></label>
                                    <select id="periodoSelect" class="form-control input-rounded">
                                        <option value="">Selecione</option>
                                        <option value="Manhã">Manhã</option>
                                        <option value="Tarde">Tarde</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label><strong>Turma</strong></label>
                                    <select id="turmaSelect" class="form-control input-rounded">
                                        <option value="">Selecione a turma</option>
                                        <?php foreach ($turmas as $turma): ?>
                                            <option value="<?= $turma->getIdTurma() ?>"><?= htmlspecialchars($turma->getNomeTurma()) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                            </div>

                            <div class="form-group campo-condicional">
                                <label class="mb-1"><strong>Aluno<b style="font-size: 14px;color: red;">*</b></strong></label>
                                <select id="alunoSelect" name="idAluno" class="form-control input-rounded" required>
                                    <option value="">Selecione o aluno</option>
                                </select>
                            </div>

                            <div class="form-group campo-condicional">
                                <label class="mb-1"><strong>Disciplina<b style="font-size: 14px;color: red;">*</b></strong></label>
                                <select class="form-control input-rounded" name="idDisciplina" required>
                                    <option value="">Selecione a disciplina</option>
                                    <?php foreach ($disciplinas as $disciplina): ?>
                                        <option value="<?= htmlspecialchars($disciplina->getIdDisciplina()) ?>">
                                            <?= htmlspecialchars($disciplina->getNomeDisciplina()) ?>
                                        </option>

                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group campo-condicional">
                                        <label class="mb-1">
                                            <strong>Tipo de Nota <b style="font-size: 14px;color: red;">*</b></strong>
                                        </label>
                                        <select class="form-control input-rounded" name="tipoNota" id="tipoNotaSelect" required>
                                            <option value="">Selecione o tipo de Nota</option>
                                            <option value="MAC">MAC</option>
                                            <option value="NPP">NPP</option>
                                            <option value="NPT">NPT</option>
                                            <option value="NE">NE</option>
                                            <option value="PG">PG</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group campo-condicional">
                                        <label class="mb-1">
                                            <strong>Tipo de Avaliação <b style="font-size: 14px;color: red;">*</b></strong>
                                        </label>
                                        <select class="form-control input-rounded" name="tipoAvaliacaoNota" id="avaliacaoNotaSelect" required>
                                            <option value="">Selecione o tipo de Avaliação</option>
                                            <option value="Avaliação Continua">Avaliação Continua</option>
                                            <option value="Prova do Professor">Prova do Professor</option>
                                            <option value="Prova Trimestral">Prova Trimestral</option>
                                            <option value="Exame">Exame</option>
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-6">

                                    <div class="form-group campo-condicional">
                                        <label class="mb-1"><strong>Trimestre<b style="font-size: 14px;color: red;">*</b></strong></label>
                                        <select class="form-control input-rounded" name="trimestreNota" required>
                                            <option value="">Selecione o Trimestre</option>
                                            <option value="1º Trimestre">1º Trimestre</option>
                                            <option value="2º Trimestre">2º Trimestre</option>
                                            <option value="3º Trimestre">3º Trimestre</option>
                                            <option value="-">-</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group campo-condicional">
                                        <label class="mb-1"><strong>Valor<b style="font-size: 14px;color: red;">*</b></strong></label>
                                        <input type="text" class="form-control input-rounded" name="valorNota" required>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group campo-condicional">
                                        <label class="mb-1"><strong>Data da Avaliação<b style="font-size: 14px;color: red;">*</b></strong></label>
                                        <input type="datetime-local" class="form-control input-rounded" name="dataAvaliacaoNota" required>
                                    </div>
                                </div>
                            </div>


                            <div class="text-center mt-4 campo-condicional">
                                <button type="submit" class="btn btn-primary btn-rounded" name="cadastrarNota">Cadastrar</button>
                                <a href="javascript:void(0)" class="btn btn-light btn-rounded ml-2">Cancelar</a>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>





        <div class="modal fade" id="modalNotaEditar">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Nota</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="../Controle/crudNota.php" method="POST">
                            <input type="hidden" name="idNota" id="idNotaEditar" required>

                            <?php
                            require_once("../Modelo/DAO/AlunoDAO.php");
                            require_once("../Modelo/DAO/DisciplinaDAO.php");
                            require_once("../Modelo/DAO/CursoDAO.php");

                            $daoAluno = new AlunoDAO();
                            $alunos = $daoAluno->listarTodos();

                            $daoCurso = new CursoDAO();
                            $cursos = $daoCurso->Mostrar();

                            $daoDisciplina = new DisciplinaDAO();
                            $disciplinas = $daoDisciplina->listarTodos();
                            ?>


                            <div class="form-group">
                                <label class="mb-1"><strong>Nome da Disciplina<b style="font-size: 14px;color: red;">*</b></strong></label>
                                <select class="form-control input-rounded" id="idDisciplinaEditar" name="idDisciplina" required>
                                    <option value="">Selecione a disciplina</option>
                                    <?php foreach ($disciplinas as $disciplina): ?>
                                        <option value="<?= htmlspecialchars($disciplina->getIdDisciplina()) ?>">
                                            <?= htmlspecialchars($disciplina->getNomeDisciplina()) ?>
                                        </option>

                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="mb-1"><strong>Nome do Aluno<b style="font-size: 14px;color: red;">*</b></strong></label>
                                <select class="form-control input-rounded" id="idAlunoEditar" name="idAluno" required>
                                    <option value="">Selecione o aluno</option>
                                    <?php foreach ($alunos as $aluno): ?>
                                        <option value="<?= htmlspecialchars($aluno->getIdAluno()) ?>">
                                            <?= htmlspecialchars($aluno->getNomeAluno()) ?>
                                        </option>

                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="mb-1"><strong>Nome do Curso <b style="font-size: 14px;color: red">*</b></strong></label>
                                <select class="form-control input-rounded" id="idCursoEditar" name="idCurso" required>
                                    <option value="">Selecione o curso</option>
                                    <?php
                                    foreach ($cursos as $curso): ?>
                                        <option value=""><?= htmlspecialchars($curso->getIdCurso()) ?>
                                            <?= htmlspecialchars($curso->getNomeCurso()) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>



                            <div class="form-group">
                                <label class="mb-1"><strong>Valor<b style="font-size: 14px;color: red;">*</b></strong></label>
                                <input type="text" class="form-control input-rounded" id="ValorNotaEditar" name="valorNota" required>
                            </div>

                            <div class="form-group">
                                <label class="mb-1"><strong>Data da Avaliação<b style="font-size: 14px;color: red;">*</b></strong></label>
                                <input type="datetime-local" class="form-control input-rounded" id="dataAvaliacaoNotaEditar" name="dataAvaliacaoNota" required>
                            </div>



                            <div class="form-group">
                                <label class="mb-1"><strong>Tipo de avaliação<b style="font-size: 14px;color: red;">*</b></strong></label>
                                <select class="form-control input-rounded" id="tipoAvaliacaoNotaEditar" name="tipoAvaliacaoNota" required>
                                    <option value="">Selecione o tipo de avaliação</option>
                                    <option value="Avaliação Continua">Avaliação Continua</option>
                                    <option value="Prova do professor">Prova do professor</option>
                                    <option value="Prova trimestral">Prova trimestral</option>
                                    <option value="Exame">Exame</option>

                                </select>
                            </div>


                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-rounded" name="actualizarNota">Salvar Alterações</button>
                                <a href="javascript:void(0)" class="btn btn-light btn-rounded ml-2">Cancelar</a>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>


        <div class="modal fade" id="modalNotaApagar">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Exclusão</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Tem certeza que deseja excluir esta nota? Esta ação não pode ser desfeita.</p>
                        <form id="formNotaExcluir" method="POST" action="../Controle/crudNota.php">
                            <input type="hidden" name="idNota" id="idNotaApagar" required>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" form="formNotaExcluir" class="btn btn-danger" name="apagarNota">Confirmar Exclusão</button>
                    </div>
                </div>
            </div>
        </div>


    </div>
    <script src="assets/js/jquery-3.6.0.min.js">

    </script>

    <script>
        $(document).on('click', '.btn-editar-nota', function() {
            const id = $(this).data('id');

            $.get('../Controle/retornarNota.php', {
                id: id
            }, function(data) {
                try {
                    const Nota = data;
                    $('#idNotaEditar').val(Nota.idNota);
                    $('#idDisciplinaEditar').val(Nota.idDisciplina);
                    $('#idAlunoEditar').val(Nota.idAluno);
                    $('#idCursoEditar').val(Nota.idCurso);
                    $('#ValorNotaEditar').val(Nota.valorNota);
                    $('#tipoAvaliacaoNotaEditar').val(Nota.tipoAvaliacacaoNota);
                    $('#dataAvaliacaoNotaEditar').val(Nota.dataAvaliacaoNota);

                } catch (e) {
                    console.log('Erro ao interpretar o JSON retornado:', data);
                }
            }).fail(function(xhr) {
                console.log('Erro na requisição AJAX:', xhr.responseText);
            });
        });
    </script>

    <script>
        $(document).on('click', '.btn-apagar-nota', function() {
            const id = $(this).data('id');

            $.get('../Controle/retornarNota.php', {
                id: id
            }, function(data) {
                try {
                    const Nota = data;
                    $('#idNotaApagar').val(Nota.idNota);

                } catch (e) {
                    console.log('Erro ao interpretar o JSON retornado:', data);
                }
            }).fail(function(xhr) {
                console.log('Erro na requisição AJAX:', xhr.responseText);
            });
        });
    </script>
    <div class="footer">
        <div class="copyright">
            <p>Copyright © Sistema de Gestão Web de Gestão de Alunos by <a href="#" target="_blank">SGWA</a> 2025</p>
        </div>
    </div>

    <script src="assets/vendor/global/global.min.js" type="text/javascript"></script>
    <script src="assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="assets/vendor/chart-js/chart.bundle.min.js" type="text/javascript"></script>
    <script src="assets/vendor/peity/jquery.peity.min.js" type="text/javascript"></script>
    <script src="assets/vendor/apexchart/apexchart.js" type="text/javascript"></script>
    <script src="assets/js/dashboard/dashboard-1.js" type="text/javascript"></script>
    <script src="assets/vendor/bootstrap-datetimepicker/js/moment.js" type="text/javascript"></script>
    <script src="assets/vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="assets/js/custom.min.js" type="text/javascript"></script>
    <script src="assets/js/deznav-init.js" type="text/javascript"></script>
    <script>
        function initProgressBars() {
            document.querySelectorAll('.progress-fill').forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0';
                setTimeout(() => {
                    bar.style.width = width;
                }, 100);
            });
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const classe = document.getElementById("classeSelect");
            const curso = document.getElementById("cursoSelect");
            const periodo = document.getElementById("periodoSelect");
            const turma = document.getElementById("turmaSelect");
            const camposCondicionais = document.querySelectorAll(".campo-condicional");
            const alunoSelect = document.getElementById("alunoSelect");

            // Inicialmente oculta todos os campos
            camposCondicionais.forEach(campo => campo.classList.remove("visivel"));

            function carregarAlunos() {
                const dados = {
                    classe: classe.value,
                    curso: curso.value,
                    periodo: periodo.value,
                    turma: turma.value
                };

                const todosSelecionados = dados.classe && dados.curso && dados.periodo && dados.turma;

                if (!todosSelecionados) {
                    alunoSelect.innerHTML = '<option value="">Selecione o aluno</option>';
                    camposCondicionais.forEach(campo => campo.classList.remove("visivel"));
                    return;
                }

                fetch("../Controle/carregarAlunos.php", {
                        method: "POST",
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(dados)
                    })
                    .then(res => res.json())
                    .then(alunos => {
                        alunoSelect.innerHTML = '<option value="">Selecione o aluno</option>';

                        if (alunos && alunos.length > 0) {
                            alunos.forEach(aluno => {
                                alunoSelect.innerHTML += `<option value="${aluno.id}">${aluno.nome}</option>`;
                            });
                            camposCondicionais.forEach(campo => campo.classList.add("visivel"));
                        } else {
                            camposCondicionais.forEach(campo => campo.classList.remove("visivel"));
                        }
                    })
                    .catch(err => {
                        console.error("Erro ao carregar alunos:", err);
                        alunoSelect.innerHTML = '<option value="">Erro ao carregar</option>';
                        camposCondicionais.forEach(campo => campo.classList.remove("visivel"));
                    });
            }

            classe.addEventListener("change", carregarAlunos);
            curso.addEventListener("change", carregarAlunos);
            periodo.addEventListener("change", carregarAlunos);
            turma.addEventListener("change", carregarAlunos);
        });
    </script>



    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tipoNotaSelect = document.getElementById("tipoNotaSelect");
            const avaliacaoNotaSelect = document.getElementById("avaliacaoNotaSelect");

            tipoNotaSelect.addEventListener("change", function() {
                const tipoSelecionado = tipoNotaSelect.value;
                let valorCorrespondente = "";

                if (tipoSelecionado === "MAC") {
                    valorCorrespondente = "Avaliação Continua";
                } else if (tipoSelecionado === "NPP") {
                    valorCorrespondente = "Prova do Professor";
                } else if (tipoSelecionado === "NPT" || tipoSelecionado === "PG") {
                    valorCorrespondente = "Prova Trimestral";
                } else if (tipoSelecionado === "NE") {
                    valorCorrespondente = "Exame";
                } else {
                    valorCorrespondente = "";
                }

                // Percorre as opções e seleciona a que corresponde ao valor
                for (let i = 0; i < avaliacaoNotaSelect.options.length; i++) {
                    if (avaliacaoNotaSelect.options[i].value === valorCorrespondente) {
                        avaliacaoNotaSelect.selectedIndex = i;
                        break;
                    }
                }
            });
        });
    </script>

    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function(el) {
            return new bootstrap.Tooltip(el)
        });
    </script>

</body>

</html>