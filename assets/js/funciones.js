document.addEventListener("DOMContentLoaded", function () {
    $('#grad').click(function () { 
        {
            $.ajax({
                url: "resultado.php", 
                type: "POST",
                data: $("#graduaciones").serialize(), 
                success: function (resultado) {
                    $("#okgrad").html(resultado);  

                }
            });
        }
    })



    if ($('#tbl').length && !$('#tbl').hasClass('custom-dt-init')) {
        $('#tbl').DataTable();
    }
    $(".confirmar").submit(function (e) {
        e.preventDefault();
        Swal.fire({
            title: 'Esta seguro de eliminar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'SI, Eliminar!'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        })
    })
    $("#nom_cliente").autocomplete({
        minLength: 3,
        source: function (request, response) {
            $.ajax({
                url: "ajax.php",
                dataType: "json",
                data: {
                    q: request.term
                },
                success: function (data) {
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            $("#idcliente").val(ui.item.id);
            $("#nom_cliente").val(ui.item.label);
            $("#tel_cliente").val(ui.item.telefono);
            $("#dir_cliente").val(ui.item.direccion);
            $("#obrasocial").val(ui.item.obrasocial);
        }
    })
    $("#producto").autocomplete({
        minLength: 3,
        source: function (request, response) {
            $.ajax({
                url: "ajax.php",
                dataType: "json",
                data: {
                    pro: request.term
                },
                success: function (data) {
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            console.log('Autocomplete select:', ui.item);
            console.log('ID del producto seleccionado:', ui.item.id);
            $("#producto").val(ui.item.label);
            // Agregar producto después de 100ms
            setTimeout(function () {
                console.log('Intentando agregar producto con ID:', ui.item.id);
                registrarDetalleManual(ui.item.id, 1, ui.item.precio);
            }, 100);
            return false;
        } 
    })
    
    // Manejar Enter en el campo de búsqueda - método alternativo
    $("#producto").on('keydown', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            var query = $(this).val();
            if (query.length >= 3) {
                // Buscar el primer resultado del autocomplete
                $.ajax({
                    url: "ajax.php",
                    dataType: "json",
                    data: {
                        pro: query
                    },
                    success: function (data) {
                        console.log('Resultados de búsqueda:', data);
                        if (data && data.length > 0) {
                            // Usar el primer resultado
                            var producto = data[0];
                            console.log('Producto seleccionado:', producto);
                            console.log('ID del producto:', producto.id);
                            $("#producto").val(producto.label);
                            registrarDetalleManual(producto.id, 1, producto.precio);
                        } else {
                            console.log('No se encontraron productos');
                            Swal.fire({
                                position: 'top-end',
                                icon: 'warning',
                                title: 'Producto no encontrado',
                                text: 'No se encontró ningún producto con ese nombre',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    },
                    error: function(error) {
                        console.log('Error en búsqueda:', error);
                        Swal.fire({
                            position: 'top-end',
                            icon: 'error',
                            title: 'Error al buscar',
                            text: 'No se pudo conectar con el servidor',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                });
            } else {
                Swal.fire({
                    position: 'top-end',
                    icon: 'info',
                    title: 'Búsqueda muy corta',
                    text: 'Ingrese al menos 3 caracteres',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        }
    })
    
    $('#btn_generar').click(function (e) {
        e.preventDefault();
        var rows = $('#tblDetalle tr').length;
        if (rows > 2) {
            var abona = $('#abona').val();
            var action = 'procesarVenta';
            var id = $('#idcliente').val();            
            var resto = $('#resto').val();
            var descuento = $('#porc').val();
            var metodo_pago = $('input[name=pago]:checked').val();

            var obrasocial = $('#obra_social').val();
            if (abona == "" || abona == null) {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: 'El campo abona no puede estar vacio',
                    showConfirmButton: false,
                    timer: 2000
                });
                return;
            }
            $.ajax({
                url: 'ajax.php',
                async: true,
                data: {
                    procesarVenta: action,
                    id: id,
                    abona : abona,
                    resto : resto,
                    descuento : descuento,
                    obrasocial : obrasocial,
                    metodo_pago : metodo_pago 
                
                },
                success: function (response) {
                    console.log("Respuesta recibida:", response);
                    console.log("Tipo de respuesta:", typeof response);
                    
                    try {
                        // Manejar respuesta que puede venir como string JSON o como objeto
                        let res;
                        if (typeof response === 'string') {
                            // Si es string, limpiar y parsear
                            const responseTrimmed = response.trim();
                            // Verificar si es JSON válido
                            if (responseTrimmed.startsWith('{') || responseTrimmed.startsWith('[')) {
                                res = JSON.parse(responseTrimmed);
                            } else {
                                // Si no es JSON, podría ser un mensaje de error
                                console.error("Respuesta no es JSON válido:", responseTrimmed);
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'error',
                                    title: 'Error en la respuesta del servidor',
                                    text: 'La respuesta no es válida: ' + responseTrimmed.substring(0, 100),
                                    showConfirmButton: false,
                                    timer: 4000
                                });
                                return;
                            }
                        } else if (typeof response === 'object' && response !== null) {
                            // Si ya es un objeto, usarlo directamente
                            res = response;
                        } else {
                            console.error("Tipo de respuesta no esperado:", typeof response, response);
                            Swal.fire({
                                position: 'top-end',
                                icon: 'error',
                                title: 'Error en la respuesta',
                                text: 'Tipo de respuesta no válido',
                                showConfirmButton: false,
                                timer: 3000
                            });
                            return;
                        }
                        
                        console.log("Respuesta parseada:", res);
                        
                        // Verificar si hay error
                        if (res.mensaje && res.mensaje === 'error') {
                            Swal.fire({
                                position: 'top-end',
                                icon: 'error',
                                title: 'Error al generar la venta',
                                text: res.detalle || 'Error desconocido',
                                showConfirmButton: false,
                                timer: 3000
                            })
                        } else if (res.id_venta || res.id) {
                            const idVenta = res.id_venta || res.id;
                            const idCliente = res.id_cliente || res.id_cliente;
                            
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: 'Venta Generada',
                                text: 'Venta #' + idVenta + ' generada exitosamente',
                                showConfirmButton: false,
                                timer: 2000
                            })
                            setTimeout(() => {
                                if (idCliente && idVenta) {
                                    generarPDF(idCliente, idVenta);
                                }
                                location.reload();
                            }, 300);
                        } else {
                            console.error("Respuesta inesperada:", res);
                            Swal.fire({
                                position: 'top-end',
                                icon: 'error',
                                title: 'Error inesperado',
                                text: 'No se pudo procesar la respuesta. Respuesta: ' + JSON.stringify(res).substring(0, 100),
                                showConfirmButton: false,
                                timer: 4000
                            })
                        }
                    } catch (e) {
                        console.error("Error al parsear JSON:", e);
                        console.error("Respuesta original:", response);
                        Swal.fire({
                            position: 'top-end',
                            icon: 'error',
                            title: 'Error al procesar la respuesta',
                            text: 'Error: ' + e.message + '. Respuesta: ' + response.substring(0, 200),
                            showConfirmButton: false,
                            timer: 5000
                        })
                    }
                
                },
               
                error: function (xhr, status, error) {
                    console.error("Error en AJAX:", status, error);
                    console.error("Respuesta del servidor:", xhr.responseText);
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor. Error: ' + error,
                        showConfirmButton: false,
                        timer: 3000
                    })
                }
            });
        } else {
            Swal.fire({
                position: 'top-end',
                icon: 'warning',
                title: 'No hay producto para generar la venta',
                showConfirmButton: false,
                timer: 2000
            })
        }
    });
    if (document.getElementById("detalle_venta")) {
        listar();
    }
    
    // Event listeners para calcular automáticamente el total
    document.getElementById('abona')?.addEventListener('input', calcularVenta);
    document.getElementById('porc')?.addEventListener('change', calcularVenta);
    document.getElementById('obra_social')?.addEventListener('input', calcularVenta);

    document.querySelector("#borrar_grad").addEventListener("click", function () {
        var ids = ['ojoD1', 'ojoD2', 'ojoD3', 'ojoI1', 'ojoI2', 'ojoI3', 'ojoDl1', 'ojoDl2', 'ojoDl3', 'ojoIl1', 'ojoIl2', 'ojoIl3', 'add', 'obs'];
        ids.forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.value = '';
        });
        var hid = document.getElementById('id_graduacion_edit');
        if (hid) hid.value = '';
        var gbtn = document.getElementById('grad');
        if (gbtn) gbtn.innerHTML = '<i class="fas fa-plus mr-2"></i> Agregar Graduaciones';
    });

    function gradCellToInput(v) {
        if (v === null || v === undefined) return '';
        if (v === 0 || v === '0') return '';
        return String(v);
    }

    $(document).on('click', '.btn-editar-graduacion', function () {
        var raw = $(this).attr('data-grad');
        if (!raw) return;
        try {
            var g = JSON.parse(raw);
        } catch (e) {
            return;
        }
        $('#id_graduacion_edit').val(g.id || '');
        $('#ojoDl1').val(gradCellToInput(g.od_l_1));
        $('#ojoDl2').val(gradCellToInput(g.od_l_2));
        $('#ojoDl3').val(gradCellToInput(g.od_l_3));
        $('#ojoIl1').val(gradCellToInput(g.oi_l_1));
        $('#ojoIl2').val(gradCellToInput(g.oi_l_2));
        $('#ojoIl3').val(gradCellToInput(g.oi_l_3));
        $('#ojoD1').val(gradCellToInput(g.od_c_1));
        $('#ojoD2').val(gradCellToInput(g.od_c_2));
        $('#ojoD3').val(gradCellToInput(g.od_c_3));
        $('#ojoI1').val(gradCellToInput(g.oi_c_1));
        $('#ojoI2').val(gradCellToInput(g.oi_c_2));
        $('#ojoI3').val(gradCellToInput(g.oi_c_3));
        $('#add').val(gradCellToInput(g.addg));
        $('#obs').val(g.obs != null ? String(g.obs) : '');
        var gbtn = document.getElementById('grad');
        if (gbtn) gbtn.innerHTML = '<i class="fas fa-save mr-2"></i> Guardar cambios';
        Swal.fire({ position: 'top-end', icon: 'info', title: 'Editando graduación', showConfirmButton: false, timer: 1500 });
    });

    $(document).on('click', '.btn-eliminar-graduacion', function () {
        var id = $(this).data('id');
        if (!id) return;
        Swal.fire({
            title: '¿Eliminar esta graduación?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: 'borrar_grad.php',
                type: 'POST',
                data: { id: id },
                success: function (resultado) {
                    $('#okgrad').html(resultado);
                }
            });
        });
    });
    
    
    
    

})

// Función para guardar nuevo cliente
function guardarNuevoCliente() {
    console.log('guardarNuevoCliente llamada');
    var nombre = $('#nombre_cliente').val();
    var telefono = $('#telefono_cliente').val();
    var direccion = $('#direccion_cliente').val();
    
    console.log('Datos:', {nombre, telefono, direccion});
    
    if (!nombre || !telefono || !direccion) {
        Swal.fire({
            position: 'top-end',
            icon: 'warning',
            title: 'Complete los campos obligatorios',
            text: 'Nombre, Teléfono y Dirección son obligatorios',
            showConfirmButton: false,
            timer: 3000
        });
        return;
    }
    
    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        dataType: 'json',
        data: {
            nuevo_cliente: true,
            nombre_cliente: nombre,
            telefono_cliente: telefono,
            direccion_cliente: direccion,
            dni_cliente: $('#dni_cliente').val(),
            obrasocial_cliente: $('#obrasocial_cliente').val(),
            medico_cliente: $('#medico_cliente').val()
        },
        success: function(response) {
            console.log('Respuesta del servidor:', response);
            if (response.success) {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: response.mensaje,
                    showConfirmButton: false,
                    timer: 2000
                });
                
                // Cerrar el modal
                $('#nuevo_cliente_venta').modal('hide');
                
                // Llenar los campos con los datos del nuevo cliente
                $('#idcliente').val(response.cliente.id);
                $('#nom_cliente').val(response.cliente.label);
                $('#tel_cliente').val(response.cliente.telefono);
                $('#dir_cliente').val(response.cliente.direccion);
                $('#obrasocial').val(response.cliente.obrasocial);
                
                // Limpiar el formulario del modal
                $('#form_nuevo_cliente')[0].reset();
            } else {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: 'Error',
                    text: response.mensaje,
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        },
        error: function(xhr, status, error) {
            console.log('Error AJAX:', {xhr, status, error});
            Swal.fire({
                position: 'top-end',
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo guardar el cliente: ' + error,
                showConfirmButton: false,
                timer: 3000
            });
        }
    });
}

