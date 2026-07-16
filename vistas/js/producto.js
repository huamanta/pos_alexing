var tabla;
let listarProductos = null;
toastr.options = {
  closeButton: true,
  progressBar: true,
  positionClass: "toast-bottom-right",
  timeOut: "3000",
};
//Función que se ejecuta al inicio
function init() {
  $("#body").addClass("sidebar-collapse sidebar-mini");
  listarProductos.load();

  $("#imagenmuestra").show();
  $("#imagenmuestra").attr("src", "files/productos/anonymous.png");
  $("#imagenactual").val("anonymous.png");

  $("#myModal").on("submit", function (e) {
    guardaryeditar(e);
  });

  $("#formularioTraslados").on("submit", function (e) {
    trasladarProducto(e);
  });
  $("#myModalCategoria").on("submit", function (e) {
    guardarCategoria(e);
  });

  $("#ModalUM").on("submit", function (e) {
    guardarum(e);
  });

  $("#formularioDesempaquetar").on("submit", function (e) {
    actualizarProductoEmpaquetado(e);
  });

  //cargamos los items al select almacen
  $.post("controladores/venta.php?op=selectSucursal", function (r) {
    $("#idsucursal").html(r);
    $("#idsucursal").select2("");
  });

  //Cargamos los items al select categoria
  $.post("controladores/producto.php?op=selectCategoria", function (r) {
    $("#idcategoria").html(r);
    $("#idcategoria").select2("");
  });

  //marca
  $.post("controladores/producto.php?op=selectMarca", function (r) {
    $("#idmarca").html(r);
    $("#idmarca").select2("");
  });

  //marca
  $.post("controladores/producto.php?op=selectModelo", function (r) {
    $("#idmodelo").html(r);
    $("#idmodelo").select2("");
  });

  $.post("controladores/producto.php?op=selectUnidadMedida", function (r) {
    $("#idunidad_medida").html(r);
    $("#idunidad_medida").select2("");
  });

  $.post("controladores/producto.php?op=selectRubro", function (r) {
    $("#idrubro").html(r);
    $("#idrubro").select2("");
  });

  $.post("controladores/producto.php?op=selectCondicionVenta", function (r) {
    $("#idcondicionventa").html(r);
    $("#idcondicionventa").select2("");
  });

  //Mostramos los sucursales
  $.post("controladores/producto.php?op=sucursales", function (r) {
    $("#sucursales").html(r);
  });

  //cargamos los items al select almacen
  // $.post("controladores/venta.php?op=selectSucursal3", function (r) {
  //   $("#idsucursal2").html(r);
  //   $("#idsucursal2").select2("");
  // });

  //cargamos los items al select almacen
  $.post("controladores/venta.php?op=selectSucursal", function (r) {
    $("#idsucursal3").html(r);
    $("#idsucursal3")
      .select2({
        placeholder: "Seleccionar Almacén ...",
        allowClear: true,
      })
      .val(null)
      .trigger("change");
  });

  //cargamos los items al select almacen
  $.post("controladores/venta.php?op=selectSucursal", function (r) {
    $("#idsucursal4").html(r);
    $("#idsucursal4")
      .select2({
        placeholder: "Seleccionar Destino ...",
        allowClear: true,
      })
      .val(null)
      .trigger("change");
  });

  // $("#idsucursal2").change(listar);

  $("#navAlmacenActive").addClass("treeview active");
  $("#navAlmacen").addClass("treeview menu-open");
  $("#navProducto").addClass("active");

  cargarComboProductos();
}

function cargarComboProductos() {
  var idp = $("#idsucursal3").val();

  //cargamos los items al select almacen
  $.post("controladores/venta.php?op=selectProductoS&idp=" + idp, function (r) {
    $("#idproducto2").html(r);
    $("#idproducto2").select2("");
  });
}

function cargarComboProductos2() {
  var idp = $("#idsucursal4").val();

  //cargamos los items al select almacen
  $.post("controladores/venta.php?op=selectProductoS&idp=" + idp, function (r) {
    $("#idproducto3").html(r);
    $("#idproducto3").select2("");
  });
}

function guardarcat(e) {
  e.preventDefault(); //no se activara la accion predeterminada
  //$("#btnGuardar").prop("disabled",true);
  var formData = new FormData($("#formularioCategoria")[0]);

  $.ajax({
    url: "controladores/producto.php?op=guardarcategoria",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,

    success: function (datos) {
      Swal.fire({
        title: "Categoria",
        icon: "success",
        text: datos,
      });
      //cargamos los items al select cliente
      $.post("controladores/producto.php?op=selectCategoria", function (r) {
        $("#idcategoria").html(r);
        $("#idcategoria").select2("");
      });

      $.post(
        "controladores/producto.php?op=mostrarUltimaCategoria",
        function (data, status) {
          data = JSON.parse(data);

          seleccionarCategoria(data.nombre, data.idcategoria);
        },
      );
    },
  });

  $("#ModalCategorias").modal("hide");

  limpiarCategoria();
}

function limpiarCategoria() {
  $("#idcategoria").val("");
}
function cancelarformcat() {
  limpiarCategoria();
}

function seleccionarCategoria(nombre, idcategoria) {
  $("#idcategoria").val(idcategoria);
  $("#idcategoria").select2("");
}

function obtenerIdCategoriaVehiculoEnSelect() {
  let idVehiculo = "";
  $("#idcategoria option").each(function () {
    const texto = (($(this).text() || "") + "").trim().toUpperCase();
    if (texto === "VEHICULO") {
      idVehiculo = $(this).val();
      return false;
    }
  });
  return idVehiculo;
}

function sincronizarTipoProductoConCategoria() {
  const idSeleccionado = ("" + $("#idcategoria").val() || "").trim();
  const idVehiculo = obtenerIdCategoriaVehiculoEnSelect();

  if (idVehiculo && idSeleccionado === idVehiculo) {
    $("#tipo_producto").val("Vehiculo");
  } else {
    $("#tipo_producto").val("Producto");
  }
}
//Función limpiar
function limpiar() {
  $("#codigo").val("");
  $("#nombre").val("");
  $("#descripcion").val("");
  $("#stock").val("0");
  $("#stockMinimo").val("0");
  $("#precio").val("");
  $("#precioB").val("");
  $("#precioC").val("");
  $("#precioD").val("");
  $("#precioE").val("");
  $("#margenpubl").val("");
  $("#margendes").val("");
  $("#margenp1").val("");
  $("#margenp2").val("");
  $("#margendist").val("");
  $("#utilprecio").val("");
  $("#utilprecioB").val("");
  $("#utilprecioC").val("");
  $("#utilprecioD").val("");
  $("#utilprecioE").val("");
  $("#fecha").val("");
  $("#fecha_hora").val("");
  $("#imagenmuestra").attr("src", "files/productos/anonymous.png");
  $("#imagenactual").val("anonymous.png");
  $("#print").hide();
  $("#idproducto").val("");
  $("#idcategoria").val("");
  $("#idcategoria").select2("");
  $("#idunidad_medida").val("");
  $("#idunidad_medida").select2("");
  $("#idrubro").val("");
  $("#idrubro").select2("");
  $("#idcondicionventa").val("");
  $("#idcondicionventa").select2("");
  $("#registrosan").val("");
  $("#fabricante").val("");
  $("#modelo").val("");
  $("#nserie").val("");
  $("#placa").val("");
  $("#color").val("");
  $("#motor").val("");
  $("#permiso_circulacion").val("");
  $("#anio_fabricacion").val("");
  $("#tipo_vehiculo").val("");
  $("#clase_vehiculo").val("");
  $("#propietario_vehiculo").val("");
  $("#stockMaximo").val("0");
  $("#controla_stock").val("Si");
  $("#alerta_stock").val("Si");
  $("#porc").val("");
  $("#precioCompra").val("");
  $("#margenUtilidad").val("");

  $("#preciocigv").val("");

  $("#comisionV").val("");
}

function limpiarTraslado() {
  $("#cantidadT").val("");
  $("#idsucursal3").val("");
  $("#idsucursal3")
    .select2({
      placeholder: "Seleccionar Almacén ...",
      allowClear: true,
    })
    .val(null)
    .trigger("change");
  $("#idsucursal4").val("");
  $("#idsucursal4")
    .select2({
      placeholder: "Seleccionar Destino ...",
      allowClear: true,
    })
    .val(null)
    .trigger("change");
  $("#idproducto2").val("");
  $("#idproducto2").select2("");
  $("#idproducto3").val("");
  $("#idproducto3").select2("");
}

function limpiarDesempaquetado() {
  $("#idproductoE").val("");
  $("#idproductoE").select2("");
  $("#idproductoD").val("");
  $("#idproductoD").select2("");
  $("#cantidadE").val("");
  $("#cantidadD").val("");
  $("#productoE").val("");
  $("#productoD").val("");

  var label = document.querySelector("#productoDesempaquetar");
  label.textContent = "0";
}

function llenarProductos() {
  $.post(
    "controladores/venta.php?op=selectProductoDesempaquetar",
    function (r) {
      $("#idproductoE").html(r);
      $("#idproductoE")
        .select2({
          placeholder: "Seleccionar Producto ...",
          allowClear: true,
        })
        .val(null)
        .trigger("change");
    },
  );

  $.post(
    "controladores/venta.php?op=selectProductoDesempaquetar",
    function (r) {
      $("#idproductoD").html(r);
      $("#idproductoD")
        .select2({
          placeholder: "Seleccionar Producto ...",
          allowClear: true,
        })
        .val(null)
        .trigger("change");
    },
  );
}

