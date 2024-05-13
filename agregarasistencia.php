<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == "") {
    header("Location: index.php");
} else {
    if (isset($_POST['submit'])) {
        $fecha = date("Y-m-d");

        // Obtener los primeros dos registros
        $sql_primeros_registros = "SELECT DISTINCT nombre_estudiante FROM asistencia LIMIT 2";
        $query_primeros_registros = $dbh->prepare($sql_primeros_registros);
        $query_primeros_registros->execute();
        $nombres = $query_primeros_registros->fetchAll(PDO::FETCH_COLUMN);

        // Insertar lista con los primeros dos registros
        foreach ($nombres as $nombre) {
            $sql_insert_fecha = "INSERT INTO asistencia (nombre_estudiante, fecha) VALUES (:nombre, :fecha)";
            $query_insert_fecha = $dbh->prepare($sql_insert_fecha);
            $query_insert_fecha->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $query_insert_fecha->bindParam(':fecha', $fecha, PDO::PARAM_STR);
            $query_insert_fecha->execute();
        }
    }
?>

?>

<link rel="stylesheet" type="text/css" href="assets/js/DataTables/datatables.min.css" />
<style>
    .circle {
        display: inline-block;
        width: 20px;
        height: 20px;
        border-radius: 50%;
    }

    .red-circle {
        background-color: red;
    }

    .green-circle {
        background-color: green;
    }

    .yellow-circle {
        background-color: yellow;
    }
</style>

<!-- ========== TOP NAVBAR ========== -->
<?php include('includes/topbar.php'); ?>
<!-- ========== WRAPPER FOR BOTH SIDEBARS & MAIN CONTENT ========== -->
<div class="content-wrapper">
    <div class="content-container">
        <?php include('includes/leftbar.php'); ?>

        <div class="main-page">
            <div class="container-fluid">
                <div class="row page-title-div">
                    <div class="col-md-6">
                        <h2 class="title">Gestión de Estudiantes</h2>
                    </div>
                </div>
                <div class="row breadcrumb-div">
                    <div class="col-md-6">
                        <ul class="breadcrumb">
                            <li><a href="dashboard.php"><i class="fa fa-home"></i> Inicio</a></li>
                            <li> Estudiantes</li>
                            <li class="active">Gestión de Estudiantes</li>
                        </ul>
                    </div>
                </div>
            </div>

            <section class="section">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <h5>Ver Información de Estudiante</h5>
                                    </div>
                                </div>
                                <div class="panel-body p-20">
                                    <form method="post">
                                        <button type="submit" name="submit" class="btn btn-primary">Insertar Lista</button>
                                    </form>
                                    <br>
                                    <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Nombre de Estudiante</th>
                                                <th>Fecha</th>
                                                <th>Asistencia</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT id, nombre_estudiante, fecha, asistencia FROM asistencia WHERE fecha = CURDATE()";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                            $cnt = 1;
                                            if ($query->rowCount() > 0) {
                                                foreach ($results as $result) {
                                                    $asistencia_class = ''; // Variable para almacenar la clase CSS según el valor de asistencia
                                                    switch ($result->asistencia) {
                                                        case 0:
                                                            $asistencia_class = 'red-circle';
                                                            break;
                                                        case 1:
                                                            $asistencia_class = 'green-circle';
                                                            break;
                                                        case 2:
                                                            $asistencia_class = 'yellow-circle';
                                                            break;
                                                        default:
                                                            $asistencia_class = '';
                                                    }
                                            ?>
                                                    <tr>
                                                        <td><?php echo htmlentities($cnt); ?></td>
                                                        <td><?php echo htmlentities($result->nombre_estudiante); ?></td>
                                                        <td><?php echo htmlentities($result->fecha); ?></td>
                                                        <td><span class="circle <?php echo $asistencia_class; ?>"></span></td>
                                                        <td><a href="editar-registro.php?id=<?php echo htmlentities($result->id); ?>" class="btn btn-info">Editar</a></td>
                                                    </tr>
                                            <?php
                                                    $cnt++;
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
        <?php include('includes/footer.php'); ?>
    </div>
</div>
<?php include('includes/footer.php'); ?>
<?php } ?>