// Simular venta - Calcular totales con descuentos
function calcularVenta() {
    var abona = parseFloat(document.getElementById('abona').value) || 0;
    var descuento = parseFloat(document.getElementById('porc').value);
    var obrasocial = parseFloat(document.getElementById('obra_social').value) || 0;

    var total_productos = 0;
    var filas = document.querySelectorAll("#tblDetalle tbody tr");
    filas.forEach(function (e) {
        var columnas = e.querySelectorAll("td");
        if (columnas.length < 5) return;
        var importe = parseFloat(columnas[4].textContent.replace('$', '').replace(',', ''));
        if (!isNaN(importe) && importe > 0) {
            total_productos += importe;
        }
    });

    if (abona < 0) {
        document.getElementById('abona').value = 0;
        abona = 0;
    }
    if (obrasocial < 0) {
        document.getElementById('obra_social').value = 0;
        obrasocial = 0;
    }

    if (obrasocial > total_productos) {
        Swal.fire({ position: 'top-end', icon: 'warning', title: 'La Obra Social no puede superar el total de productos', showConfirmButton: false, timer: 2500 });
        return;
    }

    // Total final = (productos - obra social) × factor descuento
    var total_final = (total_productos - obrasocial) * descuento;
    // Resto = lo que queda por pagar (si abona de más, queda en 0)
    var resto = Math.max(0, total_final - abona);

    // Actualizar tfoot de la tabla
    var tfootRows = document.querySelectorAll("#tblDetalle tfoot tr");
    if (tfootRows.length > 0) {
        var cells = tfootRows[0].querySelectorAll("td");
        if (cells.length > 1) {
            cells[1].innerHTML = '<strong>$' + total_final.toFixed(2) + '</strong>';
        }
    }

    document.getElementById('resto').value = resto.toFixed(2);

    var totalDisplay = document.getElementById('total-amount');
    if (totalDisplay) {
        totalDisplay.textContent = '$' + total_final.toFixed(2);
    }
}