function stockProductoE() {
  var idproductoE = $("#idproductoE").val();
  $.post(
    "controladores/producto.php?op=mostrarStockProductoE",
    { idproductoE: idproductoE },
    function (data, status) {
      data = JSON.parse(data);

      $("#productoE").val(data.stock);

      var label = document.querySelector("#productoDesempaquetar");
      label.textContent = data.stock + " - UM: " + data.unidadmedida;
    },
  );
}

function stockProductoD() {
  var idproductoD = $("#idproductoD").val();
  $.post(
    "controladores/producto.php?op=mostrarStockProductoD",
    { idproductoD: idproductoD },
    function (data, status) {
      data = JSON.parse(data);

      $("#productoD").val(data.stock);
    },
  );
}

function cancelarform() {
  // Obtener todos los campos de entrada del modal
  var campos = document.getElementById("myModal").querySelectorAll("input");

  // Iterar sobre cada campo y establecer su valor en vacío
  campos.forEach(function (campo) {
    campo.value = "";
  });
}

$("#controla_stock").change(function () {
    if ($(this).val() == "Si") {
        $("#stock").attr("readonly", "readonly");
        $("#stock").val('0');
    } else {
        $("#stock").removeAttr("readonly", "readonly");
    }
});

function mostrar(idproducto) {
  $.post(
    "controladores/producto.php?op=mostrar",
    { idproducto: idproducto },
    function (response, status) {
      const data = JSON.parse(response);
      console.log(data);

      // Abrir modal y llenar los campos
      $("#myModal").modal("show");

      $("#idsucursal").val(data.idsucursal).select2("");
      $("#idcategoria").val(data.idcategoria).select2("");
      sincronizarTipoProductoConCategoria();
      $("#idunidad_medida").val(data.idunidad_medida).select2("");
      $("#idrubro").val(data.idrubro).select2("");
      $("#idcondicionventa").val(data.idcondicionventa).select2("");
      $("#registrosan").val(data.registrosan);
      $("#idmodelo").val(data.idmodelo).select2("");
      $("#idmarca").val(data.idmarca).select2("");
      $("#codigo").val(data.codigo);
      $("#nombre").val(data.nombre);
      $("#stock").val(data.stock);
      $("#stockMinimo").val(data.stock_minimo);
      $("#precio").val(data.precio);
      $("#preciocigv").val(data.preciocigv);
      $("#precioB").val(data.precioB);
      $("#precioC").val(data.precioC);
      $("#precioD").val(data.precioD);
      $("#precioE").val(data.precioE);
      $("#margenpubl").val(data.margenpubl);
      $("#margendes").val(data.margendes);
      $("#margenp1").val(data.margenp1);
      $("#margenp2").val(data.margenp2);
      $("#margendist").val(data.margendist);
      $("#utilprecio").val(data.utilprecio);
      $("#utilprecioB").val(data.utilprecioB);
      $("#utilprecioC").val(data.utilprecioC);
      $("#utilprecioD").val(data.utilprecioD);
      $("#utilprecioE").val(data.utilprecioE);
      $("#precioCompra").val(data.precio_compra);
      $("#fecha_hora").val(data.fecha);
      $("#descripcion").val(data.descripcion);
      $("#imagenmuestra")
        .show()
        .attr("src", "files/productos/" + data.imagen);
      $("#imagenactual").val(data.imagen);
      $("#idproducto").val(data.idproducto);
      $("#idinventario").val(data.idinventario);
      $("#idserie").val(data.idserie);
      $("#nserie").val(data.numero_serie);
      $("#placa").val(data.placa || "");
      $("#color").val(data.color || "");
      $("#motor").val(data.numero_motor || "");
      $("#permiso_circulacion").val(data.permiso_circulacion || "");
      $("#anio_fabricacion").val(data.anio_fabricacion || "");
      $("#tipo_vehiculo").val(data.tipo_vehiculo || "");
      $("#clase_vehiculo").val(data.clase_vehiculo || "");
      $("#propietario_vehiculo").val(data.propietario_vehiculo || "");
      $("#stockMaximo").val(data.stock_maximo || "0");
      $("#controla_stock").val(data.controla_stock || "Si");
      $("#alerta_stock").val(data.alerta_stock || "Si");
      $("#tipoigv").val(data.proigv);
      $("#comisionV").val(data.comisionV);
      generarbarcode();
      // =================== CONFIGURAR MODO DE CÓDIGO ===================
      if (data.codigo && data.codigo.trim() !== "") {
        // Ya tiene código: mostrar como bloqueado, pero editable con confirmación
        $("#codigo").prop("readonly", true);
        $("#modoCodigo").prop("checked", false);
        $("label[for='modoCodigo']").html(
          '<i class="fa fa-lock text-danger"></i> Manual (bloqueado)',
        );

        // Guardamos un flag para saber que proviene de un producto con código
        $("#codigo").data("tieneCodigo", true);
      } else {
        // No tiene código: permitir edición libre
        $("#codigo").prop("readonly", false);
        $("#modoCodigo").prop("checked", false);
        $("label[for='modoCodigo']").html(
          '<i class="fa fa-edit text-success"></i> Manual',
        );
        $("#codigo").data("tieneCodigo", false);
      }

      // $("#myModal")
      //   .off("shown.bs.modal")
      //   .on("shown.bs.modal", function () {
      //     var idsucursalSeleccionada = $("#idsucursal2").val();
      //     console.log("Sucursal activa:", idsucursalSeleccionada);

      //     $.post(
      //       "controladores/producto.php?op=sucursales",
      //       { idsucursal: idsucursalSeleccionada, idproducto: data.idproducto },
      //       function (r) {
      //         $("#sucursales").html(r);
      //       },
      //     );
      //   });
    },
  );
}

function calcularPrecioIGV() {
  var numero = $("#precio").val();

  var numIgv = numero * 1.18;

  $("#preciocigv").val(numIgv.toFixed(2));
}

