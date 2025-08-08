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

// Verifica se o acesso é email de admin válido (ex: termina com @admin.estrela.com)
if (!preg_match('/^[0-9]{9}[A-Z]{2}[0-9]{3}$/', $acesso)) {
    // Se não for admin, redireciona para página de acesso negado ou login
    $_SESSION['success'] = "Acesso negado! Apenas administradores podem aceder.";
    $_SESSION['icon'] = "error";
    header("Location: index.php");
    exit();
}

// Se chegou aqui, é admin autenticado e pode continuar
$usuarioId = $_SESSION['idUtilizador'];
?>

<!DOCTYPE html>
<html lang="en">

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
    <link href="assets/vendor/chartist/css/chartist.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.css" rel="stylesheet" type="text/css" />
    <script src="assets/js/alertsMessage.js"></script>
    <script src="assets/js/sweetalert.js"></script>

    <style>
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
                    $mensagens = $dao->mostrarTodasNotificacoes();

                    ?>
                    <ul class="navbar-nav header-right">
                        <li class="nav-item dropdown notification_dropdown">
                            <a class="nav-link ai-icon" href="javascript:void(0)" role="button" data-bs-toggle="dropdown">
                                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M22.75 15.8385V13.0463C22.7471 10.8855 21.9385 8.80353 20.4821 7.20735C19.0258 5.61116 17.0264 4.61555 14.875 4.41516V2.625C14.875 2.39294 14.7828 2.17038 14.6187 2.00628C14.4546 1.84219 14.2321 1.75 14 1.75C13.7679 1.75 13.5454 1.84219 13.3813 2.00628C13.2172 2.17038 13.125 2.39294 13.125 2.625V4.41534C10.9736 4.61572 8.97429 5.61131 7.51794 7.20746C6.06159 8.80361 5.25291 10.8855 5.25 13.0463V15.8383C4.26257 16.0412 3.37529 16.5784 2.73774 17.3593C2.10019 18.1401 1.75134 19.1169 1.75 20.125C1.75076 20.821 2.02757 21.4882 2.51969 21.9803C3.01181 22.4724 3.67904 22.7492 4.375 22.75H9.71346C9.91521 23.738 10.452 24.6259 11.2331 25.2636C12.0142 25.9013 12.9916 26.2497 14 26.2497C15.0084 26.2497 15.9858 25.9013 16.7669 25.2636C17.548 24.6259 18.0848 23.738 18.2865 22.75H23.625C24.321 22.7492 24.9882 22.4724 25.4803 21.9803C25.9724 21.4882 26.2492 20.821 26.25 20.125C26.2486 19.117 25.8998 18.1402 25.2622 17.3594C24.6247 16.5786 23.7374 16.0414 22.75 15.8385Z" fill="#007bff" />
                                </svg>
                                <span class="badge light text-white bg-primary"> <?= $n_Notificacoes ?></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end p-0">
                                <div id="DZ_W_Notification1" class="widget-media dz-scroll p-3" style="max-height: 380px; overflow-y: auto;">
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
                                <a class="all-notification d-block text-center p-2 border-top" href="notificacoes.php">
                                    Ver todas as notificações <i class="ti-arrow-right"></i>
                                </a>
                            </div>
                        </li>



                        <li class="nav-item dropdown header-profile">
                            <a class="nav-link" href="javascript:void(0)" role="button" data-bs-toggle="dropdown">
                                <img src="imagens/perfil.png" width="20" alt="">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="editarPerfil.html" class="dropdown-item ai-icon">
                                    <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
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

        <div id="overlay" style="display:none; position: fixed; top:0; left:0; width:100vw; height:100vh; background: rgba(0,0,0,0.5); z-index: 998;" onclick="toggleMenu()"></div>

        <nav class="menu-user">
            <div class="menu-content">
                <i class="fa-solid fa-user-graduate user-photo"></i>
                <ul>
                    <li class="active"><a href="#" title="Home" id="linkAdicionarAluno"><i class="fa-solid fa-chalkboard"></i><span class="text-white">Dashboard</span></a></li>
                    <li><a href="alunoBase.php" title="Cadastrar Aluno"><i class="fa-regular fa fa-user"></i><span>Aluno</span></a></li>
                    <li><a href="professorBase.php" title="Cadastrar Professor"><i class="fa-solid fa-chalkboard-user"></i><span>Professores</span></a></li>
                    <li><a href="eventoBase.php" title="Cadastro de Evento"><i class="fa-regular fa-calendar"></i><span>Eventos</span></a></li>
                    <li><a href="cursoBase.php" title="Cadastro de Cursos"><i class="fa-solid fa-book"></i><span>Cursos</span></a></li>
                    <li><a href="horarioBase.php" title="Cadastro de Horários"><i class="fa-solid fa-clock"></i><span>Horários</span></a></li>
                    <li><a href="turmaBase.php" title="Cadastro de Turmas"><i class="fa-solid fa-users"></i><span>Turmas</span></a></li>
                    <li><a href="disciplinaBase.php" title="Cadastro de Disciplinas"><i class="fa-solid fa-book-open"></i><span>Disciplinas</span></a></li>
                    <li><a href="matriculaBase.php" title="Matrícula"><i class="fa-solid fa-file-signature"></i><span>Matrículas</span></a></li>
                    <li><a href="documentoBase.php" title="Aceitar Documentos"><i class="fa-regular fa-folder-open"></i><span>Documentos</span></a></li>
                </ul>
            </div>
        </nav>
        <div class="menu-toggle" onclick="toggleMenu()">☰</div>

    </div>

    <div class="content-body">
        <!-- row -->
        <div class="container-fluid">
            <div class="form-head d-flex align-items-center mb-sm-4 mb-3">
                <div class="me-auto">
                    <h2 class="text-black font-w600">Área Admistrativa</h2>
                    <p class="mb-0">SGWA - Sistema de Gestão Web de Alunos</p>
                </div>
            </div>
            <?php
            require_once '../Modelo/DAO/ProfessorDAO.php';
            require_once '../Modelo/DAO/AlunoDAO.php';
            require_once '../Modelo/DAO/DocumentoDAO.php';
            require_once '../Modelo/DAO/TurmaDAO.php';

            $professorDAO = new ProfessorDAO();
            $alunoDAO = new AlunoDAO();
            $documentoDAO = new DocumentoDAO();
            $turmaDAO = new TurmaDAO();

            $totalProfessores = $professorDAO->contarTodos();
            $totalAlunos = $alunoDAO->contarMatriculados();
            $totalDocumentos = $documentoDAO->contarTodos();
            $totalTurmas = $turmaDAO->contarTodas();

            // Se algum retornar false, assume 0 para evitar erros
            $totalProfessores = is_numeric($totalProfessores) ? $totalProfessores : 0;
            $totalAlunos = is_numeric($totalAlunos) ? $totalAlunos : 0;
            $totalDocumentos = is_numeric($totalDocumentos) ? $totalDocumentos : 0;
            $totalTurmas = is_numeric($totalTurmas) ? $totalTurmas : 0;

            $totalGeral = $totalProfessores + $totalAlunos + $totalDocumentos + $totalTurmas;

            function percentagemEscala($valor, $max)
            {
                if ($max <= 0) return 0;
                $percent = ($valor / $max) * 100;
                return $percent > 100 ? 100 : round($percent, 2);  // Garante que nunca passa de 100%
            }
            ?>
            <section class="cards">
                <div class="card-1">
                    <p>Professores Cadastrados <i class="fa-solid fa-chalkboard-user card-icon"></i></p>
                    <h2><?= $totalProfessores ?></h2>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= percentagemEscala($totalProfessores, $maxProfessores) ?>%;"></div>
                    </div>
                </div>
                <div class="card-2">
                    <p>Alunos Matriculados <i class="fa-regular fa-user card-icon"></i></p>
                    <h2><?= $totalAlunos ?></h2>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= percentagemEscala($totalAlunos, $maxAlunos) ?>%;"></div>
                    </div>
                </div>
                <div class="card-3">
                    <p>Total de Documentos <i class="fa-regular fa-folder-open card-icon"></i></p>
                    <h2><?= $totalDocumentos ?></h2>
                    <div class="progress-bar">
                        <div class="progress-fill negative" style="width: <?= percentagemEscala($totalDocumentos, $maxDocumentos) ?>%;"></div>
                    </div>
                </div>
                <div class="card-4">
                    <p>Total de Turmas <i class="fa-solid fa-users card-icon"></i></p>
                    <h2><?= $totalTurmas ?></h2>
                    <div class="progress-bar">
                        <div class="progress-fill negative" style="width: <?= percentagemEscala($totalTurmas, $maxTurmas) ?>%;"></div>
                    </div>
                </div>
            </section>



            <div class="row">

                <div class="col-xl-12">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card appointment-schedule">
                                <div class="card-header pb-0 border-0">
                                    <h3 class="fs-20 text-black mb-0">Calendário Académico</h3>
                                    <div class="dropdown ms-auto c-pointer">
                                        <div class="btn-link p-2 bg-light" data-bs-toggle="dropdown">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13Z" stroke="#2E2E2E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M12 6C12.5523 6 13 5.55228 13 5C13 4.44772 12.5523 4 12 4C11.4477 4 11 4.44772 11 5C11 5.55228 11.4477 6 12 6Z" stroke="#2E2E2E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M12 20C12.5523 20 13 19.5523 13 19C13 18.4477 12.5523 18 12 18C11.4477 18 11 18.4477 11 19C11 19.5523 11.4477 20 12 20Z" stroke="#2E2E2E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </div>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item text-black" href="javascript:;">Info</a>
                                            <a class="dropdown-item text-black" href="javascript:;">Details</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-xl-6 col-xxl-12 col-md-6">
                                            <div class="appointment-calender">
                                                <input type='text' class="form-control d-none" id='datetimepicker1' />
                                            </div>
                                        </div>

                                        <?php
                                        require_once("../Modelo/DAO/EventosDAO.php");
                                        setlocale(LC_TIME, 'pt_PT.UTF-8');
                                        $eventoDAO = new EventosDAO();
                                        $eventos = $eventoDAO->listarTodos();
                                        ?>

                                        <div class="col-xl-6 col-xxl-12 col-md-6">
                                            <div class="card height415 dz-scroll" id="appointment-schedule">
                                                <div class="card-header border-0 pb-0">
                                                    <h4 class="card-title">Próximos Eventos Académicos</h4>

                                                </div>
                                                <div class="card-body">

                                                    <?php foreach ($eventos as $evento): ?>
                                                        <?php
                                                        $data = new DateTime($evento->getDataEvento());
                                                        $dia = $data->format('d');
                                                        $mes = ucfirst(strftime('%b', $data->getTimestamp())); // Ex: Jan, Fev, Mar
                                                        ?>
                                                        <div class="d-flex pb-3 border-bottom mb-3 align-items-center event-item">
                                                            <div class="date-badge me-3 text-center">
                                                                <span class="day d-block font-w600"><?= $dia ?></span>
                                                                <span class="month d-block"><?= $mes ?></span>
                                                            </div>
                                                            <div class="me-auto">
                                                                <h5 class="text-black font-w600 mb-2">
                                                                    <?= htmlspecialchars($evento->getTituloEvento()) ?>
                                                                </h5>

                                                                <ul class="list-unstyled mb-0">
                                                                    <li>
                                                                        <i class="las la-calendar text-primary me-1"></i>
                                                                        <strong>Data: </strong> <?= htmlspecialchars(date("d/m/Y", strtotime($evento->getDataEvento()))) ?>
                                                                    </li>
                                                                    <li>
                                                                        <i class="las la-clock text-primary me-1"></i>
                                                                        <strong>Início: </strong> <?= htmlspecialchars($evento->getHoraInicioEvento()) ?>
                                                                        <span class="ms-2"><strong>Fim:</strong> <?= htmlspecialchars($evento->getHoraFimEvento()) ?></span>
                                                                    </li>
                                                                    <li>
                                                                        <i class="las la-map-marker text-primary me-1"></i>
                                                                        <strong>Local: </strong> <?= htmlspecialchars($evento->getLocalEvento()) ?>
                                                                    </li>
                                                                    <li>
                                                                        <i class="las la-user-tie text-primary me-1"></i>
                                                                        <strong>Responsável: </strong> <?= htmlspecialchars($evento->getResponsavelEvento()) ?>
                                                                    </li>
                                                                    <li>
                                                                        <i class="las la-tag text-primary me-1"></i>
                                                                        <strong>Tipo: </strong> <?= htmlspecialchars($evento->getTipoEvento()) ?>
                                                                    </li>
                                                                </ul>
                                                            </div>

                                                            <div class="dropdown">
                                                                <button type="button" class="btn btn-primary light btn-xs" data-bs-toggle="dropdown">
                                                                    <i class="fa fa-ellipsis-h"></i>
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <a class="dropdown-item" href="#"><i class="fa fa-eye me-2"></i>Ver detalhes</a>
                                                                    <a class="dropdown-item" href="#"><i class="fa fa-bell me-2"></i>Definir lembrete</a>
                                                                    <a class="dropdown-item" href="#"><i class="fa fa-share-alt me-2"></i>Partilhar</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

                $(function() {
                    $('#datetimepicker1').datetimepicker({
                        inline: true,
                    });
                });

                // Função para adicionar a classe 'active' e salvar o estado no Local Storage
                document.getElementById("linkAdicionarAluno").addEventListener("click", function() {
                    // Adiciona a classe 'active' ao link
                    this.classList.add("active");
                    // Salva no Local Storage que o link foi ativado
                    localStorage.setItem("linkActive", "true");
                });

                // Verifica se o link estava ativo antes de carregar a página
                window.addEventListener("load", function() {
                    // Recupera o estado do Local Stor age
                    const linkActive = localStorage.getItem("linkActive");

                    // Se o link estava ativo, adiciona a classe 'active'
                    if (linkActive === "true") {
                        document.getElementById("linkAdicionarAluno").classList.add("active");
                    }
                });
            </script>
            <style>
                .menu-user,
                #overlay {
                    transition: opacity 0.3s ease;
                    opacity: 0;
                    display: none;
                }
            </style>

            <script>
                function toggleMenu() {
                    const menu = document.querySelector('.menu-user');
                    const overlay = document.getElementById('overlay');

                    if (menu.style.display === 'block') {
                        // Esconder com efeito fade out
                        menu.style.opacity = '0';
                        overlay.style.opacity = '0';

                        setTimeout(() => {
                            menu.style.display = 'none';
                            overlay.style.display = 'none';
                        }, 300); // tempo da transição em ms
                    } else {
                        // Mostrar e começar transparente
                        menu.style.display = 'block';
                        overlay.style.display = 'block';

                        // Forçar leitura para ativar transição (reflow)
                        menu.offsetHeight;
                        overlay.offsetHeight;

                        // Fade in
                        menu.style.opacity = '1';
                        overlay.style.opacity = '1';
                    }
                }
            </script>

</body>

</html>