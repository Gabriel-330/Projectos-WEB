<?php
session_start();
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");
require_once("../Modelo/DAO/AlunoDAO.php");

// Verifica se o utilizador está autenticado
if (
    empty($_SESSION['idUtilizador']) ||
    empty($_SESSION['acesso'])
) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Verifica o tipo de usuário (exemplo: bloquear não administradores)
if (isset($_SESSION['perfilUtilizador']) && $_SESSION['perfilUtilizador'] !== 'Aluno') {
    header("Location: index.php");
    exit();
}

$id = $_SESSION['idUtilizador'];


$alunoDAO = new AlunoDAO();
$idAluno = $alunoDAO->retornarDadosPorUtilizador($id);

// Se chegou aqui, é um aluno autenticado e pode continuar
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
    <link href="assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.css" rel="stylesheet" type="text/css" />

    <style>
        .menu-user ul li.active a {
            background-color: #0b5ed7;
            /* cor de fundo ao clicar */
            color: white;
            /* cor do ícone ao clicar */
            border-radius: 5px;
        }

        #overlay {
            transition: opacity 0.3s ease;
            opacity: 0;
            display: none;
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
                    $mensagens = $dao->mostrarAlunoNotificacoes($_SESSION['perfilUtilizador']);

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
        <!-- Overlay -->
        <div id="overlay" style="display:none; position: fixed; top:0; left:0; width:100vw; height:100vh; background: rgba(0,0,0,0.5); z-index: 998;" onclick="toggleMenu()"></div>

        <nav class="menu-user">
            <div class="menu-content">
                <i class="fa-solid fa-user-graduate user-photo"></i>
                <ul>
                    <li><a href="indexAluno.php" title="Home"><i class="fa-solid fa-chalkboard"></i><span>Dashboard</span></a></li>
                    <li class="active"><a href="#" title="Consultar Nota"><i class="fa-solid fa-clipboard"></i><span class="text-white">Notas</span></a></li>
                    <li><a href="horarioAlunoBase.php" title="Consultar Horário"><i class="fa-regular fa-calendar"></i><span>Horários</span></a></li>
                    <li><a href="documentoAlunoBase.php" title="Solicitar Documentos"><i class="fa-regular fa-folder-open"></i><span>Documentos</span></a></li>
                </ul>
            </div>
        </nav>
        <div class="menu-toggle" onclick="toggleMenu()">☰</div>

    </div>

    <div class="content-body">
        <div class="container-fluid">
            <div class="page-titles">
                <h4>Consultar Notas</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Alunos</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Notas</a></li>
                </ol>
            </div>

            <?php
            require_once("../Modelo/DAO/NotaDAO.php");

            $dao = new NotaDAO();
            $palavra = $_GET['palavra'] ?? '';

            if ($palavra) {
                // Pesquisa pelo termo
                $notas = $dao->pesquisar($palavra);
            } else {
                // Verifica se $idAluno é um objeto válido
                if ($idAluno && method_exists($idAluno, 'getIdAluno')) {
                    $notas = $dao->listarPorAluno($idAluno->getIdAluno());
                } else {
                    $notas = []; // Ou uma string, dependendo do que seu código espera
                    echo "<p class='text-danger text-center fs-26'>Matrícula apagada</p>";
                }
            }

            ?>

            <form action="notaAlunoBase.php" method="GET">
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
                        <div class="card-body">
                            <div class="table-responsive">
                                <?php
                                // Agrupar notas por trimestre normalizado
                                $notasPorTrimestre = [];

                                function normalizarTrimestre($valor)
                                {
                                    $valor = trim(strtolower($valor));
                                    return match ($valor) {
                                        '1', '1º', 'primeiro', '1º trimestre', 'primeiro trimestre' => '1º Trimestre',
                                        '2', '2º', 'segundo', '2º trimestre', 'segundo trimestre' => '2º Trimestre',
                                        '3', '3º', 'terceiro', '3º trimestre', 'terceiro trimestre' => '3º Trimestre',
                                        '-', '-', '-', '-', '-' => 'Exame',
                                        default => $valor ?: 'Outro',
                                    };
                                }

                                function idSeguro($str)
                                {
                                    // Substituir espaços, acentos e símbolos
                                    return preg_replace('/[^a-z0-9]/', '', strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $str)));
                                }

                                foreach ($notas as $nota) {
                                    $trimestreFormatado = normalizarTrimestre($nota->getTrimestreNota());
                                    $notasPorTrimestre[$trimestreFormatado][] = $nota;
                                }

                                // Ordenar trimestres
                                uksort($notasPorTrimestre, function ($a, $b) {
                                    $ordem = ['1º Trimestre' => 1, '2º Trimestre' => 2, '3º Trimestre' => 3];
                                    return ($ordem[$a] ?? 99) <=> ($ordem[$b] ?? 99);
                                });
                                ?>

                                <!-- Nav Tabs -->
                                <ul class="nav nav-tabs mt-3" id="trimestreTabs" role="tablist">
                                    <?php $active = true;
                                    foreach ($notasPorTrimestre as $trimestre => $notasTrimestre):
                                        $idTab = idSeguro($trimestre);
                                    ?>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link <?= $active ? 'active' : '' ?>"
                                                id="tab-<?= $idTab ?>-tab"
                                                data-bs-toggle="tab"
                                                data-bs-target="#tab-<?= $idTab ?>"
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
                                        $idTab = idSeguro($trimestre);
                                    ?>
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
                                                        <?php
                                                        if (!empty($notasTrimestre)) :
                                                            $cont = 1;
                                                            foreach ($notasTrimestre as $nota):
                                                                $valor = floatval($nota->getValorNota());

                                                                // Badge da nota
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
                                                                                <svg width="20px" height="20px" viewBox="0 0 24 24" version="1.1">
                                                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                                        <rect x="0" y="0" width="24" height="24"></rect>
                                                                                        <circle fill="#000000" cx="5" cy="12" r="2"></circle>
                                                                                        <circle fill="#000000" cx="12" cy="12" r="2"></circle>
                                                                                        <circle fill="#000000" cx="19" cy="12" r="2"></circle>
                                                                                    </g>
                                                                                </svg>
                                                                            </button>
                                                                            <div class="dropdown-menu">
                                                                                <a class="dropdown-item btn-apagar-nota" data-bs-toggle="modal" data-bs-target="#modalNotaApagar" data-id="<?= $nota->getIdNota() ?>">
                                                                                    <i class="fa fa-trash me-2 text-danger"></i> Apagar
                                                                                </a>
                                                                                <a class="dropdown-item btn-editar-nota" data-bs-toggle="modal" data-bs-target="#modalNotaEditar" data-id="<?= $nota->getIdNota() ?>">
                                                                                    <i class="fa fa-edit me-2 text-primary"></i> Editar
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php
                                                            endforeach;
                                                        else: ?>
                                                            <tr>
                                                                <td colspan="10" class="text-center text-muted">
                                                                    <i class="bi bi-info-circle"></i> Nenhuma nota lançada.
                                                                </td>
                                                            </tr>
                                                        <?php endif; ?>
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
    </script>

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