document.querySelector("#guardar_cristal")?.addEventListener("click", function () {
    {
        $.ajax({
            url: "colocar_cristal.php",
            type: "POST",
            data: $("#form_cristal").serialize(),
            success: function (resultado) {
                $("#div_cristal").html(resultado);
            }
        });
    }
})

document.querySelector("#buscar_venta")?.addEventListener("click", function () {
    {
        $.ajax({
            url: "postpagos.php",
            type: "POST",
            data: $("#form_venta").serialize(),
            success: function (resultado) {
                $("#div_venta").html(resultado);

            }
        });
    }
})

document.querySelector("#anular_venta")?.addEventListener("click", function () {
    var idventa = $('#idanular').val();
    if(idventa == ""){
         Swal.fire({
            position: 'top-mid',
            icon: 'error',
            title: 'Complete Id Venta',
            showConfirmButton: false,
            timer: 2000
        });
        return;
    }

    Swal.fire({
        position: 'top-mid',
        icon: 'success',
        title: '',
        text: '¿Desea Eliminar la Venta?',
        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, Eliminar!'    
    }).then((result) => {
        if (result.isConfirmed) {
            // La respuesta del usuario es "Aceptar"
            {
                $.ajax({
                    url: "anular.php",
                    type: "POST",
                    data: $("#form_anular").serialize(),
                    success: function (resultado) {
                        $("#div_anular").html(resultado);
                    }
                });
            }
        } else if (result.isDenied || result.isDismissed) {
            // La respuesta del usuario es "Cancelar"
            Swal.fire('No se ha eliminado la venta', '', 'info')
        }
    });
});
    

