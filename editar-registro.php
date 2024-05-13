<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == "") {
    header("Location: index.php");
} else {

    $id = intval($_GET['id']);

    if (isset($_POST['submit'])) {
        $asistencia = $_POST['asistencia'];

        $sql_update_asistencia = "UPDATE asistencia SET asistencia = :asistencia WHERE id = :id";
        $query_update_asistencia = $dbh->prepare($sql_update_asistencia);
        $query_update_asistencia->bindParam(':asistencia', $asistencia, PDO::PARAM_INT);
        $query_update_asistencia->bindParam(':id', $id, PDO::PARAM_INT);
        $query_update_asistencia->execute();

        $msg = "Asistencia actualizada correctamente";
    }

    // Consulta para obtener el nombre y la fecha del registro
    $sql_info_registro = "SELECT nombre_estudiante, fecha FROM asistencia WHERE id = :id";
    $query_info_registro = $dbh->prepare($sql_info_registro);
    $query_info_registro->bindParam(':id', $id, PDO::PARAM_STR);
    $query_info_registro->execute();
    $registro_info = $query_info_registro->fetch(PDO::FETCH_OBJ);

    // Consulta para obtener el siguiente registro después del actual
    $sql_siguiente_registro = "SELECT id FROM asistencia WHERE id > :id ORDER BY id ASC LIMIT 1";
    $query_siguiente_registro = $dbh->prepare($sql_siguiente_registro);
    $query_siguiente_registro->bindParam(':id', $id, PDO::PARAM_INT);
    $query_siguiente_registro->execute();
    $siguiente_registro = $query_siguiente_registro->fetch(PDO::FETCH_OBJ);
?>

<!-- ========== TOP NAVBAR ========== -->
<?php include('includes/topbar.php'); ?>
<!-- ========== WRAPPER FOR BOTH SIDEBARS & MAIN CONTENT ========== -->
<div class="content-wrapper">
    <div class="content-container">

        <!-- ========== LEFT SIDEBAR ========== -->
        <?php include('includes/leftbar.php'); ?>
        <!-- /.left-sidebar -->

        <div class="main-page">

            <div class="container-fluid">
                <div class="row page-title-div">
                    <div class="col-md-6">
                        <h2 class="title">Editar Registro de Asistencia</h2>

                    </div>

                    <!-- /.col-md-6 text-right -->
                </div>
                <!-- /.row -->
                <div class="row breadcrumb-div">
                    <div class="col-md-6">
                        <ul class="breadcrumb">
                            <li><a href="dashboard.php"><i class="fa fa-home"></i> Inicio</a></li>
                            <li><a href="gestion-estudiantes.php">Estudiantes</a></li>
                            <li class="active">Editar Registro de Asistencia</li>
                        </ul>
                    </div>

                </div>
                <!-- /.row -->
            </div>
            <div class="container-fluid">

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel">
                            <div class="panel-heading">
                                <div class="panel-title">
                                    <h5>Completa la información de la asistencia</h5>
                                </div>
                            </div>
                            <div class="panel-body">
                                <?php if ($msg) { ?>
                                    <div class="alert alert-success left-icon-alert" role="alert">
                                        <strong>Proceso correcto! </strong><?php echo htmlentities($msg); ?>
                                    </div><?php } else if ($error) { ?>
                                    <div class="alert alert-danger left-icon-alert" role="alert">
                                        <strong>Hubo un inconveniente! </strong> <?php echo htmlentities($error); ?>
                                    </div>
                                <?php } ?>
                                <form class="form-horizontal" method="post">
                                    <?php
                                    // Mostrar el nombre y la fecha del registro
                                    ?>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Nombre del Estudiante:</label>
                                        <div class="col-sm-10">
                                            <p class="form-control-static"><?php echo htmlentities($registro_info->nombre_estudiante); ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Fecha:</label>
                                        <div class="col-sm-10">
                                            <p class="form-control-static"><?php echo htmlentities($registro_info->fecha); ?></p>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="asistencia" class="col-sm-2 control-label">Asistencia:</label>
                                        <div class="col-sm-10">
                                            <input type="number" class="form-control" id="asistencia" name="asistencia" value="<?php echo htmlentities($result->asistencia); ?>" required>
                                        </div>
                                    </div>
                                    <button type="submit" name="submit" class="btn btn-primary">Actualizar</button>
                                    <?php if ($siguiente_registro) { ?>
                                        <a href="editar-registro.php?id=<?php echo htmlentities($siguiente_registro->id); ?>" class="btn btn-success">Editar Siguiente</a>
                                    <?php } ?>
                                    <a href="agregarasistencia.php" class="btn btn-warning">Regresar a Agregar Asistencia</a>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /.col-md-12 -->
                </div>
            </div>
        </div>
        <!-- /.content-container -->
    </div>
    <!-- /.content-wrapper -->
    <?php include('includes/footer.php'); ?>
<?php } ?>