//Función Listar
function pintarProductos(data, permissions) {
  
  let html = "";

  if (data.length === 0) {

    html = `
            <tr>
                <td colspan="10" class="text-center">
                    No se encontraron registros
                </td>
            </tr>
        `;

    $("#tbody_productos").html(html);
    return;
  }

  data.forEach(item => {
    let btnActivarDesactivar = (permissions.desactivar) ?
      (item.condicion === 1) ?
        `<button class="btn btn-danger btn-xs" onclick="desactivar(${item.idproducto})"><i class="fas fa-times-circle"></i></button>` :
        `<button class="btn btn-info btn-xs" onclick="activar(${item.idproducto})"><i class="fas fa-check"></i></button>`
      : ''

    html += `
            <tr>
                <td>${item.codigo ?? ''}</td>
                <td>${item.nombre ?? ''}</td>
                <td>${item.stock}</td>
                <td>${item.stock_minimo}</td>
                <td>${item.numero_serie || '-'}</td>
                <td>${item.numero_motor || '-'}</td>
                <td>S/ ${parseFloat(item.precio).toFixed(2)}</td>
                <td>S/ ${parseFloat(item.precio_compra).toFixed(2)}</td>
                <td>
                    ${item.color}
                </td>
                <td>
                    ${item.condicion === 1
        ? '<span class="badge badge-success">Activo</span>'
        : '<span class="badge badge-danger">Inactivo</span>'
      }
                </td>
                <td>
                    ${permissions.desactivar ? `<button class="btn btn-warning btn-xs" onclick="mostrar(${item.idproducto})"><i class="fas fa-edit"></i></button>` : ''}
		                <!--button class="btn btn-primary btn-xs" onclick="entradaSalida(${item.idproducto}, ${item.idsucursal})"><i class="fas fa-archive"></i></button-->
                    ${permissions.configurar ? `<button class="btn btn-success btn-xs" onclick='config(${JSON.stringify(item).replace(/'/g, "\\'")})'><i class="fas fa-cog"></i></button>` : ''}
                    ${permissions.vencimientos ? `<button class="btn btn-info btn-xs" onclick="fechaVencimiento(${item.idproducto})"><i class="fa fa-list"></i></button>` : ''}  
		                ${btnActivarDesactivar}
                    ${permissions.eliminar ? `<button class="btn btn-danger btn-xs" onclick="eliminarProducto(${item.idproducto})"><i class="fas fa-trash"></i></button>` : ''}
                </td>

            </tr>
        `;

  });

  $("#tbody_productos").html(html);

}

listarProductos = new FluentPaginator({
  url: "controladores/producto.php?op=listar",
  renderTabla: pintarProductos
});


toastr.options = {
  closeButton: false,
  progressBar: true,
  positionClass: "toast-top-right",
  timeOut: "4000",
  extendedTimeOut: "1000",
};

// let timeout = null;

// $(document).on("input", "#stock_filtro", function () {
//   clearTimeout(timeout);

//   timeout = setTimeout(() => {
//     const valor = parseInt($("#stock_filtro").val());

//     if (!isNaN(valor) && valor >= 0) {
//       toastr.info(
//         `Listando productos con stock menor o igual a ${valor}`,
//         "Filtro aplicado",
//       );
//     } else {
//       toastr.warning("Listando todos los productos", "Sin filtro");
//     }
//   }, 1200); // Espera 1.2 segundos después de escribir
// });

function generarbarcode(e = null) {
  if (e) e.preventDefault(); // Solo prevenir si viene de un evento

  var codigo = $("#codigo").val();

  if (codigo && codigo.trim() !== "") {
    JsBarcode("#barcode", codigo);
    $("#print").show();
  } else {
    $("#print").hide();
  }
}

// Función para imprimir el Código de barras
function imprimir() {
  $("#print").printArea();
}

$(document).ready(function () {
  $("#codigo").on("input", generarbarcode);
});

$(document).ready(function () {
  $("#formulario").on("keydown", function (event) {
    if (event.key === "Enter") {
      event.preventDefault(); // Evita que el formulario se envíe con Enter
    }
  });
});

function validarDatosProducto() {
  const idrubro = $("#idrubro").val();
  const nombre = $("#nombre").val().trim();
  const precio = parseFloat($("#precio").val());
  const codigo = $("#codigo").val().trim();
  const idcategoria = $("#idcategoria").val();
  const idunidad_medida = $("#idunidad_medida").val();
  const idmarca = $("#idmarca").val();
  const idmodelo = $("#idmodelo").val();
  const idcondicionventa = $("#idcondicionventa").val();

  if (!idrubro) {
    Swal.fire("Error", "El rubro es obligatorio", "error");
    return false;
  }

  if (!idcategoria) {
    Swal.fire("Error", "La categoría es obligatoria", "error");
    return false;
  }

  if (!idunidad_medida) {
    Swal.fire("Error", "La unidad de medida es obligatoria", "error");
    return false;
  }

  if (!idmarca) {
    Swal.fire("Error", "La marca es obligatoria", "error");
    return false;
  }

  if (!idmodelo) {
    Swal.fire("Error", "El modelo es obligatorio", "error");
    return false;
  }

  if (!idcondicionventa) {
    Swal.fire("Error", "La condición de venta es obligatoria", "error");
    return false;
  }

  if (nombre === "") {
    Swal.fire("Error", "El nombre del producto es obligatorio", "error");
    return false;
  }

  if (isNaN(precio) || precio < 0) {
    Swal.fire(
      "Error",
      "El precio debe ser un número válido mayor o igual a 0",
      "error",
    );
    return false;
  }

  if (codigo === "") {
    Swal.fire("Error", "El código del producto es obligatorio", "error");
    return false;
  }

  return true;
}

function guardaryeditar(e) {
  e.preventDefault(); // No se activará la acción predeterminada del evento

  if (!validarDatosProducto()) {
    return;
  }
  var btnGuardar = $("#btnGuardarP");

  if ($("#tipo_producto").val() === "Vehiculo") {
    const idVehiculo = obtenerIdCategoriaVehiculoEnSelect();
    if (idVehiculo) {
      $("#idcategoria").val(idVehiculo).trigger("change");
    }
  }

  // Verificar si el botón ya está deshabilitado
  if (btnGuardar.prop("disabled")) {
    return;
  }

  // Deshabilitar el botón para evitar múltiples clics
  btnGuardar.prop("disabled", true);

  var formData = new FormData($("#formulario")[0]);

  $.ajax({
    url: "controladores/producto.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (datos) {
      const data = JSON.parse(datos);
      if (!data.success) {
        Swal.fire({ title: "Producto", icon: "error", text: data.message });
        return;
      }
      Swal.fire({ title: "Cliente", icon: "success", text: data.message });
      $("#myModal").modal("hide");
      listarProductos.load();
      limpiarFormulario();
      limpiarProducto();
    },
    complete: function () {
      // Habilitar el botón nuevamente después de la respuesta del servidor
      btnGuardar.prop("disabled", false);
    },
    error: function () {
      // Si ocurre un error, asegurarse de que el botón se habilite nuevamente
      btnGuardar.prop("disabled", false);
    },
  });
}

// Función para manejar el envío del formulario
/*function guardaryeditar(e) {
  e.preventDefault(); // No se activará la acción predeterminada del evento
  var formData = new FormData($("#formulario")[0]);

  $.ajax({
    url: "controladores/producto.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function(datos) {
      Swal.fire({
        title: "Producto",
        icon: "success",
        text: datos,
      });

      $("#myModal").modal("hide");
      tabla.ajax.reload();
      limpiarFormulario();
      limpiarProducto(); // Llama a la función para limpiar el formulario
    },
  });
}*/

function restaurarImagen() {
  $("#imagenmuestra").show();
  $("#imagenmuestra").attr("src", "files/productos/anonymous.png");
  $("#imagenactual").val("anonymous.png");
}

$(document).ready(function () {
  $("#restaurarImagen").click(function () {
    restaurarImagen();
  });
});

function nuevo() {
  $("#myModal").modal("show");
  limpiarFormulario();
  limpiarProducto();
  $("#tipo_producto").val("Producto");

  // var idsucursalSeleccionada = $("#idsucursal2").val();
  // $("#idsucursal2").val(idsucursalSeleccionada);

  // $.post(
  //   "controladores/producto.php?op=sucursales",
  //   { idsucursal: idsucursalSeleccionada },
  //   function (r) {
  //     $("#sucursales").html(r);
  //   },
  // );
}

/*function guardaryeditar(e) {
  e.preventDefault(); //No se activará la acción predeterminada del evento
  var formData = new FormData($("#formulario")[0]);

  $.ajax({
    url: "controladores/producto.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,

    success: function (datos) {
      Swal.fire({
        title: "Producto",
        icon: "success",
        text: datos,
      });

      $("#myModal").modal("hide");
      tabla.ajax.reload();
      limpiarFormulario();
      limpiarProducto(); // Llama a la función para limpiar el formulario
    },
  });
}

//función para generar el código de barras
function generarbarcode() {
  codigo = $("#codigo").val();
  JsBarcode("#barcode", codigo);
  $("#print").show();
}

//Función para imprimir el Código de barras
function imprimir() {
  $("#print").printArea();
}*/

function limpiarFormulario() {
  // Resetea el formulario
  document.getElementById("formulario").reset();
}
function limpiarProducto() {
  $("#idproducto").val("");
  $("#idcategoria").val("");
  $("#idcategoria").select2("");
  $("#tipo_producto").val("Producto");
  $("#imagenmuestra").attr("src", "files/productos/anonymous.png");
  $("#imagenactual").val("anonymous.png");
}

$(document).on("change", "#idcategoria", function () {
  sincronizarTipoProductoConCategoria();
});

$(document).on("change", "#tipo_producto", function () {
  if ($(this).val() !== "Vehiculo") {
    return;
  }

  const idVehiculo = obtenerIdCategoriaVehiculoEnSelect();
  if (idVehiculo) {
    $("#idcategoria").val(idVehiculo).trigger("change");
  }
});

function trasladarProducto(e) {
  e.preventDefault(); //no se activara la accion predeterminada
  //$("#btnGuardar").prop("disabled",true);
  var formData = new FormData($("#formularioTraslados")[0]);

  $.ajax({
    url: "controladores/producto.php?op=trasladarProducto",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,

    success: function (datos) {
      Swal.fire({
        title: "",
        icon: "success",
        text: datos,
      });
      $("#myModalTraslados").modal("hide");
      listar();
    },
  });
  limpiarTraslado();
}

function actualizarProductoEmpaquetado(e) {
  e.preventDefault(); //no se activara la accion predeterminada
  //$("#btnGuardar").prop("disabled",true);
  var formData = new FormData($("#formularioDesempaquetar")[0]);

  $.ajax({
    url: "controladores/producto.php?op=actualizarProductoEmpaquetado",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,

    success: function (datos) {
      Swal.fire({
        title: "",
        icon: "success",
        text: datos,
      });
      $("#myModalDesempaquetar").modal("hide");
      listar();
    },
  });

  limpiarDesempaquetado();
}

//Función para desactivar registros
function desactivar(idproducto) {
  Swal.fire({
    title: "¿Desactivar?",
    text: "¿Está seguro Que Desea Desactivar el Producto?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si",
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      $.post(
        "controladores/producto.php?op=desactivar",
        { idproducto: idproducto },
        function (response) {
          const data = JSON.parse(response);
          if (!data.success) {
            Swal.fire({ title: "Producto", icon: "error", text: data.message });
            return;
          }
          Swal.fire({ title: "Producto", icon: "success", text: data.message });
          listarProductos.load();
        },
      );
    }
  });
}

//Función para desactivar registros
function activar(idproducto) {
  Swal.fire({
    title: "Activar?",
    text: "¿Está seguro Que Desea Activar el Producto?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si",
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      $.post(
        "controladores/producto.php?op=activar",
        { idproducto: idproducto },
        function (response) {
          const data = JSON.parse(response);
          if (!data.success) {
            Swal.fire({ title: "Producto", icon: "error", text: data.message });
            return;
          }
          Swal.fire({ title: "Producto", icon: "success", text: data.message });
          listarProductos.load();
        },
      );
    }
  });
}

function verimagen(
  idproducto,
  imagen,
  nombre,
  stock,
  categoria,
  registrosan,
  rubro,
  condicionventa,
  precio,
  precio_compra,
  precioB,
  precioC,
  precioD,
  fabricante,
  descripcion,
) {
  $("#modalDetalleProducto").modal("show");

  // Mostrar imagen
  $("#detalleImagenProducto").attr("src", "files/productos/" + imagen);

  // Construir contenido
  let html = `
  <div class="col-md-6 mb-2">
    <div class="border rounded p-2"><strong>Nombre:</strong> ${nombre}</div>
  </div>
  <div class="col-md-6 mb-2">
    <div class="border rounded p-2"><strong>Stock:</strong> ${stock}</div>
  </div>
  <div class="col-md-6 mb-2">
    <div class="border rounded p-2"><strong>Categoría:</strong> ${categoria}</div>
  </div>
  <div class="col-md-6 mb-2">
    <div class="border rounded p-2"><strong>Precio:</strong> S/ ${precio}</div>
  </div>
  <div class="col-md-6 mb-2">
    <div class="border rounded p-2"><strong>Precio Compra:</strong> S/ ${precio_compra}</div>
  </div>
  <div class="col-md-6 mb-2">
    <div class="border rounded p-2"><strong>Fabricante:</strong> ${fabricante}</div>
  </div>
  <div class="col-md-12 mb-2">
    <div class="border rounded p-2"><strong>Descripción:</strong> ${descripcion}</div>
  </div>
