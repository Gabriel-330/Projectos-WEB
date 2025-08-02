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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css" />
    <link href="assets/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.css" rel="stylesheet" type="text/css" />

    <style>
        .iti {
            width: 100%;
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


        <nav class="menu-user">
            <div class="menu-content">
                <i class="fa-solid fa-user-graduate user-photo"></i>
                <ul>
                    <li><a href="indexAdmin.php" title="Home"><i class="fa-solid fa-chalkboard"></i></a></li>
                    <li class="active"><a href="#" title="Cadastrar Aluno"><i class="fa-regular fa fa-user"></i></a></li>
                    <li><a href="professorBase.php" title="Cadastrar professor"><i class="fa-solid fa-chalkboard-user"></i></a></li>
                    <li><a href="eventoBase.php" title="Calendário Académico"><i class="fa-regular fa-calendar"></i></a></li>
                    <li><a href="cursoBase.php" title="Cadastro de Cursos"><i class="fa-solid fa-book"></i></a></li>
                    <li><a href="horarioBase.php" title="Cadastro de Horários"><i class="fa-solid fa-clock"></i></a></li>
                    <li><a href="turmaBase.php" title="Cadastro de Turmas"><i class="fa-solid fa-users"></i></a></li>
                    <li><a href="disciplinaBase.php" title="Cadastro de Disciplinas"><i class="fa-solid fa-book-open"></i></a></li>
                    <li><a href="matriculaBase.php" title="Matrícula"><i class="fa-solid fa-file-signature"></i></a></li>
                </ul>
            </div>
        </nav>
    </div>




    <div class="content-body" style="margin-top: -30px;">
        <!-- row -->
        <div class="container-fluid">
            <div class="page-titles">
                <h4>Gestão de Alunos</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Alunos</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Gestão de Alunos</a></li>

                </ol>
            </div>


            <?php
            require_once("../Modelo/DAO/AlunoDAO.php");

            $dao = new AlunoDAO();
            $palavra = $_GET['palavra'] ?? '';

            // Verificar se algum filtro foi aplicado
            if ($palavra) {
                // Se filtros estiverem preenchidos, realizar pesquisa
                $alunos = $dao->pesquisar($palavra);
            } else {
                // Caso contrário, exibir todos os alunos
                $alunos = $dao->listarTodos();
            }
            ?>

            <!-- Formulário de pesquisa -->
            <form action="alunoBase.php" method="GET">
                <div class="col-md-5">
                    <div class="form-group d-flex">
                        <input type="text" class="form-control input-rounded flex-grow-1 me-2" style="width: 200px;" placeholder="Pesquisar alunos..." name="palavra" value="<?= htmlspecialchars($palavra) ?>">

                        <button type="submit" class="btn btn-primary btn-rounded" style="flex-shrink: 0;">
                            <i class="fa fa-search mr-2"></i> Buscar
                        </button>
                    </div>
                </div>
            </form>





            <!-- row -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Lista de Alunos</h4>
                            <a href="#" class="btn btn-success btn-rounded " data-bs-toggle="modal" data-bs-target="#modalAlunoCadastrar">+ Adicionar aluno</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-responsive-md text-center">
                                    <thead>
                                        <tr>
                                            <th style="width:50px;"><strong>#</strong></th>
                                            <th><strong>NOME DO ALUNO</strong></th>
                                            <th><strong>MORADA</strong></th>
                                            <th><strong>DATA NASCIMENTO</strong></th>
                                            <th><strong>GÊNERO</strong></th>
                                            <th><strong>CURSO</strong></th>
                                            <th><strong>TURMA</strong></th>
                                            <th><strong>RESPONSÁVEL</strong></th>
                                            <th><strong>CONTACTO RESPONSÁVEL</strong></th>
                                            <th><strong>ANO DE MATRÍCULA</strong></th>
                                            <th><strong>RESPONSÁVEL PELO CADASTRO</strong></th>
                                            <th><strong>Nº DO BI</strong></th>
                                            <th><strong>AÇÕES</strong></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $cont = 1;
                                        foreach ($alunos as $aluno): ?>
                                            <tr>
                                                <td><strong><?= $cont++; ?></strong></td>
                                                <td><?= htmlspecialchars($aluno->getNomeAluno()); ?></td>
                                                <td><?= htmlspecialchars($aluno->getMoradaAluno()); ?></td>
                                                <td><?= htmlspecialchars($aluno->getDataNascimentoAluno()); ?></td>
                                                <td><?= htmlspecialchars($aluno->getGeneroAluno()); ?></td>
                                                <td><?= htmlspecialchars($aluno->getNomeCurso()); ?></td>
                                                <td><?= htmlspecialchars($aluno->getNomeTurma()); ?></td>
                                                <td><?= htmlspecialchars($aluno->getResponsavelAluno()); ?></td>
                                                <td><?= htmlspecialchars($aluno->getContactoResponsavelAluno()); ?></td>
                                                <td><?= htmlspecialchars($aluno->getAnoIngressoAluno()); ?></td>
                                                <td><?= htmlspecialchars($aluno->getNomeUtilizador()); ?></td>
                                                <td><?= htmlspecialchars($aluno->getnIdentificacao()); ?></td>


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
                                                            <a class="dropdown-item btn-apagar-aluno" data-bs-toggle="modal" data-bs-target="#modalAlunoApagar" data-id="<?= $aluno->getIdAluno() ?>">Apagar</a>
                                                            <a class="dropdown-item btn-editar-aluno" data-bs-toggle="modal" data-bs-target="#modalAlunoEditar" data-id="<?= $aluno->getIdAluno() ?>">Editar</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalAlunoCadastrar">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cadastrar Aluno</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="../Controle/crudAluno.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" class="form-control input-rounded" name="dataMatricula" required>

                            <div class="form-group">
                                <label class="mb-1"><strong>Nome do Aluno <b style="font-size: 14px;color: red;">*</b></strong></label>
                                <input type="text" class="form-control input-rounded" name="nomeAluno" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="mb-1">
                                            <strong>Nº de Identificação (BI) <b style="font-size: 14px;color: red;">*</b></strong>
                                        </label>
                                        <input type="text"
                                            class="form-control input-rounded"
                                            name="nIdentificacao"
                                            pattern="([0-9]{9}[A-Z]{2}[0-9]{3})"
                                            title="Nº do Bilhente Inválido! O BI tem esse formato (ex: 123456789LA400)"
                                            required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="mb-1"><strong>Gênero <b style="font-size: 14px;color: red;">*</b></strong></label>
                                        <select class="form-control input-rounded" name="generoAluno" required>
                                            <option value="">Selecione o gênero</option>
                                            <option value="Masculino">Masculino</option>
                                            <option value="Femenino">Femenino</option>
                                        </select>
                                    </div>
                                </div>
                            </div>



                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="mb-1"><strong>Morada <b style="font-size: 14px;color: red;">*</b></strong></label>
                                        <input type="text" class="form-control input-rounded" name="moradaAluno" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="mb-1"><strong>Data de Nascimento <b style="font-size: 14px;color: red;">*</b></strong></label>
                                        <input type="date" class="form-control input-rounded" name="dataNascAluno" required>
                                    </div>
                                </div>
                            </div>

                            <?php
                            require_once("../Modelo/DAO/CursoDAO.php");

                            $dao = new CursoDAO();
                            $cursos = $dao->Mostrar();
                            ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="mb-1"><strong>Curso <b style="font-size: 14px;color: red;">*</b></strong></label>
                                        <select class="form-control input-rounded" name="tipoCurso" required>
                                            <option value="">Selecione o curso</option>
                                            <?php foreach ($cursos as $curso): ?>
                                                <option value="<?= htmlspecialchars($curso->getIdCurso()) ?>">
                                                    <?= htmlspecialchars($curso->getNomeCurso()) ?>
                                                </option>

                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                </div>

                                <?php
                                require_once("../Modelo/DAO/TurmaDAO.php");

                                $dao = new TurmaDAO();
                                $turmas = $dao->listarTodos();
                                ?>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="mb-1"><strong>Turma <b style="font-size: 14px;color: red;">*</b></strong></label>
                                        <select class="form-control input-rounded" name="tipoTurma" required>
                                            <option value="">Selecione o turma</option>
                                            <?php foreach ($turmas as $turma): ?>
                                                <option value="<?= htmlspecialchars($turma->getIdTurma()) ?>">
                                                    <?= htmlspecialchars($turma->getNomeTurma()) ?>
                                                </option>

                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="mb-1"><strong>Classe<b style="font-size: 14px;color: red;">*</b></strong></label>
                                        <select class="form-control input-rounded" name="classeMatricula" required>
                                            <option value="">Selecione a classe</option>
                                            <option value="10">10ª</option>
                                            <option value="11">11ª</option>
                                            <option value="12">12ª</option>
                                            <option value="13">13ª</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="mb-1"><strong>Período<b style="font-size: 14px;color: red;">*</b></strong></label>
                                        <select class="form-control input-rounded" name="periodoMatricula" required>
                                            <option value="">Selecione o período</option>
                                            <option value="Manhã">Manhã</option>
                                            <option value="Tarde">Tarde</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="mb-1"><strong>Foto do Aluno <b style="font-size: 14px;color: red;">*</b></strong></label>
                                        <input type="file" class="form-control input-rounded" name="fotoAluno" accept="image/*" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="mb-1"><strong>Responsável <b style="font-size: 14px;color: red;">*</b></strong></label>
                                        <input type="text" class="form-control input-rounded" name="responsavelAluno" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="mb-1"><strong>Contacto do responsável <b style="font-size: 14px;color: red;">*</b></strong></label>
                                    <div class="form-group">
                                        <input type="tel"
                                            class="form-control input-rounded telefone"
                                            name="contactoResponsavelAluno"
                                            inputmode="numeric"
                                            pattern="\+244\d{9}"
                                            title="O número deve começar com +244 e ter 9 dígitos (ex: +244923456789)"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-rounded" name="cadastrarAluno">Cadastrar</button>
                                <a href="javascript:void(0)" class="btn btn-light btn-rounded ml-2">Cancelar</a>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal fade" id="modalAlunoEditar">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Aluno</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <form action="../Controle/crudAluno.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="idAluno" id="idAlunoEditar" required>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="mb-1"><strong>Nome do Aluno <b style="font-size: 14px;color: red;">*</b></strong></label>
                                        <input type="text" class="form-control input-rounded" id="nomeAlunoEditar" name="nomeAluno" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-6">
                                        <label class="mb-1"><strong>Nº de Identificação (BI) <b style="font-size: 14px;color: red;">*</b></strong></label>
                                        <input type="text" class="form-control input-rounded" pattern="([0-9]{9}[A-Z]{2}[0-9]{3})"
                                            title="Nº do Bilhente Inválido! O BI tem esse formato (ex: 123456789LA400)" id="nIdentificacaoEditar" name="nIdentificacao" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="mb-1"><strong>Foto do Aluno <b style="font-size: 14px;color: red;">*</b></strong></label>
                                        <input type="file" class="form-control input-rounded" id="fotoAlunoEditar" name="fotoAluno" accept="image/*">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="mb-1"><strong>Gênero <b style="font-size: 14px;color: red;">*</b></strong></label>
                                        <select class="form-control input-rounded" id="generoAlunoEditar" name="generoAluno" required>
                                            <option value="">Selecione o gênero</option>
                                            <option value="Masculino">Masculino</option>
                                            <option value="Femenino">Femenino</option>

                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="mb-1"><strong>Morada <b style="font-size: 14px;color: red;">*</b></strong></label>
                                        <input type="text" class="form-control input-rounded" id="moradaAlunoEditar" name="moradaAluno" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="mb-1"><strong>Data de Nascimento <b style="font-size: 14px;color: red;">*</b></strong></label>
                                        <input type="date" class="form-control input-rounded" id="dataNascimentoAlunoEditar" name="dataNascAluno" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                            </div>

                            <?php
                            require_once("../Modelo/DAO/CursoDAO.php");

                            $dao = new CursoDAO();
                            $cursos = $dao->Mostrar();
                            ?>


                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="mb-1"><strong>Curso <b style="font-size: 14px;color: red;">*</b></strong></label>
                                    <select class="form-control input-rounded" id="idCursoEditar" name="tipoCurso" required>
                                        <option value="">Selecione o curso</option>
                                        <?php foreach ($cursos as $curso): ?>
                                            <option value="<?= htmlspecialchars($curso->getIdCurso()) ?>">
                                                <?= htmlspecialchars($curso->getNomeCurso()) ?>
                                            </option>

                                        <?php endforeach; ?>
                                    </select>
                                </div>

                            </div>


                            <?php
                            require_once("../Modelo/DAO/TurmaDAO.php");

                            $dao = new TurmaDAO();
                            $turmas = $dao->listarTodos();
                            ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="mb-1"><strong>Turma <b style="font-size: 14px;color: red;">*</b></strong></label>
                                    <select class="form-control input-rounded" name="tipoTurma" id="idTurmaEditar" required>
                                        <option value="">Selecione o turma</option>
                                        <?php foreach ($turmas as $turma): ?>
                                            <option value="<?= htmlspecialchars($turma->getIdTurma()) ?>">
                                                <?= htmlspecialchars($turma->getNomeTurma()) ?>
                                            </option>

                                        <?php endforeach; ?>
                                    </select>
                                </div>

                            </div>
                    </div>


                    <div class="form-group">
                        <label class="mb-1"><strong>Responsável <b style="font-size: 14px;color: red;">*</b></strong></label>
                        <input type="text" class="form-control input-rounded" id="responsavelAlunoEditar" name="responsavelAluno" required>
                    </div>

                    <label class="mb-1"><strong>Contacto do responsável <b style="font-size: 14px;color: red;">*</b></strong></label>
                    <div class="form-group">

                        <input type="tel"
                            class="form-control input-rounded telefone"
                            id="contactoResponsavelAlunoEditar"
                            name="contactoResponsavelAluno"

                            inputmode="numeric"
                            pattern="\+244\d{9}"
                            title="O número deve começar com +244 e ter 9 dígitos (ex: +244923456789)"
                            required>


                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-rounded" name="actualizarAluno">Salvar Alterações</button>
                        <a href="javascript:void(0)" class="btn btn-light btn-rounded ml-2">Cancelar</a>
                    </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAlunoApagar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir os dados deste Aluno? Esta ação não pode ser desfeita.</p>
                    <form id="formAlunoExcluir" method="POST" action="../Controle/crudAluno.php">
                        <input type="hidden" name="idAluno" id="idAlunoApagar" required>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="formAlunoExcluir" class="btn btn-danger" name="apagarAluno">Confirmar Exclusão</button>
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
    <script src="assets/js/jquery-3.6.0.min.js">

    </script>

    <script>
        $(document).on('click', '.btn-editar-aluno', function() {
            const id = $(this).data('id');

            $.get('../Controle/retornarAluno.php', {
                id: id
            }, function(data) {
                try {
                    const Aluno = data;
                    $('#idAlunoEditar').val(Aluno.idAluno);
                    $('#nomeAlunoEditar').val(Aluno.nomeAluno);
                    $('#generoAlunoEditar').val(Aluno.generoAluno);
                    $('#dataNascimentoAlunoEditar').val(Aluno.dataNascimentoAluno);
                    $('#moradaAlunoEditar').val(Aluno.moradaAluno);
                    $('#responsavelAlunoEditar').val(Aluno.responsavelAluno);
                    $('#contactoResponsavelAlunoEditar').val(Aluno.contactoResponsavelAluno);
                    $('#nIdentificacaoEditar').val(Aluno.nIdentificacao);
                    $('#idCursoEditar').val(Aluno.idCurso);
                    $('#idTurmaEditar').val(Aluno.idTurma);


                } catch (e) {
                    console.log('Erro ao interpretar o JSON retornado:', data);
                }
            }).fail(function(xhr) {
                console.log('Erro na requisição AJAX:', xhr.responseText);
            });
        });
    </script>

    <script>
        $(document).on('click', '.btn-apagar-aluno', function() {
            const id = $(this).data('id');

            $.get('../Controle/retornarAluno.php', {
                id: id
            }, function(data) {
                try {
                    const Aluno = data;
                    $('#idAlunoApagar').val(Aluno.idAluno);

                } catch (e) {
                    console.log('Erro ao interpretar o JSON retornado:', data);
                }
            }).fail(function(xhr) {
                console.log('Erro na requisição AJAX:', xhr.responseText);
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
    <script>
        const input = document.querySelector(".telefone");

        const iti = window.intlTelInput(input, {
            // Mostra apenas Angola
            onlyCountries: ["ao"],

            // Usa o código internacional (+244)
            nationalMode: false,

            // Remove a possibilidade de abrir o seletor de país
            allowDropdown: false,

            // Carrega utilitários da biblioteca
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js"
        });

        // Define o valor com +244 quando focar, se estiver vazio
        input.addEventListener("focus", function() {
            if (input.value.trim() === "") {
                const dialCode = iti.getSelectedCountryData().dialCode;
                input.value = `+${dialCode} `;
            }
        });

        // Garante que o campo só aceite dígitos e o símbolo +
        input.addEventListener("input", function() {
            this.value = this.value.replace(/[^\d+]/g, '');
        });
    </script>


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
            const hoje = new Date().toISOString().split('T')[0];
            document.querySelector('input[name="dataMatricula"]').value = hoje;
        });
    </script>
</body>

</html>