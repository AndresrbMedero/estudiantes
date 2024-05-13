<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == "") {
    header("Location: index.php");
} else {
?>

<link rel="stylesheet" type="text/css" href="assets/js/DataTables/datatables.min.css" />
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
                        <h2 class="title">Gestionar Parciales</h2>
                    </div>
                </div>
                <div class="row breadcrumb-div">
                    <div class="col-md-6">
                        <ul class="breadcrumb">
                            <li><a href="dashboard.php"><i class="fa fa-home"></i> Inicio</a></li>
                            <li> Parciales</li>
                            <li class="active">Gestionar Parciales</li>
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
                                        <h5>Ver información de Parcial</h5>
                                    </div>
                                </div>
                                <?php if ($msg) { ?>
                                    <div class="alert alert-success left-icon-alert" role="alert">
                                        <strong>Bien hecho</strong><?php echo htmlentities($msg); ?>
                                    </div><?php } else if ($error) { ?>
                                    <div class="alert alert-danger left-icon-alert" role="alert">
                                        <strong>Inconvenientes</strong> <?php echo htmlentities($error); ?>
                                    </div>
                                <?php } ?>
                                <div class="panel-body p-20">

                                    <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Nombre del Estudiante</th>
                                                <th>Inasistencias (0)</th>
                                                <th>Asistencias (1)</th>
                                                <th>Justificadas (2)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT nombre_estudiante, 
                                                        SUM(CASE WHEN asistencia = 0 THEN 1 ELSE 0 END) as inasistencias,
                                                        SUM(CASE WHEN asistencia = 1 THEN 1 ELSE 0 END) as asistencias,
                                                        SUM(CASE WHEN asistencia = 2 THEN 1 ELSE 0 END) as justificadas
                                                    FROM asistencia 
                                                    GROUP BY nombre_estudiante";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                            $cnt = 1;
                                            if ($query->rowCount() > 0) {
                                                foreach ($results as $result) {   ?>
                                                    <tr>
                                                        <td><?php echo htmlentities($cnt); ?></td>
                                                        <td><?php echo htmlentities($result->nombre_estudiante); ?></td>
                                                        <td><?php echo htmlentities($result->inasistencias); ?></td>
                                                        <td><?php echo htmlentities($result->asistencias); ?></td>
                                                        <td><?php echo htmlentities($result->justificadas); ?></td>
                                                    </tr>
                                            <?php $cnt = $cnt + 1;
                                                }
                                            } ?>


                                        </tbody>
                                    </table>


                                    <!-- /.col-md-12 -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

    </div>
    <!-- /.content-container -->
</div>
<!-- /.content-wrapper -->
<?php include('includes/footer.php'); ?>




<?php } ?>
