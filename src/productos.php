<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$permiso = "productos";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
}
if (!empty($_POST)) {
    $alert = "";
    $id = $_POST['id'];
    $codigo = $_POST['codigo'];
    $producto = $_POST['producto'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];
    if (empty($codigo) || empty($producto) || empty($precio) || $precio <  0 || empty($cantidad) || $cantidad <  0) {
        $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Todo los campos son obligatorios
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
    } else {
        if (empty($id)) {
            $query = mysqli_query($conexion, "SELECT * FROM producto WHERE codigo = '$codigo'");
            $result = mysqli_fetch_array($query);
            if ($result > 0) {
                $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        El codigo ya existe
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            } else {
                $query_insert = mysqli_query($conexion, "INSERT INTO producto(codigo,descripcion,precio,cantidad) values ('$codigo', '$producto', '$precio', '$cantidad')");
                if ($query_insert) {
                    $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Producto registrado
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                } else {
                    $alert = '<div class="alert alert-danger" role="alert">
                    Error al registrar el producto, por falta de stock
                  </div>';
                }
            }
        } else {
            $query_update = mysqli_query($conexion, "UPDATE producto SET codigo = '$codigo', descripcion = '$producto', precio= $precio, cantidad = $cantidad WHERE codproducto = $id");
            if ($query_update) {
                $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Producto Modificado
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            } else {
                $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Error al modificar
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            }
        }
    }
}
include_once "includes/header.php";
?>
<div class="card shadow-lg">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <form action="" method="post" autocomplete="off" id="formulario">
                    <?php echo isset($alert) ? $alert : ''; ?>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="codigo" class=" text-dark font-weight-bold"><i class="fas fa-barcode"></i> Código de Barras</label>
                                <input type="text" placeholder="Ingrese código de barras" name="codigo" id="codigo" class="form-control">
                                <input type="hidden" id="id" name="id">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="producto" class=" text-dark font-weight-bold">Producto</label>
                                <input type="text" placeholder="Ingrese nombre del producto" name="producto" id="producto" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="precio" class=" text-dark font-weight-bold">Precio</label>
                                <input type="text" placeholder="Ingrese precio" class="form-control" name="precio" id="precio">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="cantidad" class=" text-dark font-weight-bold">Cantidad</label>
                                <input type="number" placeholder="Ingrese cantidad" class="form-control" name="cantidad" id="cantidad">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <input type="submit" value="Registrar" class="btn btn-primary" id="btnAccion">
                            <input type="button" value="Nuevo" onclick="limpiar()" class="btn btn-success" id="btnNuevo">
                            <button type="button" class="btn btn-primary " id="importar-excel" data-toggle="modal" data-target="#excelModal"><i class="fas fa-file-excel"></i> &nbsp; Importar desde Excel</button>
                            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#lowStockModal">
    <i class="fas fa-exclamation-triangle"></i> Productos con Bajo Stock
</button>
<a href="imprimir_codigos.php" class="btn btn-info" target="_blank">
        <i class="fas fa-print"></i> Imprimir Códigos de Barra
    </a>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="tbl">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include "../conexion.php";

                        $query = mysqli_query($conexion, "SELECT * FROM producto");
                        $result = mysqli_num_rows($query);
                        if ($result > 0) {
                            while ($data = mysqli_fetch_assoc($query)) { ?>
                                <tr>
                                    <td><?php echo $data['codproducto']; ?></td>
                                    <td><?php echo $data['codigo']; ?></td>
                                    <td><?php echo $data['descripcion']; ?></td>
                                    <td><?php echo $data['precio']; ?></td>
                                    <td><?php echo $data['cantidad']; ?></td>
                                    <td>
                                        <a href="#" onclick="editarProducto(<?php echo $data['codproducto']; ?>)" class="btn btn-primary"><i class='fas fa-edit'></i></a>

                                        <form action="eliminar_producto.php?id=<?php echo $data['codproducto']; ?>" method="post" class="confirmar d-inline">
                                            <button class="btn btn-danger" type="submit"><i class='fas fa-trash-alt'></i> </button>
                                        </form>
                                    </td>
                                </tr>
                        <?php }
                        } ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal para cargar archivo -->
<div class="modal fade" id="excelModal" tabindex="-1" role="dialog" aria-labelledby="excelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="excelModalLabel">Importar Productos desde Excel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="importForm" method="POST" enctype="multipart/form-data">
                    <input type="file" id="excelFile" name="archivo" accept=".csv, .xlsx, .xls" required>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="uploadExcel">Subir Archivo</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal para productos con bajo stock -->
<div class="modal fade" id="lowStockModal" tabindex="-1" role="dialog" aria-labelledby="lowStockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lowStockModalLabel">Productos con Bajo Stock</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody id="lowStockTableBody">
                            <!-- Aquí se cargarán los datos -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<div id="statusMessage" class="mt-3"></div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    $('#lowStockModal').on('show.bs.modal', function () {
        const tableBody = document.getElementById('lowStockTableBody');
        tableBody.innerHTML = '<tr><td colspan="5">Cargando...</td></tr>';
        
        fetch('./low_stock.php')
            .then(response => response.json())
            .then(data => {
                tableBody.innerHTML = '';
                if (data.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="5" class="text-center">No hay productos con bajo stock.</td></tr>';
                } else {
                    data.forEach((producto, index) => {
                        tableBody.innerHTML += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${producto.codigo}</td>
                                <td>${producto.descripcion}</td>
                                <td>${producto.precio}</td>
                                <td>${producto.cantidad}</td>
                            </tr>
                        `;
                    });
                }
            })
            .catch(error => {
                tableBody.innerHTML = `<tr><td colspan="5" class="text-danger">Error: ${error.message}</td></tr>`;
            });
    });
});
</script>

<script>
document.getElementById("uploadExcel").addEventListener("click", function () {
    const fileInput = document.getElementById("excelFile");
    const formData = new FormData();

    if (fileInput.files.length === 0) { 
        Swal.fire({ 
            position: 'center', 
            icon: 'error', 
            title: 'Por favor, selecciona un archivo para subir.', 
            showConfirmButton: false, timer: 2000 }); 
            return; }

    formData.append("archivo", fileInput.files[0]);

    fetch("./uploadProducts.php", {
        method: "POST",
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) { 
            Swal.fire({
                 position: 'center',
                 icon: 'success', 
                 title: data.message, 
                 showConfirmButton: false, 
                 timer: 2000 }); 
                 setTimeout(() => { location.reload(); }, 2000);
                } else { 
                    Swal.fire({
                         position: 'center', 
                         icon: 'error', 
                         title: `Error: ${data.error}`, 
                         showConfirmButton: false, 
                         timer: 2000 }); }
        $('#excelModal').modal('hide'); // Cerrar el modal
    })
    .catch(error => {
        document.getElementById("statusMessage").innerText = `Error: ${error.message}`;
    });
});
</script>


<?php include_once "includes/footer.php"; ?>