`;
  $("#detalleProductoContenido").html(html);

  // Obtener precios adicionales
  $.post(
    "controladores/producto.php?op=precios_adicionales",
    { idproducto: idproducto },
    function (data) {
      $("#detallePreciosAdicionales").html(data);
    },
  );
}

// Cerrar y resetear el modal al hacer clic en el botón
$(document).on("click", "#btnCerrarModalProducto", function () {
  $("#modalDetalleProducto").modal("hide");

  // Esperar a que termine la animación antes de resetear
  setTimeout(() => {
    // Resetear imagen
    $("#detalleImagenProducto").attr("src", "");

    // Vaciar contenido de detalles y precios
    $("#detalleProductoContenido").html("");
    $("#detallePreciosAdicionales").html("<i>Cargando...</i>");

    // Volver a activar el tab de imagen
    $('#detalleProductoTabs a[href="#tab-imagen"]').tab("show");
  }, 300); // espera a que el modal se oculte completamente
});

/*=============================================
SUBIENDO LA FOTO DEL PRODUCTO
=============================================*/

$("#imagen").change(function () {
  var imagen = this.files[0];

  /*=============================================
    VALIDAMOS EL FORMATO DE LA IMAGEN SEA JPG O PNG
    =============================================*/

  if (imagen["type"] != "image/jpeg" && imagen["type"] != "image/png") {
    $(".nuevaImagen").val("");

    swal({
      title: "Error al subir la imagen",
      text: "¡La imagen debe estar en formato JPG o PNG!",
      type: "error",
      confirmButtonText: "¡Cerrar!",
    });
  } else if (imagen["size"] > 2000000) {
    $(".nuevaImagen").val("");

    swal({
      title: "Error al subir la imagen",
      text: "¡La imagen no debe pesar más de 2MB!",
      type: "error",
      confirmButtonText: "¡Cerrar!",
    });
  } else {
    var datosImagen = new FileReader();
    datosImagen.readAsDataURL(imagen);

    $(datosImagen).on("load", function (event) {
      var rutaImagen = event.target.result;

      $("#imagenmuestra").attr("src", rutaImagen);
    });
  }
});

var m = 0;
var p = 0;
var precioVentaBase = 0; // Para mostrar y calcular precios de venta
var costoCompraBase = 0; // Para calcular márgenes de utilidad reales
var configuracionesState = [];

function config(producto) {
  console.log(producto);

  $("#ModalConfigProducto").modal("show");
  $("#p-producto").html(
    '<span class="badge bg-info" style="font-size:20px">' +
    producto.nombre +
    "</span>",
  );
  $("#idproductoconfig").val(producto.idproducto);

  // Inicializamos con los valores del producto principal
  costoCompraBase = parseFloat(producto.precio_compra) || 0;
  precioVentaBase = parseFloat(producto.precio) || 0;

  $("#p-unitario").html(
    '<span class="badge bg-info" style="font-size:20px">Precio Venta Lote: S/. ' +
    precioVentaBase.toFixed(2) +
    "</span>",
  );

  configuracionesState = [];
  listarDataCofig(producto.idproducto);
}

function listarDataCofig(idproducto) {
  $.ajax({
    url:
      "controladores/producto.php?op=listCofiguration&idproducto=" + idproducto,
    type: "GET",
    success: function (response) {
      var response = JSON.parse(response);

      if (response.length > 0) {
        // Actualizamos las bases globales con lo que viene del FIFO
        costoCompraBase = parseFloat(response[0].costo_compra_unitario) || 0;
        precioVentaBase = parseFloat(response[0].precio_venta_unitario) || 0;

        $("#p-unitario").html(
          '<span class="badge bg-info" style="font-size:20px">Precio Venta Lote: S/. ' +
          precioVentaBase.toFixed(2) +
          "</span>",
        );
      }

      configuracionesState = response.length
        ? response.map((item) => ({
          id: item.id || 0,
          codigo_extra: item.codigo_extra || "",
          contenedor: item.contenedor || "",
          cantidad_contenedor: parseFloat(item.cantidad_contenedor) || 1,
          precio_venta: item.precio_venta
            ? parseFloat(item.precio_venta)
            : null,
          precio_venta_manual: item.precio_venta_manual
            ? parseFloat(item.precio_venta_manual)
            : null,
          precios: item.precios || [],
        }))
        : [];

      p = configuracionesState.length;
      renderizarTabla();
    },
  });
}

function renderizarTabla() {
  var html = "";
  configuracionesState.forEach((item, idx) => {
    let cantidad = parseFloat(item.cantidad_contenedor) || 1;
    let esUnidad =
      cantidad === 1 ||
      (item.contenedor && item.contenedor.toUpperCase().trim() === "UNIDAD");

    // El precio sugerido se basa en el PRECIO DE VENTA del lote
    let precioCalculado = (precioVentaBase * cantidad).toFixed(2);
    let precioFinal =
      item.precio_venta_manual || item.precio_venta || precioCalculado;

    html += `<tr id="fila${idx}">
            <td><input type="text" class="form-control" value="${item.codigo_extra}" onchange="actualizarConfig(${idx}, 'codigo_extra', this.value)"></td>
            <td><input type="text" class="form-control" value="${item.contenedor}" onchange="actualizarConfig(${idx}, 'contenedor', this.value)"></td>
            <td><input type="number" class="form-control" value="${cantidad}" onchange="actualizarConfig(${idx}, 'cantidad_contenedor', this.value); renderizarTabla();"></td>
            <td>
                <input type="number" class="form-control" value="${parseFloat(precioFinal).toFixed(2)}" onchange="actualizarConfig(${idx}, 'precio_venta_manual', this.value); renderizarTabla();">
                <small class="text-muted">Auto: S/ ${precioCalculado}</small>
            </td>
            <td class="text-center"><i class="fa fa-plus text-primary" onclick="configurarPrecios(${idx})" style="cursor:pointer"></i></td>
            <td class="text-center"><i class="fa fa-trash text-danger" onclick="eliminarFila(${idx}, ${item.id})" style="cursor:pointer"></i></td>
        </tr>`;
  });
  $("#detalle").html(html);
}

function resetearPrecio(idx) {
  if (configuracionesState[idx]) {
    delete configuracionesState[idx].precio_venta_manual;
    renderizarTabla();
    Swal.fire({
      icon: "success",
      title: "Precio restaurado",
      text: "Se restauró el precio automático",
      timer: 1500,
      showConfirmButton: false,
    });
  }
}

function actualizarConfig(index, campo, valor) {
  if (!configuracionesState[index]) return;

  let cantidadContenedor =
    parseFloat(configuracionesState[index].cantidad_contenedor) || 1;
  let esUnidad =
    cantidadContenedor === 1 ||
    (configuracionesState[index].contenedor &&
      configuracionesState[index].contenedor.toUpperCase().trim() === "UNIDAD");

  if (campo === "cantidad_contenedor") {
    let cantidad = parseFloat(valor) || 1;
    if (cantidad <= 0) cantidad = 1;
    configuracionesState[index][campo] = cantidad;

    // Solo resetear precio si no es UNIDAD
    let nuevaEsUnidad = cantidad === 1;
    if (!nuevaEsUnidad) {
      delete configuracionesState[index].precio_venta_manual;
      delete configuracionesState[index].precio_venta;
    }
  } else if (campo === "precio_venta_manual") {
    // UNIDAD no permite edición manual
    if (esUnidad) {
      Swal.fire({
        icon: "warning",
        title: "Precio bloqueado",
        text: "El contenedor UNIDAD usa precio automático del lote FIFO",
        timer: 2000,
      });
      return;
    }

    let valorNum = parseFloat(valor) || 0;

    if (valorNum <= 0) {
      delete configuracionesState[index].precio_venta_manual;
      delete configuracionesState[index].precio_venta;
      return;
    }

    // Guardar precio manual para contenedores no-UNIDAD
    configuracionesState[index].precio_venta_manual = valorNum;
  } else {
    configuracionesState[index][campo] = valor;
  }
}

// Agrega una fila de precio
function agregarItem(precio = {}) {
  const index = $(".precio-item").length; // Solo para generar ID único del select
  const idSelect = `idnombre_p_${index}`;

  // Si es el primer item, agregar encabezados
  if ($(".encabezado-precio").length === 0) {
    $("#precios").append(`
      <div class="col-md-12 row mb-1 font-weight-bold text-center encabezado-precio">
        <div class="col-md-4">Tipo de Precio</div>
        <div class="col-md-3">Valor Venta (S/)</div>
        <div class="col-md-3">Margen %</div>
        <div class="col-md-2">Acción</div>
      </div>
    `);
  }

  // HTML de la fila
  // NOTA: Se usa oninput="calcularMargen(this)" y oninput="actualizarMargen(this)"
  $("#precios").append(`
    <div class="col-md-12 row mb-2 precio-item">
      <input type="hidden" class="form-control" name="config_id_precio[]" value="${precio.id || ""}">
      <div class="col-md-4">
        <select id="${idSelect}" name="idnombre_p[]" class="form-control idnombre_p select-precio"></select>
      </div>
      <div class="col-md-3">
        <input type="number" class="form-control precio_precio" name="precio_configuracion[]" value="${precio.precio || ""}" 
          placeholder="Precio" step="0.01" min="0" oninput="calcularMargen(this)">
      </div>
      <div class="col-md-3 mb-2">
        <input type="number" class="form-control margen_utilidad" value="${precio.margen_utilidad || 0}" placeholder="Margen %"
        min="0" step="0.01" oninput="actualizarMargen(this)">
      </div>
      <div class="col-md-2 text-center">
        <button type="button" class="btn btn-danger btn-sm" onclick="eliminarItemPrecio(this)">
          <i class="fa fa-trash"></i>
        </button>
      </div>
    </div>
  `);

  // Cargar opciones del select
  $.post("controladores/producto.php?op=selectNombrePrecios", function (r) {
    $(`#${idSelect}`).html(r);

    if (precio.idnombre_p) {
      $(`#${idSelect}`).val(precio.idnombre_p);
    }

    $(`#${idSelect}`).select2();
  });
}

