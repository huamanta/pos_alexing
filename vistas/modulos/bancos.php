<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Bancos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Facturacion y cajas</a></li>
                        <li class="breadcrumb-item active">Bancos</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
 <div class="row">
                                <div class="col-md-6 d-flex align-items-center">
                                    <span class="mr-2">Mostrar</span>
                                    <select id="limit" class="form-control" style="width:100px">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>

                                    <span class="ml-2">Registros</span>

                                </div>
                                <div class="col-md-6">
                                    <input type="text" id="search" class="form-control" placeholder="Buscar...">
                                </div>
                                <div class="col-md-12 mt-2">
                                    <table id="tbllistado" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Descripción</th>
                                                <th>N° cuenta</th>
                                                <th>CCI</th>
                                                <th>Saldo</th>
                                                <th style="width: 120px;">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyBancos">
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

<script src="vistas/js/bancos.js"></script>