function listar() {
    let html = '';
    let detalle = 'detalle';
    $.ajax({
        url: "ajax.php",
        dataType: "json",
        data: {
            detalle: detalle
        },
        success: function (response) {
            if (response.length === 0) {
                html = `<tr>
                    <td colspan="6" class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted">No hay productos en el carrito</p>
                    </td>
                </tr>`;
            } else {
                response.forEach(row => {
                    // Mostrar badge de costo si es un producto de costo
                    let costoBadge = '';
                    if (row['costo'] == 1) {
                        costoBadge = '<span class="badge badge-modern" style="background: #17a2b8; color: white; margin-left: 5px;"><i class="fas fa-tag"></i></span>';
                    }
                    
                    html += `<tr>
                    <td><span class="badge badge-modern" style="background: #667eea; color: white;">${row['codigo'] || row['id']}</span></td>
                    <td><strong>${row['descripcion']}</strong>${costoBadge}</td>
                    <td><span class="badge badge-modern" style="background: #28a745; color: white;">${row['cantidad']}</span></td>
                    <td>
                        <div style="display:flex; align-items:center; gap:4px;">
                            <span style="color:#555; font-weight:600;">$</span>
                            <input type="number"
                                   class="precio-editable-input"
                                   value="${parseInt(row['precio_venta'])}"
                                   min="0" step="any"
                                   data-id="${row['id']}"
                                   data-original="${parseInt(row['precio_venta'])}"
                                   style="width:110px; border:1.5px dashed #b0bec5; border-radius:6px; padding:4px 8px; font-size:0.9rem; background:#fffde7; outline:none;"
                                   title="Podés editar el precio para esta venta sin modificar el catálogo"
                                   onchange="actualizarPrecio(${row['id']}, this.value, this)">
                            <i class="fas fa-pen" style="color:#b0bec5; font-size:0.7rem;" title="Precio editable"></i>
                        </div>
                    </td>
                    <td><strong class="subtotal-item">$${parseFloat(row['sub_total']).toFixed(2)}</strong></td>
                    <td>
                        <button class="btn btn-modern btn-modern-danger btn-sm" type="button" onclick="deleteDetalle(${row['id']})">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>`;
                });
            }
            document.querySelector("#detalle_venta").innerHTML = html;
            calcular();

        }
    });
}