function calcularMargen(input) {
  let indexConfig = $("#idproductoPrecio").val();
  let precioVentaAdicional = parseFloat(input.value) || 0;

  let configActual = configuracionesState[indexConfig];
  let cantidadContenedor = parseFloat(configActual.cantidad_contenedor) || 1;

  // COSTO TOTAL DEL CONTENEDOR = Costo Compra Base x Cantidad
  let costoTotalContenedor = costoCompraBase * cantidadContenedor;

  let item = $(input).closest(".precio-item");

  if (costoTotalContenedor > 0) {
    // Fórmula: ((Venta - Costo) / Costo) * 100
    let margen =
      ((precioVentaAdicional - costoTotalContenedor) / costoTotalContenedor) *
      100;
    item.find(".margen_utilidad").val(margen.toFixed(2));
  }
}

function actualizarMargen(input) {
  let indexConfig = $("#idproductoPrecio").val();
  let margen = parseFloat(input.value) || 0;

  let configActual = configuracionesState[indexConfig];
  let cantidadContenedor = parseFloat(configActual.cantidad_contenedor) || 1;

  // BASE DE COSTO PARA EL CÁLCULO
  let costoTotalContenedor = costoCompraBase * cantidadContenedor;

  let item = $(input).closest(".precio-item");
  if (costoTotalContenedor <= 0) return;

  // Calculamos el precio de venta necesario para ese margen sobre el costo
  let precioVentaCalculado = costoTotalContenedor * (1 + margen / 100);

  item.find(".precio_precio").val(precioVentaCalculado.toFixed(2));
}

$("#nuevo_precio").click(function () {
  agregarItem({});
});

function agregarCofiguracion() {
  p++;
  configuracionesState.push({
    id: 0,
    codigo_extra: "",
    contenedor: "",
    cantidad_contenedor: 1,
    // No incluir precio_venta_manual aquí, se agrega solo si el usuario lo edita
    precios: [],
  });
  renderizarTabla();
  comprobarData();
}

function eliminarFila(index, id) {
  if (id !== 0) {
    $.ajax({
      url: "controladores/producto.php?op=eliminarCofiguration&idconfig=" + id,
      type: "GET",
      success: function () {
        console.log("Eliminado correctamente");
      },
      error: function (error) {
        console.log(error.responseText);
      },
    });
  }
  configuracionesState.splice(index, 1);
  p--;
  renderizarTabla();
  comprobarData();
}

function configurarPrecios(index) {
  $("#ModalPreciosProducto").modal("show");
  $("#precios").html("");
  let data = configuracionesState[index];
  if (data && data.precios && data.precios.length > 0) {
    data.precios.forEach((item) => {
      agregarItem(item);
    });
  } else {
    agregarItem({});
  }
  $("#idproductoPrecio").val(index);
}

$("#savePrecios").on("submit", function (e) {
  e.preventDefault();
  let index = $("#idproductoPrecio").val();
  let precios = [];
  let error = false;

  $("#precios .precio-item").each(function (i) {
    let fila = i + 1;
    let idnombre_p = $(this).find(".idnombre_p").val();
    let precio = $(this).find(".precio_precio").val();

    if (!idnombre_p) {
      toastr.warning(
        "Error",
        `El campo "Nombre precio" es obligatorio en la fila ${fila}.`,
        "error",
      );
      error = true;
      return false;
    }

    if (!precio) {
      toastr.warning(
        "Error",
        `El campo "Precio" es obligatorio en la fila ${fila}.`,
        "error",
      );
      error = true;
      return false;
    }

    let margen_utilidad = $(this).find(".margen_utilidad").val() || 0; // ✅ Declarado antes de usarlo
    let idPrecio = $(this).find("input[name='config_id_precio[]']").val() || 0;

    precios.push({
      id: parseInt(idPrecio),
      idnombre_p,
      precio: parseFloat(precio),
      margen_utilidad: parseFloat(margen_utilidad), // ✅ sin error
    });
  });

  if (error) return;

  configuracionesState[index].precios = precios;
  $("#ModalPreciosProducto").modal("hide");
  Swal.fire("¡Precios guardados!", "", "success");
});

function validarDatos() {
  for (let i = 0; i < configuracionesState.length; i++) {
    const item = configuracionesState[i];
    const fila = i + 1;

    if (!item.codigo_extra || item.codigo_extra.trim() === "") {
      toastr.warning(
        "Error",
        `El campo "Código extra" es obligatorio en la fila ${fila}.`,
        "error",
      );
      return false;
    }

    if (!item.contenedor || item.contenedor.trim() === "") {
      toastr.warning(
        "Error",
        `El campo "Contenedor" es obligatorio en la fila ${fila}.`,
        "error",
      );
      return false;
    }

    if (!item.cantidad_contenedor || item.cantidad_contenedor <= 0) {
      // ← modificar validación
      toastr.warning(
        "Error",
        `La cantidad debe ser mayor a 0 en la fila ${fila}.`,
        "error",
      );
      return false;
    }

    // ← ELIMINAR la validación de precio_venta (ya no es necesaria)
  }

  // Todo bien
  return true;
}

$("#saveCofigurtion").submit(function (e) {
  e.preventDefault();

  if (configuracionesState.length === 0) {
    toastr.warning(
      "Error",
      "Debe agregar al menos una configuración.",
      "error",
    );
    return;
  }

  let validarDatdos = validarDatos();
  if (!validarDatdos) {
    return;
  }

  $.ajax({
    url: "controladores/producto.php?op=saveCofiguration",
    type: "POST",
    data: {
      configuraciones: JSON.stringify(configuracionesState),
      idproducto: $("#idproductoconfig").val(),
    },
    success: function () {
      Swal.fire({
        title: "¡Configuración guardada!",
        text: "La configuración se ha guardado correctamente.",
        icon: "success",
        timer: 2000,
        showConfirmButton: false,
      });
      listarDataCofig($("#idproductoconfig").val());
      listar();
      $("#ModalConfigProducto").modal("hide");
    },
    error: function (error) {
      Swal.fire("Error", "No se pudo guardar la configuración.", "error");
      console.log(error.responseText);
    },
  });
});

function eliminarItemPrecio(btn) {
  $(btn).closest(".precio-item").remove();
}

function comprobarData() {
  if (p === 0) {
    $("#detalle").html(
      `<tr><td colspan="6" class="text-center">No hay configuraciones</td></tr>`,
    );
  }
}

function fechaVencimiento(id) {
  $("#fechavencimiento-modal").modal("show");

  // Cargar información general del producto
  $.ajax({
    url: "controladores/producto.php?op=listarvencimiento&id=" + id,
    type: "get",
    dataType: "json",
    success: function (json) {
      // Título del modal
      $(".modal-title").html(`
                <h2 style="margin:5px 0 0 0; font-weight:bold; color:#2c3e50; font-size:20px">
                    ${json.nombre_producto}
                </h2>
            `);

      // Mostrar total de stock
      $("#totareal").html(json.total_stock + " Unid.");

      // Destruir DataTable si existe
      if ($.fn.DataTable.isDataTable("#tbllistadoKardex")) {
        $("#tbllistadoKardex").DataTable().destroy();
      }

      // Inicializar DataTable con server-side processing
      $("#tbllistadoKardex").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url:
            "controladores/producto.php?op=listarvencimiento_datatable&id=" +
            id,
          type: "GET",
          error: function (xhr, error, thrown) {
            Swal.fire("Error", "No se pudo cargar los datos", "error");
          },
        },
        columns: [
          { data: "numero", orderable: false },
          { data: "fecha_ingreso" },
          { data: "fvencimiento" },
          { data: "dias_restantes", orderable: true },
          { data: "cantidad" },
          { data: "stock_lote" },
          { data: "nlote" },
          { data: "precio_compra" },
          { data: "precio_venta" },
        ],
        createdRow: function (row, data, dataIndex) {
          if (data.clase) {
            $(row).addClass(data.clase);
          }
        },
        dom: "Bfrtip",
        buttons: ["copy", "excel", "pdf", "print"],
        responsive: true,
        pageLength: 10,
        order: [[1, "asc"]],
        language: {
          processing: "Procesando...",
          lengthMenu: "Mostrar _MENU_ registros",
          zeroRecords: "No se encontraron resultados",
          emptyTable: "Ningún dato disponible en esta tabla",
          info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
          infoEmpty:
            "Mostrando registros del 0 al 0 de un total de 0 registros",
          infoFiltered: "(filtrado de un total de _MAX_ registros)",
          search: "Buscar:",
          paginate: {
            first: "Primero",
            last: "Último",
            next: "Siguiente",
            previous: "Anterior",
          },
        },
      });
    },
    error: function () {
      Swal.fire(
        "Error",
        "No se pudo cargar la información del producto",
        "error",
      );
    },
  });
}

