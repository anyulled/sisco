<?php
// <editor-fold defaultstate="collapsed" desc="php">
require '../../includes/constants.php';
$pag = new paginacion();
$usuario = new usuario();
$usuario->confirmar_miembro();
$query = "select producto.*, categoria.nombre categoria from producto 
    inner join categoria on producto.categoria_id = categoria.id
    where empresa_id={$_SESSION['usuario']['empresa_id']}";
if (isset($_GET['filtrar'])) {
    $query.= " and producto.nombre like '%{$_GET['filtrar']}%' or categoria.nombre like '%{$_GET['filtrar']}%'";
}
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
                    <h1>Listar productos <small> productos disponibles</small> </h1>
                </div>
                <ul class="breadcrumb">
                    <li><a href="../usuario">Sistema</a><span class="divider">&raquo;</span></li>
                    <li><a href="listar.php">Producto</a><span class="divider">&raquo;</span></li>
                    <li>Listar</li>
                </ul>
                <div class="row">
                    <div class="span16">
                        <?php if (count($pag->registros) > 0): ?>
                            <div class="pull-right">
                                <form class="">
                                    <label>Filtrar</label>
                                    <div class="input">
                                        <input type="search" name="filtrar" id="filtrar" placeholder="Buscar producto" value="<?php echo isset($_GET['filtrar']) ? $_GET['filtrar'] : ""; ?>" />
                                    </div>
                                </form>
                            </div>
                            <table class="zebra-striped bordered-table">
                                <thead>
                                    <tr>
                                        <th>id</th>
                                        <th>Codigo</th>
                                        <th>Nombre</th>
                                        <th>Categoria</th>
                                        <th>Operaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pag->registros as $registro): ?>
                                        <tr>
                                            <td><?php echo $registro['id']; ?></td>
                                            <td><?php echo $registro['codigo']; ?></td>
                                            <td><?php echo $registro['nombre']; ?></td>
                                            <td><?php echo $registro['categoria']; ?></td>
                                            <td>
                                                <a href="../producto/almacen.php?producto=<?php echo $registro['id']; ?>" class="btn small">Existencia</a>
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
                            <a href="crear.php" class="btn small primary">Crear producto</a>
                            <a href="../almacen/ordenDeCompra.php" class="btn small">Orden de compra</a>
                            <a href="../usuario" class="btn small ">Volver al menu</a>
                        </div>
                    </div>
                    <div class="hide">
                        <h3>Ayuda</h3>
                        <p>Listado de productos que emiten contratos</p>
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