function registrarDetalle(e, id, cant, precio) {
    if (document.getElementById('producto').value != '') {
        if (e.which == 13) {
            if (id != null) {
                registrarDetalleManual(id, cant, precio);
            }
        }
    }
}

// Nueva función para agregar producto manualmente
function registrarDetalleManual(id, cant, precio) {
    console.log('registrarDetalleManual llamado con:', {id, cant, precio});
    if (id != null && id > 0) {
        $.ajax({
            url: "ajax.php",
            type: 'POST',
            dataType: "json",
            data: {
                id: id,
                cant: cant,
                action: 'agregar',
                precio: precio
            },
            success: function (response) {
                console.log('Respuesta recibida:', response, typeof response);
                
                // Convertir a string para comparación
                let resp = String(response).replace(/"/g, '');
                console.log('Respuesta como string:', resp);
                
                if (resp === 'registrado') {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Producto Ingresado',
                        showConfirmButton: false,
                        timer: 2000
                    })
                    document.querySelector("#producto").value = '';
                    document.querySelector("#producto").focus();
                    listar();
                } else if (resp === 'actualizado') {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Producto Actualizado',
                        showConfirmButton: false,
                        timer: 2000
                    })
                    document.querySelector("#producto").value = '';
                    document.querySelector("#producto").focus();
                    listar();
                } else if (resp === 'stock_insuficiente') {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'warning',
                        title: 'Stock Insuficiente',
                        text: 'No hay suficiente stock para este producto',
                        showConfirmButton: false,
                        timer: 3000
                    })
                    document.querySelector("#producto").value = '';
                    document.querySelector("#producto").focus();
                } else if (resp === 'producto_no_existe') {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: 'Producto No Encontrado',
                        text: 'El producto seleccionado no existe o está inactivo',
                        showConfirmButton: false,
                        timer: 3000
                    })
                    document.querySelector("#producto").value = '';
                    document.querySelector("#producto").focus();
                } else {
                    console.log('Respuesta no reconocida:', resp);
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: 'Error al ingresar el producto',
                        text: 'Respuesta: ' + resp,
                        showConfirmButton: false,
                        timer: 3000
                    })
                }
            },
            error: function() {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo agregar el producto',
                    showConfirmButton: false,
                    timer: 2000
                })
            }
        });
    }
}
function actualizarPrecio(id, nuevoPrecio, inputEl) {
    var precio = parseFloat(nuevoPrecio);

    if (isNaN(precio) || precio < 0) {
        inputEl.value = inputEl.dataset.original;
        Swal.fire({
            position: 'top-end',
            icon: 'warning',
            title: 'Precio inválido',
            showConfirmButton: false,
            timer: 2000
        });
        return;
    }

    $.ajax({
        url: "ajax.php",
        type: "POST",
        data: {
            update_precio: true,
            id: id,
            precio: precio
        },
        success: function(response) {
            if (response === 'ok') {
                inputEl.dataset.original = precio.toFixed(2);
                var fila = inputEl.closest('tr');
                var cantBadge = fila.querySelector('td:nth-child(3) .badge');
                var cantidad = cantBadge ? parseInt(cantBadge.textContent) : 1;
                var nuevoSubtotal = precio * cantidad;
                var subtotalEl = fila.querySelector('.subtotal-item');
                if (subtotalEl) {
                    subtotalEl.textContent = '$' + nuevoSubtotal.toFixed(2);
                }
                calcular();
                calcularVenta();
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Precio actualizado',
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                inputEl.value = inputEl.dataset.original;
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: 'Error al actualizar el precio',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        },
        error: function() {
            inputEl.value = inputEl.dataset.original;
            Swal.fire({
                position: 'top-end',
                icon: 'error',
                title: 'Error de conexión',
                showConfirmButton: false,
                timer: 2000
            });
        }
    });
}

function deleteDetalle(id) {
    let detalle = 'Eliminar'
    $.ajax({
        url: "ajax.php",
        data: {
            id: id,
            delete_detalle: detalle
        },
        success: function (response) {
            console.log(response);
            if (response == 'restado') {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Producto Descontado',
                    showConfirmButton: false,
                    timer: 2000
                })
                document.querySelector("#producto").value = '';
                document.querySelector("#producto").focus();
                listar();
            } else if (response == 'ok') {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Producto Eliminado',
                    showConfirmButton: false,
                    timer: 2000
                })
                document.querySelector("#producto").value = '';
                document.querySelector("#producto").focus();
                listar();
            } else {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: 'Error al eliminar el producto',
                    showConfirmButton: false,
                    timer: 2000
                })
            }
        }
    });
}