function imprimirCodigosBarras() {
  $("#ModalCodigosProducto").modal("show");
  $.ajax({
    url:
      "controladores/producto.php?op=listCofiguration&idproducto=" +
      $("#idproductoconfig").val(),
    type: "GET",
    data: "",
    contentType: false,
    processData: false,
    success: function (response) {
      var response = JSON.parse(response);
      console.log(response);
      var html = "";
      if (response != "") {
        $.each(response, function (i, item) {
          html += '<div class="col-md-4 text-center">';
          html += "<p>" + response[i].contenedor + "</p>";
          html += '<svg id="codigo' + i + '"></svg></div>';
        });
        $("#codigos").html(html);

        // Generar y convertir a PNG
        $.each(response, function (i, item) {
          JsBarcode("#codigo" + i, response[i].codigo_extra, {
            displayValue: true,
            format: "CODE128",
            lineColor: "#000",
            width: 2,
            height: 40,
            margin: 0,
          });

          // Convertir SVG a PNG
          setTimeout(function () {
            var svg = document.querySelector("#codigo" + i);
            var svgData = new XMLSerializer().serializeToString(svg);
            var canvas = document.createElement("canvas");
            var ctx = canvas.getContext("2d");
            var img = new Image();
            img.onload = function () {
              canvas.width = img.width;
              canvas.height = img.height;
              ctx.drawImage(img, 0, 0);
              var pngFile = canvas.toDataURL("image/png");
              $(svg).replaceWith(
                '<img src="' + pngFile + '" style="width:100%;" />',
              );
            };
            img.src =
              "data:image/svg+xml;base64," +
              btoa(unescape(encodeURIComponent(svgData)));
          }, 200);
        });
      }
    },
  });
}

function imprSelec(nombre) {
  var ficha = document.getElementById(nombre);
  var ventimp = window.open("", "popimpr");
  ventimp.document.write("<html><head><title>Impresión</title></head><body>");
  ventimp.document.write(ficha.innerHTML);
  ventimp.document.write("</body></html>");
  ventimp.document.close();

  // Dar tiempo a que las imágenes carguen
  setTimeout(function () {
    ventimp.print();
    ventimp.close();
    $("#ModalCodigosProducto").modal("hide");
  }, 1000);
}

function guardarCategoria(e) {
  e.preventDefault(); // No se activará la acción predeterminada del evento

  var formData = new FormData($("#formularioCategoria")[0]);

  $.ajax({
    url: "controladores/categoria.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,

    success: function (datos) {
      Swal.fire({
        title: "Categoria",
        icon: "success",
        text: datos,
      }).then(function () {
        $("#myModalCategoria").modal("hide");
        reloadCategoria();
      });
    },
  });

  limpiarcat();
  // location.reload();
}

$("#myModalCategoria").on("hidden.bs.modal", function () {
  $("body").addClass("modal-open");
});

function reloadCategoria() {
  $.post("controladores/producto.php?op=selectCategoria", function (r) {
    $("#idcategoria").html(r);
    $("#idcategoria").select2("");
  });
}

//Función limpiar
function limpiarcat() {
  $("#nombrecat").val("");
  $("#idcategoria").val("");
}

function guardarum(e) {
  e.preventDefault(); //No se activará la acción predeterminada del evento
  //$("#btnGuardar").prop("disabled",true);
  var formData = new FormData($("#formularioUM")[0]);

  $.ajax({
    url: "controladores/unidadmedida.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,

    success: function (datos) {
      Swal.fire({
        title: "UnidadMedida",
        type: "success",
        text: datos,
      });
      $("#ModalUM").modal("hide");
      $.post("controladores/producto.php?op=selectUnidadMedida", function (r) {
        $("#idunidad_medida").html(r);
        $("#idunidad_medida").select2("");
      });
    },
  });
  limpiar();
}

$("#ModalUM").on("hidden.bs.modal", function () {
  $("body").addClass("modal-open");
});

function seleccionarum(nombre, idunidad_medidad) {
  $("#idunidad_medidad").val(idunidad_medidad);
  $("#idunidad_medidad").select2("");
}

// Mostrar alerta con SweetAlert
function mostrarAlerta(mensaje) {
  Swal.fire({
    icon: "error",
    title: mensaje,
    timer: 2000, // Duración de la alerta en milisegundos
    showConfirmButton: false,
    timerProgressBar: true,
  });
}

// Función para limpiar los caracteres no numéricos y puntos decimales
function filtrarEntrada(valor) {
  return valor.replace(/[^0-9.]/g, "");
}

function entradaSalida(idproducto, idsucursal) {
  $("#myModalEntradas").modal("show");
  $("#input-idproducto").val(idproducto);
  $("#input-idsucursal").val(idsucursal);

  $.post(
    "controladores/producto.php?op=listarLotesFifo",
    {
      idproducto: idproducto,
      idsucursal: idsucursal,
    },
    function (r) {
      $("#idfifo").html(r);
      // Reiniciar los campos cuando se abre el modal
      $("#tipo_movimiento").val("");
      actualizarPreciosBox();
    },
  );
}

$("#formularioIngreso").submit(function (e) {
  e.preventDefault();

  var tipo = $("#tipo_movimiento").val();
  var lote = $("#idfifo").val();

  // Validar que si es entrada y crea nuevo lote, tenga precios
  if (tipo == "0" && (lote == "0" || lote == "")) {
    var precioVenta = $("#precio_venta").val();
    var precioCompra = $("#precio_compra").val();

    if (
      !precioVenta ||
      !precioCompra ||
      precioVenta <= 0 ||
      precioCompra <= 0
    ) {
      Swal.fire({
        title: "Advertencia",
        icon: "warning",
        text: "Debe ingresar precio de compra y venta para crear un nuevo lote",
      });
      return false;
    }
  }

  var formData = new FormData(this);

  $.ajax({
    url: "controladores/producto.php?op=movimientoEntradaSalida",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (datos) {
      var datos = JSON.parse(datos);
      if (datos.status == 1) {
        listar();
        Swal.fire({
          title: "Guardado",
          icon: "success",
          text: datos.message,
        });
      } else {
        Swal.fire({
          title: "Error",
          icon: "error",
          text: datos.message,
        });
      }
      $("#myModalEntradas").modal("hide");
      resetearFormulario2();
    },
    error: function (xhr, status, error) {
      console.error("Error AJAX:", error);
      console.error("Respuesta:", xhr.responseText);
      Swal.fire({
        title: "Error",
        icon: "error",
        text: "Error al procesar la solicitud: " + error,
      });
    },
  });
});

function actualizarPreciosBox() {
  let tipo = $("#tipo_movimiento").val();
  let lote = $("#idfifo").val();

  // Por defecto ocultamos los precios
  $(".precio-box").hide();

  // ENTRADA (0)
  if (tipo == "0") {
    // Si selecciona "Crear nuevo lote" (value="0") o no selecciona nada
    if (!lote || lote === "" || lote === "0") {
      $(".precio-box").show();

      // Hacer los campos requeridos solo cuando se muestran
      $("#precio_venta").prop("required", true);
      $("#precio_compra").prop("required", true);
    } else {
      // Lote existente seleccionado - quitar required
      $("#precio_venta").prop("required", false);
      $("#precio_compra").prop("required", false);
    }
  } else {
    // SALIDA (1) - quitar required
    $("#precio_venta").prop("required", false);
    $("#precio_compra").prop("required", false);
  }
}

// Eventos para actualizar la visibilidad de precios
$("#tipo_movimiento").on("change", actualizarPreciosBox);
$("#idfifo").on("change", actualizarPreciosBox);

function limpiarIngreso() {
  $("#formularioIngreso")[0].reset();
  $(".precio-box").hide();
}

function resetearFormulario2() {
  $("#formularioIngreso")[0].reset();
  $(".precio-box").hide();
}

document.addEventListener("DOMContentLoaded", function () {
  function calcularMargen1(precioCompra, precioVenta) {
    if (precioCompra > 0) {
      return (((precioVenta - precioCompra) / precioCompra) * 100).toFixed(2);
    }
    return 0;
  }

  function actualizarMargen1(inputId, margenId) {
    function recalcular() {
      let precioCompra =
        parseFloat(document.getElementById("precioCompra").value) || 0;
      let precioVenta = parseFloat(document.getElementById(inputId).value) || 0;
      document.getElementById(margenId).value = calcularMargen1(
        precioCompra,
        precioVenta,
      );
    }

    document.getElementById(inputId).addEventListener("input", recalcular);
    document
      .getElementById("precioCompra")
      .addEventListener("input", recalcular); // Asegura actualización
  }

  // Aplicar función a todos los márgenes
  actualizarMargen1("precio", "margenpubl");
  actualizarMargen1("precioB", "margendes");
  actualizarMargen1("precioC", "margenp1");
  actualizarMargen1("precioD", "margenp2");
  actualizarMargen1("precioE", "margendist");
});

function calcularUtilidades() {
  // Obtener los valores de los inputs
  var precio = parseFloat(document.getElementById("precio").value) || 0;
  var precioB = parseFloat(document.getElementById("precioB").value) || 0;
  var precioC = parseFloat(document.getElementById("precioC").value) || 0;
  var precioD = parseFloat(document.getElementById("precioD").value) || 0;
  var precioE = parseFloat(document.getElementById("precioE").value) || 0;
  var precioCompra =
    parseFloat(document.getElementById("precioCompra").value) || 0;

  // Calcular las utilidades
  var utilprecio = precio - precioCompra;
  var utilprecioB = precioB - precioCompra;
  var utilprecioC = precioC - precioCompra;
  var utilprecioD = precioD - precioCompra;
  var utilprecioE = precioE - precioCompra;

  // Mostrar las utilidades en los campos correspondientes
  document.getElementById("utilprecio").value = utilprecio.toFixed(2);
  document.getElementById("utilprecioB").value = utilprecioB.toFixed(2);
  document.getElementById("utilprecioC").value = utilprecioC.toFixed(2);
  document.getElementById("utilprecioD").value = utilprecioD.toFixed(2);
  document.getElementById("utilprecioE").value = utilprecioE.toFixed(2);
}

