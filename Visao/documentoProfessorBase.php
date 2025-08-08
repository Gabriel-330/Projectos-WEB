<?php
session_start();
require_once("../Modelo/DAO/NotificacoesDAO.php");
require_once("../Modelo/DTO/NotificacoesDTO.php");
require_once("../Modelo/DAO/ProfessorDAO.php");
require_once("../Modelo/DTO/ProfessorDTO.php");
// Verifica se o utilizador está autenticados
if (!isset($_SESSION['idUtilizador']) || !isset($_SESSION['acesso'])) {
    header("Location: index.php"); // Redireciona para login se não estiver autenticado
    exit();
}

$acesso = $_SESSION['acesso'];
$professorDAO = new ProfessorDAO();
$professor = $professorDAO->buscarPorUtilizador($_SESSION['idUtilizador']);

require_once("../Modelo/DAO/ProfessorDAO.php");
require_once("../Modelo/DTO/ProfessorDTO.php");
// Verifica se o 'acesso' corresponde ao padrão de aluno (ex: 009266492HA041)
if (!preg_match('/^[0-9]{9}[A-Z]{2}[0-9]{3}$/', $acesso)) {
    // Se não for aluno, redireciona com mensagem
    $_SESSION['success'] = "Acesso negado! Apenas alunos podem aceder.";
    $_SESSION['icon'] = "error";
    header("Location: index.php");
    exit();
}

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                                <a class="all-notification" href="javascript:void(0)">Mostrar mais notificações <i class="ti-arrow-right"></i></a>
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
        <div class="menu-toggle" onclick="toggleMenu()">☰</div>
        <nav class="menu-user">
            <div class="menu-content">
                <i class="fa-solid fa-user-graduate user-photo"></i>
                <ul>
                    <li><a href="indexProfessor.php" title="Home"><i class="fa-solid fa-chalkboard"></i><span>Dashboard</span></a></li>
                    <li><a href="notaProfessorBase.php" title="Consultar Nota"><i class="fa-solid fa-clipboard"></i><span>Notas</span></a></li>
                    <li><a href="horarioProfessorBase.php" title="Consultar Horário"><i class="fa-regular fa-calendar"></i><span>Horários</span></a></li>
                    <li class="active"><a href="#" title="Solicitar Documentos"><i class="fa-regular fa-folder-open"></i><span class="text-white">Documentos</span></a></li>
                </ul>
            </div>
        </nav>
    </div>



    <div class="content-body" style="margin-top: -30px;">
        <div class="container-fluid">
            <div class="page-titles">
                <h4>Gestão de Documentos</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Documentação</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Consulta</a></li>
                </ol>
            </div>

            <div class="col-md-5">
                <div class="form-group d-flex">
                    <input type="text" class="form-control input-rounded flex-grow-1 me-2" style="width: 200px;" placeholder="Pesquisar documentos...">
                    <a href="criarAluno.php" class="btn btn-primary btn-rounded" style="flex-shrink: 0;">Pesquisar</a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Busca de Documentos</h4>
                            <a href="#" class="btn btn-primary btn-rounded float-right" data-bs-toggle="modal" data-bs-target="#modalDocumentoSolicitar">
                                <i class="fa fa-upload mr-2"></i>
                                Solicitar documento
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="basic-form">
                                <form>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><strong>Tipo de Documento</strong></label>
                                                <select class="form-control input-rounded">
                                                    <option value="">Todos</option>
                                                    <option>Projeto de Lei</option>
                                                    <option>Requerimento</option>
                                                    <option>Indicação</option>
                                                    <option>Parecer Técnico</option>
                                                    <option>Ofício</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><strong>Período</strong></label>
                                                <select class="form-control input-rounded">
                                                    <option value="">Todos</option>
                                                    <option>Últimos 7 dias</option>
                                                    <option>Este mês</option>
                                                    <option>Este trimestre</option>
                                                    <option>Este ano</option>
                                                    <option>Personalizado</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><strong>Status</strong></label>
                                                <select class="form-control input-rounded">
                                                    <option value="">Todos</option>
                                                    <option>Vigente</option>
                                                    <option>Vencido</option>
                                                    <option>Próximo do vencimento</option>
                                                    <option>Arquivado</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><strong>Palavras-chave</strong></label>
                                                <input type="text" class="form-control input-rounded" placeholder="Termo de busca...">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 text-right">
                                            <button type="submit" class="btn btn-primary btn-rounded mr-2">
                                                <i class="fa fa-search mr-2"></i>Buscar
                                            </button>
                                            <button type="reset" class="btn btn-light btn-rounded">
                                                Limpar Filtros
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <hr class="mt-4 mb-4">

                            <div class="table-responsive">
                                <?php

                                require_once("../Modelo/DAO/DocumentoDAO.php");
                                require_once("../Modelo/DAO/ProfessorDAO.php");
                                require_once("../Modelo/DTO/DocumentoDTO.php");


                                $documentoDAO = new DocumentoDAO();
                                $professorDAO = new ProfessorDAO();

                                $id = $professorDAO->buscarPorUtilizador($_SESSION['idUtilizador']);
                                $documentos = $documentoDAO->buscarPorProfessor($id);

                                ?>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tipo Documento</th>
                                            <th>Data</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($documentos)): ?>
                                            <?php foreach ($documentos as $documento): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="fa fa-file-pdf text-danger mr-3 fa-2x"></i>
                                                            <div>
                                                                <strong><?= htmlspecialchars($documento->getTipoDocumento()) ?></strong>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td><?= date('d/m/Y', strtotime($documento->getDataEmissaoDocumento())) ?></td>

                                                    <td>
                                                        <?php
                                                        $status = $documento->getEstadoDocumento();
                                                        $badgeClass = 'secondary';
                                                        if ($status === 'Recebido') $badgeClass = 'success';
                                                        elseif ($status === 'Em processamento') $badgeClass = 'warning';
                                                        elseif ($status === 'Recusado') $badgeClass = 'danger';
                                                        ?>
                                                        <span class="badge badge-<?= $badgeClass ?>"><?= $status ?></span>
                                                    </td>

                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-primary light btn-rounded btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                Ações
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a class="dropdown-item" href="visualizarDocumento.php?id=<?= $documento->getIdDocumento() ?>">
                                                                        <i class="fa fa-eye me-2"></i>Visualizar
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item" href="downloadDocumento.php?id=<?= $documento->getIdDocumento() ?>">
                                                                        <i class="fa fa-download me-2"></i>Baixar
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item" href="editarDocumento.php?id=<?= $documento->getIdDocumento() ?>">
                                                                        <i class="fa fa-edit me-2"></i>Editar
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item text-danger" href="excluirDocumento.php?id=<?= $documento->getIdDocumento() ?>" onclick="return confirm('Tem certeza que deseja excluir este documento?')">
                                                                        <i class="fa fa-trash me-2"></i>Excluir
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">Nenhum documento solicitado.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>

                                </table>

                            </div>

                            <nav class="mt-4">
                                <ul class="pagination pagination-rounded">
                                    <li class="page-item disabled"><a class="page-link" href="#">Anterior</a></li>
                                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                                    <li class="page-item"><a class="page-link" href="#">Próxima</a></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalDocumentoSolicitar">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Solicitar Documento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <?php

                        require_once("../Modelo/DAO/AlunoDAO.php");
                        require_once("../Modelo/DAO/DisciplinaDAO.php");
                        require_once("../Modelo/DAO/CursoDAO.php");
                        require_once("../Modelo/DAO/TurmaDAO.php");

                        $daoCurso = new CursoDAO();
                        $cursos = $daoCurso->Mostrar();


                        $daoTurma = new TurmaDAO();
                        $turmas = $daoTurma->listarTodos();

                        $daoDisciplina = new DisciplinaDAO();
                        $disciplinas = $daoDisciplina->listarPorProfessor($professor);
                        ?>
                        <form action="../Controle/crudDocumento.php" method="POST">
                            <input type="hidden" name="estadoDocumento" value="Validado">
                            <input type="hidden" name="idAluno" value="">

                            <div class="col-md-12">
                                <label><strong>Tipo de Documento</strong></label>
                                <select id="tipoDocumentoSelect" class="form-control input-rounded" name="tipoDocumento" required>
                                    <option value="">Selecione o tipo de Documento</option>
                                    <option value="Mini Pauta">Mini Pauta</option>
                                </select>
                            </div>

                            <div class="campo-condicional mt-2">
                                <div class="row">
                                    <!-- Curso -->
                                    <div class="col-md-6">
                                        <label><strong>Curso</strong></label>
                                        <select id="cursoSelect" name="cursoDocumento" class="form-control input-rounded" required>
                                            <option value="">Selecione o curso</option>
                                            <?php foreach ($cursos as $curso): ?>
                                                <option value="<?= $curso->getNomeCurso() ?>"><?= htmlspecialchars($curso->getNomeCurso()) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- Disciplina -->
                                    <div class="col-md-6">
                                        <label><strong>Disciplina</strong></label>
                                        <select id="disciplinaSelect" name="disciplinaDocumento" class="form-control input-rounded" required>
                                            <option value="">Selecione a disciplina</option>
                                            <?php foreach ($disciplinas as $disciplina): ?>
                                                <option value="<?= $disciplina->getNomeDisciplina() ?>"><?= htmlspecialchars($disciplina->getNomeDisciplina()) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <!-- Classe -->
                                    <div class="col-md-4">
                                        <label><strong>Classe</strong></label>
                                        <select id="classeSelect" class="form-control input-rounded" name="classeDocumento" required>
                                            <option value="">Selecione</option>
                                            <option value="10">10ª</option>
                                            <option value="11">11ª</option>
                                            <option value="12">12ª</option>
                                            <option value="13">13ª</option>
                                        </select>
                                    </div>
                                    <!-- Período -->
                                    <div class="col-md-4">
                                        <label><strong>Período</strong></label>
                                        <select id="periodoSelect" class="form-control input-rounded" name="periodoDocumento" required>
                                            <option value="">Selecione</option>
                                            <option value="Manhã">Manhã</option>
                                            <option value="Tarde">Tarde</option>
                                        </select>
                                    </div>

                                    <!-- Turma -->
                                    <div class="col-md-4">
                                        <label><strong>Turma</strong></label>
                                        <select id="turmaSelect" class="form-control input-rounded" name="turmaDocumento" required>
                                            <option value="">Selecione a turma</option>
                                            <?php foreach ($turmas as $turma): ?>
                                                <option value="<?= $turma->getNomeTurma() ?>"><?= htmlspecialchars($turma->getNomeTurma()) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-rounded" name="solicitarDocumentoProfessor">Solicitar</button>

                            </div>
                        </form>
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
            if (menu.style.display === 'block') {
                menu.style.display = 'none';

            } else {
                menu.style.display = 'block';

            }
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tipoDocumento = document.getElementById("tipoDocumentoSelect");
            const camposCondicionais = document.querySelectorAll(".campo-condicional");

            tipoDocumento.addEventListener("change", function() {
                if (tipoDocumento.value === "Mini Pauta") {
                    camposCondicionais.forEach(campo => campo.classList.add("visivel"));
                } else {
                    camposCondicionais.forEach(campo => campo.classList.remove("visivel"));
                }
            });

            // Garante que os campos começam ocultos
            camposCondicionais.forEach(campo => campo.classList.remove("visivel"));
        });
    </script>



</body>

</html>