function calcular() {
    var total = 0;
    // obtenemos todas las filas del tbody
    var filas = document.querySelectorAll("#tblDetalle tbody tr");

    // recorremos cada una de las filas
    filas.forEach(function (e) {
        // obtenemos las columnas de cada fila
        var columnas = e.querySelectorAll("td");

        // Nos saltamos la fila vacía
        if (columnas.length < 5) return;

        // obtenemos los valores del importe total (columna 4 que es index 4)
        var importe = parseFloat(columnas[4].textContent.replace('$', '').replace(',', ''));
        
        if (!isNaN(importe) && importe > 0) {
            total += importe;
        }
    });

    console.log('Total calculado:', total);

    // mostramos la suma total en el tfoot
    var tfootRows = document.querySelectorAll("#tblDetalle tfoot tr");
    if (tfootRows.length > 0) {
        var cells = tfootRows[0].querySelectorAll("td");
        // cells[0] = colspan 4 (texto "Total a Pagar:")
        // cells[1] = colspan 2 (donde va el total)
        if (cells.length > 1) {
            cells[1].innerHTML = '<strong>$' + total.toFixed(2) + '</strong>';
        }
    }

    // Actualizar el total display grande
    var totalDisplay = document.getElementById('total-amount');
    if (totalDisplay) {
        totalDisplay.textContent = '$' + total.toFixed(2);
        
        // Animación si hay productos
        if (total > 0) {
            totalDisplay.parentElement.classList.add('success-pulse');
            setTimeout(() => {
                totalDisplay.parentElement.classList.remove('success-pulse');
            }, 2000);
        }
    }
}


function generarPDF(cliente, id_venta) {
    url = 'pdf/generar.php?cl=' + cliente + '&v=' + id_venta;
    window.open(url, '_blank');
}

function btnCambiar(e) {
    e.preventDefault();
    const actual = document.getElementById('actual').value;
    const nueva = document.getElementById('nueva').value;
    if (actual == "" || nueva == "") {
        Swal.fire({
            position: 'top-end',
            icon: 'error',
            title: 'Los campos estan vacios',
            showConfirmButton: false,
            timer: 2000
        })
    } else {
        const cambio = 'pass';
        $.ajax({
            url: "ajax.php",
            type: 'POST',
            data: {
                actual: actual,
                nueva: nueva,
                cambio: cambio
            },
            success: function (response) {
                console.log(response);
                if (response == 'ok') {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Contraseña modificado',
                        showConfirmButton: false,
                        timer: 2000
                    })
                    document.querySelector('frmPass').reset();
                    $("#nuevo_pass").modal("hide");
                } else if (response == 'dif') {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: 'La contraseña actual incorrecta',
                        showConfirmButton: false,
                        timer: 2000
                    })
                } else {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: 'Error al modificar la contraseña',
                        showConfirmButton: false,
                        timer: 2000
                    })
                }
            }
        });
    }
}

// Event listener para botón simular venta (una sola vez)
$(document).ready(function() {
    var btnParcial = document.getElementById('btn_parcial');
    if (btnParcial && !btnParcial.hasAttribute('data-listener-attached')) {
        btnParcial.addEventListener('click', calcularVenta);
        btnParcial.setAttribute('data-listener-attached', 'true');
    }
});