// Llamar la función cada vez que los precios cambian
document.getElementById("precio").addEventListener("input", calcularUtilidades);
document
  .getElementById("precioB")
  .addEventListener("input", calcularUtilidades);
document
  .getElementById("precioC")
  .addEventListener("input", calcularUtilidades);
document
  .getElementById("precioD")
  .addEventListener("input", calcularUtilidades);
document
  .getElementById("precioE")
  .addEventListener("input", calcularUtilidades);
document
  .getElementById("precioCompra")
  .addEventListener("input", calcularUtilidades);

$(document).on("click", ".editable-price", function () {
  let $this = $(this);
  let currentValue = $this.text();
  $this.attr("contenteditable", true).focus();

  $this.data("original", currentValue);

  // Selecciona todo el texto al hacer clic
  document.execCommand("selectAll", false, null);
});

$(document).on("blur keypress", ".editable-price", function (e) {
  let $this = $(this);

  // Enter key or blur
  if (e.type === "blur" || e.which === 13) {
    e.preventDefault();

    let newValue = $this.text().trim();
    let original = $this.data("original");

    if (newValue === original) {
      $this.attr("contenteditable", false);
      return;
    }

    let id = $this.data("id");
    let field = $this.data("field");

    $.ajax({
      url: "controladores/producto.php?op=actualizarPrecio",
      method: "POST",
      data: { idproducto: id, campo: field, valor: newValue },
      success: function (response) {
        $this.attr("contenteditable", false);
        // Actualiza el valor por si viene con formato del servidor
        $this.text(newValue);
      },
      error: function (err) {
        alert("Error al actualizar precio");
        $this.text(original).attr("contenteditable", false);
      },
    });
  }
});

// Asignar evento una sola vez
$("#btnGenerarCatalogo").on("click", function () {
  const idcategoria = $("#categoriaCatalogo").val();

  // Obtener todos los precios seleccionados
  const preciosSeleccionados = [];
  $('#contenedor-precios input[type="checkbox"]:checked').each(function () {
    preciosSeleccionados.push($(this).val());
  });

  if (preciosSeleccionados.length === 0) {
    Swal.fire({
      icon: "warning",
      title: "Seleccione al menos un tipo de precio",
      confirmButtonText: "Aceptar",
    });
    return;
  }

  const precios = preciosSeleccionados.join(",");
  const url =
    "reportes/rptproductos.php?idcategoria=" +
    idcategoria +
    "&precios=" +
    precios;

  window.open(url, "_blank");
  $("#modalCatalogoConfig").modal("hide");
});

// Al abrir el modal, cargar las imágenes
$("#modalCatalogoConfig").on("shown.bs.modal", function () {
  $.post("controladores/producto.php?op=obtenerimagenes", function (data) {
    let imagenes = JSON.parse(data);
    let html = "";

    if (imagenes.length === 0) {
      html = crearBloqueImagen();
    } else {
      contadorImagenes = 0;
      imagenes.forEach(function (img) {
        contadorImagenes++;
        html += `
          <div class="form-group border rounded p-3 mb-3 position-relative bloque-imagen">
            <input type="file" class="form-control d-none" name="imagenes[]" id="imagen_${contadorImagenes}" accept="image/*">
            <img src="files/${img.nombre_imagen}" class="img-thumbnail imagen-hover" id="imagenmuestra_${contadorImagenes}" width="400px" style="cursor: pointer;" onclick="document.getElementById('imagen_${contadorImagenes}').click();">
            <button type="button" class="btn btn-danger btnEliminarImagen"><i class="fas fa-trash"></i></button>
          </div>
        `;
      });
    }

    $("#contenedor-imagenes").html(html);
  });
});

let contadorImagenes = 0;

function crearBloqueImagen() {
  contadorImagenes++;
  return `
    <div class="form-group border rounded p-3 mb-3 position-relative bloque-imagen">
      <input type="file" class="form-control d-none" name="imagenes[]" id="imagen_${contadorImagenes}" accept="image/*">
      <img src="files/productos/anonymous.png" class="img-thumbnail imagen-hover" id="imagenmuestra_${contadorImagenes}" width="400px" style="cursor: pointer;" onclick="document.getElementById('imagen_${contadorImagenes}').click();">
      <button type="button" class="btn btn-success btnGuardarImagen"><i class="fas fa-save"></i></button>
      <button type="button" class="btn btn-danger  btnEliminarImagen"><i class="fas fa-trash"></i></button>
    </div>
  `;
}

$(document).ready(function () {
  // Al abrir el modal, se inicializa con una imagen
  $("#modalCatalogoConfig").on("shown.bs.modal", function () {
    $("#contenedor-imagenes").html(crearBloqueImagen());
  });

  // Botón para agregar otra imagen
  $("#btnAgregarImagen").click(function () {
    $("#contenedor-imagenes").append(crearBloqueImagen());
  });

  // Eliminar bloque de imagen
  $("#contenedor-imagenes").on("click", ".btnEliminarImagen", function () {
    $(this).closest(".bloque-imagen").remove();
  });

  // Vista previa cuando se selecciona imagen
  $("#contenedor-imagenes").on("change", 'input[type="file"]', function () {
    const input = this;
    const reader = new FileReader();
    const imgId = "imagenmuestra_" + input.id.split("_")[1];

    reader.onload = function (e) {
      document.getElementById(imgId).src = e.target.result;
    };
    if (input.files[0]) reader.readAsDataURL(input.files[0]);
  });
});

$("#contenedor-imagenes").on("click", ".btnGuardarImagen", function () {
  const bloque = $(this).closest(".bloque-imagen");
  const inputFile = bloque.find('input[type="file"]')[0];

  if (!inputFile.files[0]) {
    Swal.fire({
      icon: "warning",
      title: "Imagen no seleccionada",
      text: "Por favor, seleccione una imagen antes de guardar.",
      confirmButtonColor: "#3085d6",
      confirmButtonText: "Aceptar",
    });
    return;
  }

  const formData = new FormData();
  formData.append("imagen", inputFile.files[0]);

  $.ajax({
    url: "controladores/producto.php?op=guardarimagenindividual",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (response) {
      const res = JSON.parse(response);
      if (res.status === "ok") {
        Swal.fire({
          icon: "success",
          title: "Éxito",
          text: "Imagen guardada correctamente.",
          confirmButtonColor: "#28a745",
          confirmButtonText: "Aceptar",
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Error al guardar la imagen.",
          confirmButtonColor: "#dc3545",
          confirmButtonText: "Aceptar",
        });
      }
    },
  });
});

$("#contenedor-imagenes").on("click", ".btnEliminarImagen", function () {
  const bloque = $(this).closest(".bloque-imagen");
  const imgElement = bloque.find("img");
  const src = imgElement.attr("src");

  // Extraer nombre de archivo desde "files/nombre.jpg"
  const nombreImagen = src.split("/").pop();

  // Si la imagen es una precargada (ej. anonymous.png), no la elimines del servidor
  if (nombreImagen === "anonymous.png" || !src.includes("files/")) {
    bloque.remove();
    return;
  }

  // Confirmar antes de eliminar con Swal
  Swal.fire({
    title: "¿Estás seguro?",
    text: "Esta acción eliminará la imagen permanentemente.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      // Ejecutar eliminación vía AJAX
      $.post(
        "controladores/producto.php?op=eliminarimagenindividual",
        { nombre_imagen: nombreImagen },
        function (response) {
          const res = JSON.parse(response);
          if (res.status === "ok") {
            bloque.remove();
          } else {
            Swal.fire({
              icon: "error",
              title: "Error al eliminar",
              text: "Error al eliminar la imagen: " + res.msg,
              confirmButtonColor: "#dc3545",
              confirmButtonText: "Aceptar",
            });
          }
        },
      );
    }
  });
});

function cargarCategoriasCatalogo() {
  $.post("controladores/producto.php?op=obtenercategorias", function (data) {
    let categorias = JSON.parse(data);
    let html = '<option value="0">Todas las categorías</option>';
    categorias.forEach((cat) => {
      html += `<option value="${cat.idcategoria}">${cat.nombre}</option>`;
    });
    $("#categoriaCatalogo").html(html);
  });
}

$(document).ready(function () {
  function cargarPreciosCatalogo() {
    $.post("controladores/producto.php?op=obtenerprecios", function (data) {
      const precios = JSON.parse(data);
      let html = `
        <label class="precio-chip active">
          <input type="checkbox" value="precio_venta" checked>
          Precio Público
        </label>
      `;

      precios.forEach((p) => {
        html += `
          <label class="precio-chip">
            <input type="checkbox" value="${p.idnombre_p}">
            ${p.descripcion}
          </label>
        `;
      });

      $("#contenedor-precios").html(html);
    });
  }

  // Al abrir el modal, carga precios y categorías
  $("#modalCatalogoConfig").on("shown.bs.modal", function () {
    cargarCategoriasCatalogo();
    cargarPreciosCatalogo();
  });

  // Evento para el botón Generar
  $("#btnGenerarCatalogo").on("click", function () {
    const idcategoria = $("#categoriaCatalogo").val();
    const preciosSeleccionados = [];

    $('#contenedor-precios input[type="checkbox"]:checked').each(function () {
      preciosSeleccionados.push($(this).val());
    });

    if (preciosSeleccionados.length === 0) {
      Swal.fire({
        icon: "warning",
        title: "Seleccione al menos un tipo de precio",
        confirmButtonText: "Aceptar",
      });
      return;
    }

    const precios = preciosSeleccionados.join(",");
    const url =
      "reportes/rptproductos.php?idcategoria=" +
      idcategoria +
      "&precios=" +
      precios;
    window.open(url, "_blank");
    $("#modalCatalogoConfig").modal("hide");
  });

  // Delegar el evento de toggle visual
  $("#contenedor-precios").on("click", ".precio-chip", function () {
    const checkbox = $(this).find('input[type="checkbox"]');
    checkbox.prop("checked", !checkbox.prop("checked"));
    $(this).toggleClass("active", checkbox.prop("checked"));
  });
});

