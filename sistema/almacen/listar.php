<?php
// <editor-fold defaultstate="collapsed" desc="php">
require '../../includes/constants.php';
$usuario = new usuario();
$usuario->confirmar_miembro();
//require '../../includes/paginacion.php';
$pag = new paginacion();
$query = "select almacen.id, almacen.nombre nombre, count(producto_id) productos
    from almacen 
    left outer join producto_almacen on almacen.id = producto_almacen.almacen_id
    where empresa_id ={$_SESSION['usuario']['empresa_id']}";
if (isset($_GET['filtrar'])) {
    $query .=" and almacen.nombre like '%{$_GET['filtrar']}%'";
}
$query.=" group by almacen.id";
$pag->paginar($query, 5);
// </editor-fold>
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title><?php echo TITULO; ?></title>
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
        <!--[if lt IE 9]>
          <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <!-- Le styles -->
        <link href="<?php echo ROOT; ?>/css/bootstrap.min.css" rel="stylesheet"/>
        <link href="<?php echo ROOT; ?>/css/style.css" rel="stylesheet"/>
        <script src="<?php echo ROOT; ?>/js/jquery-1.7.1.min.js"></script>
        <script src="<?php echo ROOT; ?>/js/listado.js"></script>
    </head>
    <body>
        <?php include TEMPLATE . 'topbar.php'; ?>
        <div class="container">
            <div class="content">
                <div class="page-header">
                    <h1>Listar almacenes <small> almacenes disponibles</small> </h1>
                </div>
                <ul class="breadcrumb">
                    <li><a href="../usuario">Sistema</a><span class="divider">&raquo;</span></li>
                    <li><a href="listar.php">Almacen</a><span class="divider">&raquo;</span></li>
                    <li>Listar</li>
                </ul>
                <div class="row">
                    <div class="span16">
                        <?php if (count($pag->registros) > 0): ?>
                            <div class="pull-right">
                                <form class="">
                                    <label>Filtrar</label>
                                    <div class="input">
                                        <input type="search" name="filtrar" id="filtrar" placeholder="Buscar almacen" value="<?php echo isset($_GET['filtrar']) ? $_GET['filtrar'] : ""; ?>" />
                                    </div>
                                </form>
                            </div>
                            <table class="zebra-striped bordered-table">
                                <thead>
                                    <tr>
                                        <th><a href="<?php echo misc::url_sortable(); ?>">id</a></th>
                                        <th><a href="<?php echo misc::url_sortable("nombre"); ?>">Nombre</a></th>
                                        <th><a href="<?php echo misc::url_sortable("prductos"); ?>">Productos</a></th>
                                        <th>Operaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pag->registros as $registro): ?>
                                        <tr>
                                            <td><?php echo $registro['id']; ?></td>
                                            <td><?php echo $registro['nombre']; ?></td>
                                            <td><?php echo $registro['productos']; ?></td>
                                            <td>
                                                <a href="productos.php?id=<?php echo $registro['id']; ?>" class="btn small">Ver Productos</a>
                                                <a href="modificar.php?id=<?php echo $registro['id']; ?>" class="btn small info">Modificar</a>
                                                <a href="borrar.php?id=<?php echo $registro['id']; ?>" class="btn small danger">Eliminar</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5">
                                            <?php $pag->mostrar_paginado_lista(); ?>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        <?php else: ?>
                            <div class="alert-message">No hay resultados que mostrar</div>
                        <?php endif; ?>
                        <div class="actions">
                            <a href="crear.php" class="btn small primary">Crear almacen</a>
                            <a href="ordenDeCompra.php" class="btn small">Orden de compra</a>
                            <a href="productos_faltantes.php" class="btn small">Productos Faltantes</a>
                            <a href="../usuario" class="btn small ">Volver al menu</a>
                        </div>
                    </div>
                    <div class="hide">
                        <h3>Ayuda</h3>
                        <p>Listado de almacenes que emiten contratos</p>
                    </div>
                </div>
                <footer class="footer">
                    <div class="container">
                        <p>&copy; Aled Multimedia Solutions <?php echo date('Y'); ?> </p>
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>