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
                            <h2 class="title">Gestionar Calificaciones</h2>

                        </div>

                        <!-- /.col-md-6 text-right -->
                    </div>
                    <!-- /.row -->
                    <div class="row breadcrumb-div">
                        <div class="col-md-6">
                            <ul class="breadcrumb">
                                <li><a href="dashboard.php"><i class="fa fa-home"></i> Inicio</a></li>
                                <li> Resultados</li>
                                <li class="active">Gestionar Calificaciones</li>
                            </ul>
                        </div>

                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->

                <section class="section">
                    <div class="container-fluid">



                        <div class="row">
                            <div class="col-md-12">

                                <div class="panel">
                                    <div class="panel-heading">
                                        <div class="panel-title">
                                            <h5>Ver Información de Calificaciones</h5>
                                        </div>
                                    </div>
                                    <?php if ($msg) { ?>
                                        <div class="alert alert-success left-icon-alert" role="alert">
                                            <strong>Proceso Correcto! </strong><?php echo htmlentities($msg); ?>
                                        </div><?php } else if ($error) { ?>
                                        <div class="alert alert-danger left-icon-alert" role="alert">
                                            <strong>Algo salió mal! </strong> <?php echo htmlentities($error); ?>
                                        </div>
                                    <?php } ?>
                                    <div class="panel-body p-20">
                                        <!-- Formulario de búsqueda por ID Roll -->
                                        <form method="get" action="">
                                            <label for="idRoll">Buscar por Numero de Control:</label>
                                            <input type="text" id="idRoll" name="idRoll">
                                            <button type="submit">Buscar</button>
                                        </form>

                                        <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Nombre de Estudiante</th>
                                                    <th>Numero de control</th>
                                                    <th>Parcial</th>
                                                    <th>Fecha de Registro</th>
                                                    <th>Estado</th>
                                                    <th>Promedio de Parcial</th>
                                                    <th>Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $whereCondition = '';
                                                if (isset($_GET['idRoll'])) {
                                                    $idRoll = $_GET['idRoll'];
                                                    $whereCondition = " WHERE tblstudents.RollId LIKE '%$idRoll%'";
                                                }
                                                $sql = "SELECT DISTINCT tblstudents.StudentName, tblstudents.RollId, tblstudents.RegDate, tblstudents.StudentId, tblstudents.Status, tblclasses.ClassName, tblclasses.Section FROM tblresult JOIN tblstudents ON tblstudents.StudentId = tblresult.StudentId JOIN tblclasses ON tblclasses.id = tblresult.ClassId $whereCondition";
                                                $query = $dbh->prepare($sql);
                                                $query->execute();
                                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                $cnt = 1;
                                                if ($query->rowCount() > 0) {
                                                    foreach ($results as $result) {   ?>
                                                        <tr>
                                                            <td><?php echo htmlentities($cnt); ?></td>
                                                            <td><?php echo htmlentities($result->StudentName); ?></td>
                                                            <td><?php echo htmlentities($result->RollId); ?></td>
                                                            <td><?php echo htmlentities($result->ClassName); ?>(<?php echo htmlentities($result->Section); ?>)</td>
                                                            <td><?php echo htmlentities($result->RegDate); ?></td>
                                                            <td><?php if ($result->Status == 1) {
                                                                    echo htmlentities('Active');
                                                                } else {
                                                                    echo htmlentities('Blocked');
                                                                }
                                                                ?></td>
                                                            <td>
                                                                <!-- Mostrar promedio parcial -->
                                                                <?php
                                                                $sql2 = "SELECT AVG(marks) as promedio FROM tblresult WHERE StudentId=:studentId";
                                                                $query2 = $dbh->prepare($sql2);
                                                                $query2->bindParam(':studentId', $result->StudentId, PDO::PARAM_STR);
                                                                $query2->execute();
                                                                $row = $query2->fetch(PDO::FETCH_ASSOC);
                                                                $promedio = number_format((float)$row['promedio'], 2, '.', '');
                                                                echo $promedio;
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <a href="edit-result.php?stid=<?php echo htmlentities($result->StudentId); ?>" class="btn btn-info"><i class="fa fa-edit" title="Edit Record"></i> </a>
                                                            </td>
                                                        </tr>
                                                <?php $cnt = $cnt + 1;
                                                    }
                                                } ?>


                                            </tbody>
                                        </table>

                                        <!-- Botón para calcular el promedio general -->
                                        <button id="calcularPromedioGeneral" class="btn btn-primary">Calcular Promedio General</button>

                                        <!-- Mostrar el promedio general -->
                                        <div id="promedioGeneral"></div>

                                        <script>
                                            document.getElementById('calcularPromedioGeneral').addEventListener('click', function() {
                                                // Obtener todas las celdas de promedio parcial
                                                var promediosParciales = document.querySelectorAll('td:nth-child(7)');
                                                var sumatoriaPromediosParciales = 0;
                                                // Sumar los promedios parciales
                                                promediosParciales.forEach(function(celda) {
                                                    sumatoriaPromediosParciales += parseFloat(celda.textContent);
                                                });
                                                // Calcular el promedio general
                                                var promedioGeneral = sumatoriaPromediosParciales / promediosParciales.length;
                                                document.getElementById('promedioGeneral').innerHTML = "El promedio general es: " + promedioGeneral.toFixed(2);
                                            });
                                        </script>

                                        <!-- /.col-md-12 -->
                                    </div>
                                </div>
                            </div>
                            <!-- /.col-md-6 -->


                        </div>
                        <!-- /.col-md-12 -->
                    </div>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-md-6 -->

    </div>
    <!-- /.row -->

    </div>
    <!-- /.container-fluid -->
    </section>
    <!-- /.section -->

    </div>
    <!-- /.main-page -->



    </div>
    <!-- /.content-container -->
    </div>
    <!-- /.content-wrapper -->

    <?php include('includes/footer.php'); ?>



<?php } ?>
