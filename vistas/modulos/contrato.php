<div class="scale-global">
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header" id="header">
                                <h3 class="card-title"> </h3>

                                <!--div class="row">
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-primary btn-block btn-xs"
                                            id="btnNuevo" onclick="mostrarform(true)" title="Crear nuevo contrato"><i
                                                class="fa fa-plus"></i>
                                            Nuevo</button>
                                    </div>
                                </div-->

                            </div>
                            <!-- /.card-header -->
                            <div class="card-body" id="listadoregistros">

                                <div class="row">

                                    <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                        <label>Fecha Inicio:</label>

                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="far fa-calendar-alt"></i>
                                                </span>
                                            </div>
                                            <input type="date" class="form-control" name="fecha_inicio"
                                                id="fecha_inicio" value="<?php echo date('Y-m-01'); ?>">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                        <label>Fecha Fin:</label>

                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="far fa-calendar-alt"></i>
                                                </span>
                                            </div>
                                            <input type="date" class="form-control" name="fecha_fin" id="fecha_fin"
                                                value="<?php echo date("Y-m-d"); ?>">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                        <label>Almacén:</label>

                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-store-alt"></i>
                                                </span>
                                            <select id="idsucursal" name="idsucursal" class="form-control select2">
                                            </select>
                                            </div>
                                        </div>
                                    </div>
                                    <!--div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                        <label>Estado:</label>

                                        <div class="input-group">
                                            <select id="estado" name="estado" class="form-control select2">
                                                <option value="Todos">Todos</option>
                                                <option value="Aceptado">Aceptado</option>
                                                <option value="Por Enviar">Por Enviar</option>
                                                <option value="Nota Credito">Nota de Crédito</option>
                                                <option value="Rechazado">Rechazado</option>
                                            </select>
                                        </div>
                                    </div-->

                                </div>

                                <table id="tbllistado" class="table table-tailpanel dt-responsive">
                                    <thead>
                                        <th>Fecha</th>
                                        <th>N° Documento</th>
                                        <th>Cliente / Razón Social</th>
                                        <th>N° Contrato</th>
                                        <th>Estado</th>
                                        <th>Forma de pago</th>
                                        <th>Monto</th>
                                        <th width="180px;">Acciones</th>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <th>Fecha</th>
                                        <th>N° Documento</th>
                                        <th>Cliente / Razón Social</th>
                                        <th>N° Contrato</th>
                                        <th>Estado</th>
                                        <th>Forma de pago</th>
                                        <th>Monto</th>
                                        <th width="180px;">Acciones</th>
                                    </tfoot>
                                </table>

                            </div>

                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
        <style>
            .pdf-card {
                border: 1px solid #e0e0e0;
                border-radius: 12px;
                padding: 20px 10px;
                background: #fff;
                transition: all 0.3s ease;
                cursor: pointer;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            }

            .pdf-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
            }

            .pdf-icon {
                font-size: 50px;
                color: #e74c3c;
                margin-bottom: 10px;
            }

            .pdf-card p {
                margin: 0;
                font-size: 14px;
                font-weight: 500;
                color: #333;
            }
        </style>
        <!-- modals -->
        <div class="modal fade" id="modal-ver-contrato" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Documentación del Contrato</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="min-height: 400px; overflow-y: auto;">
                        <div id="contratoContent" class="row text-center">

                            <div class="col-md-3 mb-4" id="btnDescargarContrato">
                                <div class="pdf-card">
                                    <input type="hidden" id="idventa" />
                                    <i class="fa fa-file-pdf pdf-icon"></i>
                                    <p>Contrato</p>
                                </div>
                            </div>

                            <div class="col-md-3 mb-4" id="btnDescargarActaEntrega">
                                <div class="pdf-card">
                                    <i class="fa fa-file-pdf pdf-icon"></i>
                                    <p>Acta de entrega</p>
                                </div>
                            </div>

                            <div class="col-md-3 mb-4" id="btnDescargarOrdenRecojo">
                                <div class="pdf-card">
                                    <i class="fa fa-file-pdf pdf-icon"></i>
                                    <p>Orden de recojo</p>
                                </div>
                            </div>

                            <div class="col-md-3 mb-4" id="btnDescargarCronogramaPagos">
                                <div class="pdf-card">
                                    <i class="fa fa-file-pdf pdf-icon"></i>
                                    <p>Cronograma de pagos</p>
                                </div>
                            </div>

                            <div class="col-md-3 mb-4" id="btnDescargarCompraVenta">
                                <div class="pdf-card">
                                    <i class="fa fa-file-pdf pdf-icon"></i>
                                    <p>Compra venta</p>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-retener-contrato" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Retener Contrato</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="form-retener-contrato" action="">
                        <div class="modal-body">
                            <div class="alert alert-warning" role="alert">
                                <strong>Advertencia:</strong> Retener un contrato significa que no se podrá realizar
                                ninguna acción adicional sobre él, como imprimir o descargar documentos relacionados.
                                Asegúrese de que esta es la acción correcta antes de proceder.
                            </div>
                            <input type="hidden" id="idventa_retenida" name="idventa" />
                            <div class="form-group">
                                <label for="motivoRetencion">Motivo de la retención:</label>
                                <textarea class="form-control" id="motivoRetencion" name="motivo" rows="3"
                                    placeholder="Ingrese el motivo por el cual se retiene este contrato..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger" id="confirmarRetencion">Sí, Retener</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-compra-venta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Compra Venta</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="form-compra-venta" action="">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="vendedor">Vendedor:</label>
                                        <select name="idvendedor" id="idvendedor" class="form-control"></select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="comprador">Comprador:</label>
                                        <div class="d-flex align-items-end">
                                            <input type="hidden" name="idcliente" id="idcliente" />
                                            <input type="text" name="comprador" id="comprador" class="form-control mr-2" readonly />
                                            <button type="button" class="btn btn-info mr-1" id="btnEditarComprador" title="Editar cliente"><i class="fas fa-edit"></i></button>
                                            <button type="button" class="btn btn-primary" id="btnNuevoComprador" title="Nuevo cliente"><i class="fas fa-user-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="idventa_compra_venta" name="idventa" />
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="monto_compra_venta">Monto:</label>
                                        <input type="text" class="form-control" id="monto_compra_venta" name="monto_compra_venta"
                                            placeholder="Ingrese el monto de la compra venta...">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger" id="confirmarCompraVenta">Sí, Realizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-cliente-compra-venta" tabindex="-1" role="dialog"
            aria-labelledby="modalClienteCompraVentaLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalClienteCompraVentaLabel">Cliente</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="form-cliente-compra-venta" action="">
                        <div class="modal-body">
                            <input type="hidden" name="idpersona" id="cliente_idpersona" value="">
                            <input type="hidden" name="tipo_persona" id="cliente_tipo_persona" value="Cliente">

                            <div class="form-group">
                                <label for="cliente_nombre">Nombre / Razón social:</label>
                                <input type="text" class="form-control" name="nombre" id="cliente_nombre" required>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-4">
                                    <label for="cliente_tipo_documento">Tipo Doc:</label>
                                    <select class="form-control" name="tipo_documento" id="cliente_tipo_documento">
                                        <option value="DNI">DNI</option>
                                        <option value="RUC">RUC</option>
                                        <option value="CE">CE</option>
                                        <option value="OTROS">OTROS</option>
                                    </select>
                                </div>
                                <div class="form-group col-8">
                                    <label for="cliente_num_documento">N° Documento:</label>
                                    <input type="text" class="form-control" name="num_documento" id="cliente_num_documento" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="cliente_telefono">Teléfono:</label>
                                <input type="text" class="form-control" name="telefono" id="cliente_telefono">
                            </div>

                            <div class="form-group">
                                <label for="cliente_direccion">Dirección:</label>
                                <input type="text" class="form-control" name="direccion" id="cliente_direccion">
                            </div>

                            <div class="form-group mb-0">
                                <label for="cliente_email">Email:</label>
                                <input type="email" class="form-control" name="email" id="cliente_email">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary" id="btnGuardarClienteCompraVenta">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="vistas/js/contratos.js"></script>