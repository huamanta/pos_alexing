<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Conceptos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Conceptos</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-outline-primary btn-xs"
                                        onclick="crearConcepto()"><i class="fa fa-plus"></i> Crear concepto</button>
                                </div>
                                <div class="col-md-6 d-flex align-items-center mt-2">
                                    <span class="mr-2">Mostrar</span>
                                    <select id="limit" class="form-control" style="width:100px">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>

                                    <span class="ml-2">Registros</span>

                                </div>
                                <div class="col-md-6 mt-2">
                                    <input type="text" id="search" class="form-control" placeholder="Buscar...">
                                </div>
                                <div class="col-md-12 mt-2">
                                    <table id="tbllistado" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Descripción</th>
                                                <th>Tipo concepto</th>
                                                <th>Categoria concepto</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyConceptos">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6"></div>
                                <div class="col-md-6">
                                    <div id="pagination"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<div class="modal fade" id="myModalCocepto">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Conceptos</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" role="form" name="formularioConcepto" id="formularioConcepto" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="idconcepto_movimiento" id="idconcepto_movimiento_form">
                    <div class="row">
                        <div class="form-group col-12">
                            <label for="name" class="control-label">Concepto movimiento <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" name="descripcion" id="descripcion_concepto"></textarea>
                        </div>
                        <div class="form-group col-lg-12">
                            <label>Tipo concepto<span class="text-danger">*</span></label>
                            <select id="tipo" name="tipo" class="form-control" data-live-search="true" required>
                                <option value="">Seleccione...</option>
                                <option value="ingresos">Ingresos</option>
                                <option value="egresos">Egresos</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-12" id="divCategoriaMov" hidden>
                            <label>Categoria concepto</label>
                            <select id="categoria_concepto" name="categoria_concepto" class="form-control"
                                data-live-search="true">
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button class="btn btn-primary" type="submit" id="btnGuardarC">Guardar</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script src="vistas/js/conceptos.js"></script>