function eliminarProducto(idproducto) {
  Swal.fire({
    title: "Eliminar?",
    text: "¿Está seguro que desea eliminar el producto?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si",
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      $.post(
        "controladores/producto.php?op=eliminar",
        { idproducto: idproducto },
        function (response) {
          const data = JSON.parse(response);
          if (!data.success) {
            Swal.fire({ title: "Producto", icon: "error", text: data.message });
            return;
          }
          Swal.fire({ title: "Producto", icon: "success", text: data.message });
          listarProductos.load();
        },
      );
    }
  });
}

// Abrir modal vacío
function abrirModalStockSucursales() {
  $("#buscarProducto").val("");
  $("#tablaStockSucursales tbody").html(
    '<tr><td colspan="6" class="text-center">Ingrese un producto para ver el stock.</td></tr>',
  );
  $("#modalStockSucursales").modal("show");
}

// Buscar producto al escribir (con delay)
let buscarTimeout;

$("#buscarProducto").on("keyup", function () {

  clearTimeout(buscarTimeout);

  let termino = $(this).val().trim();
  let idsucursalFiltro = $("#sucursalFiltro").val();

  if (termino.length < 2) {
    $("#tablaStockSucursales tbody").html(`
            <tr>
                <td colspan="6" class="text-center">
                    Ingrese al menos 2 caracteres para buscar.
                </td>
            </tr>
        `);
    return;
  }
  buscarTimeout = setTimeout(function () {
    $.post(
      "controladores/producto.php?op=buscarStockPorSucursales",
      {
        search: termino,
        idsucursalFiltro: idsucursalFiltro
      },
      function (response) {
        const data = JSON.parse(response);

        if (!data.success) {
          Swal.fire({ title: "Producto", icon: "error", text: data.message });
          return;
        };

        let tbody = "";

        if (data.data.length === 0) {

          tbody = `
                        <tr>
                            <td colspan="6" class="text-center">
                                No se encontraron resultados.
                            </td>
                        </tr>
                    `;

        } else {

          data.data.forEach((row) => {

            tbody += `
                            <tr>

                                <td>
                                    <input
                                        type="checkbox"
                                        class="chkProductoStock"
                                        data-idserie="${row.idserie}"
                                        data-idproducto="${row.idproducto}"
                                        data-nombre="${row.nombre}"
                                        data-codigo="${row.codigo}"
                                        data-serie="${row.numero_serie}"
                                        data-motor="${row.numero_motor}"
                                        data-placa="${row.placa}"
                                        data-color="${row.color}"
                                        data-idsucursal="${row.idsucursal}"
                                        data-sucursal="${row.sucursal}">
                                </td>

                                <td>${row.nombre}</td>
                                <td>${row.codigo}</td>
                                <td>${row.numero_serie ?? "-"}</td>
                                <td>${row.placa ?? "-"}</td>
                                <td>${row.sucursal}</td>

                            </tr>
                        `;
          });

        }

        $("#tablaStockSucursales tbody").html(tbody);

      }
    );

  }, 400);

});

// 🔹 Cuando el usuario haga clic en "Generar Solicitud"
$("#btnGenerarSolicitudDesdeStock").on("click", function () {

  let productosSeleccionados = [];

  $("#tablaStockSucursales tbody tr").each(function () {
    const chk = $(this).find(".chkProductoStock");
    const inputCantidad = $(this).find(".cantidadSolicitud");

    if (chk.is(":checked")) {
      productosSeleccionados.push({
        idproducto: chk.data("idproducto"),
        idserie: chk.data("idserie"),
        nombre: chk.data("nombre"),
        codigo: chk.data("codigo"),
        stock: chk.data("stock"),
        cantidad: parseFloat(inputCantidad.val()) || 1,
        idsucursal_origen: chk.data("idsucursal_origen"),
        sucursal_origen: chk.data("sucursal"),
      });
    }
  });

  if (productosSeleccionados.length === 0) {
    Swal.fire("Atención", "Debe seleccionar al menos un producto.", "warning");
    return;
  }

  const iddestino_solicitud = $("#sucursalFiltro").val();
  if (!iddestino_solicitud) {
    Swal.fire("Atención", "Debe seleccionar la sucursal destino.", "warning");
    return;
  }

  $("#modalStockSucursales").modal("hide");

  $.ajax({
    url: "controladores/traslado.php?op=guardarSolicitud",
    type: "POST",
    data: {
      productos: JSON.stringify(productosSeleccionados),
      iddestino_solicitud: iddestino_solicitud,
    },
    success: function (resp) {

      // Normalizamos la respuesta
      const r = resp.trim().toLowerCase();

      if (r === "ok" || r.includes("solicitud enviada")) {
        Swal.fire({
          icon: "success",
          title: "Solicitud generada correctamente",
          text: "La solicitud fue enviada al almacén de origen.",
          timer: 2500,
          showConfirmButton: false,
        });
      } else {
        Swal.fire("Error", "No se pudo registrar la solicitud.", "error");
      }
    },
    error: function (xhr) {
      Swal.fire("Error", "Ocurrió un error en el servidor.", "error");
    },
  });
});

function abrirModalStockSucursales() {
  $("#buscarProducto").val("");
  $("#tablaStockSucursales tbody").html(
    '<tr><td colspan="6" class="text-center">Ingrese un producto para ver el stock.</td></tr>',
  );
  $("#modalStockSucursales").modal("show");

  // 🔹 Llenar sucursales excepto la actual
  $.post("controladores/traslado.php?op=almacenesDestino", {}, function (data) {
    // data viene como <option>...</option>
    $("#sucursalFiltro").html(data);

    // 🔸 Seleccionar automáticamente la primera sucursal diferente a la actual
    let $primera = $("#sucursalFiltro option:first");
    if ($primera.length) {
      $primera.prop("selected", true);
      console.log("🏬 Sucursal por defecto:", $primera.text());
    } else {
      console.warn("⚠️ No se encontraron sucursales disponibles.");
    }
  });
}

function limpiarSolicitud() {
  $("#tablaDetalleSolicitud tbody").html("");
  $("#iddestino_solicitud").val("");
}

// ======================= MODO CÓDIGO: Manual / Automático =======================
// Alternar entre modo Manual y Automático
$("#modoCodigo").on("change", function () {
  const codigoActual = $("#codigo").val()?.trim() || "";

  // Detectar si es un código automático (formato: 4 dígitos año + 3 dígitos número)
  const esCodigoAutomatico = /^[0-9]{4}[0-9]{3}$/.test(codigoActual);

  if ($(this).is(":checked")) {
    // ===== MODO AUTOMÁTICO =====
    $("#codigo").prop("readonly", true);
    $("label[for='modoCodigo']").html(
      '<i class="fa fa-robot text-primary"></i> Automático',
    );

    // Si ya tiene un código automático, NO generes otro
    if (esCodigoAutomatico) {
      console.log(
        "El código actual ya es automático. No se generará uno nuevo.",
      );
      generarbarcode();
      return;
    }

    // Generar un nuevo código automático
    $.post("controladores/producto.php?op=generar_codigo", function (data) {
      try {
        let json = JSON.parse(data);
        if (json.codigo) {
          $("#codigo").val(json.codigo);
          generarbarcode();
        } else {
          console.error("Respuesta inesperada:", data);
        }
      } catch (error) {
        console.error("Error al parsear JSON:", data);
      }
    });
  } else {
    // ===== MODO MANUAL =====
    if (esCodigoAutomatico) {
      // El código actual es automático → no generamos otro, solo habilitamos edición
      Swal.fire({
        title: "Código automático detectado",
        text: "Este producto tiene un código automático. ¿Deseas editarlo manualmente?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, editar",
        cancelButtonText: "No, mantener igual",
      }).then((result) => {
        if (result.isConfirmed) {
          $("#codigo").prop("readonly", false);
          $("label[for='modoCodigo']").html(
            '<i class="fa fa-edit text-success"></i> Manual (editable)',
          );
        } else {
          // Vuelve al estado automático sin tocar el código
          $("#modoCodigo").prop("checked", true);
          $("#codigo").prop("readonly", true);
          $("label[for='modoCodigo']").html(
            '<i class="fa fa-robot text-primary"></i> Automático',
          );
        }
      });
    } else {
      // Si el código no es automático (manual o vacío), se puede escribir normalmente
      $("#codigo").prop("readonly", false);
      $("label[for='modoCodigo']").html(
        '<i class="fa fa-edit text-success"></i> Manual',
      );
      generarbarcode();
    }
  }
});

// Por defecto: modo manual
$("#modoCodigo").prop("checked", false).trigger("change");

// Solución al warning aria-hidden + foco retenido
$(document).on("hide.bs.modal", function () {
  setTimeout(() => document.activeElement.blur(), 10);
});
init();
