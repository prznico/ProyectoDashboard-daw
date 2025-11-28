function init() {
    // Inicializar valores por defecto en el formulario
    $('#marca').val('');
    $('#modelo').val('');
    $('#precio').val('');
    $('#detalles').val('');
    $('#unidades').val('');
    $('#imagen').val('img/default.png');
}

$(document).ready(function() {
    let edit = false;
    fetchProducts();
    console.log("jQuery is ready");

    // Inicializar el formulario
    init();

    //Funcion buscar con Jquery
    $('#search').keyup(function (e) {
        if ($('#search').val()) {
            let search = $('#search').val();
            console.log(search);
            $.ajax({
                url: 'backend/product-search.php',
                type: 'POST',
                data: {search},
                success: function(response) {
                    try {
                        let products = JSON.parse(response);
                        let template = "";
                        products.forEach(product => {
                            console.log(product);
                            template += `<li>${product.nombre}</li>`;
                        });
                        if (products.length > 0) {
                            $("#product-result").removeClass("d-none");
                        }
                        $('#container').html(template);

                        //Se listan los productos que coinciden con la busqueda con todos sus datos
                        $("#products").html("");
                        products.forEach(product => {
                            $("#products").append(`
                                <tr productId="${product.id}">
                                    <td>${product.id}</td>
                                    <td>
                                        <a href="#" class="product-item">${product.nombre}</a>
                                    </td>
                                    <td>${product.marca}</td>
                                    <td>${product.modelo}</td>
                                    <td>${product.precio}</td>
                                    <td>${product.detalles}</td>
                                    <td>${product.unidades}</td>
                                    <td>
                                        <img src="${product.imagen}" class="img-fluid" alt="Imagen del producto" style="max-width: 100px;">
                                    </td>
                                    <td>
                                        <button class="product-delete btn btn-danger">Eliminar</button>
                                    </td>
                                </tr>
                            `);
                        });
                    } catch (error) {
                        console.error("Error parsing search response:", error);
                    }
                }
            });
        }
    });

    //Enviar Productos con jquery
    $("#product-form").submit(function (e) {
        e.preventDefault();

        // Obtener valores usando los IDs CORRECTOS del HTML
        let id = $('#productId').val();
        let nombre = $('#name').val();
        let marca = $('#marca').val(); // CORREGIDO: era $('#brand').val()
        let modelo = $('#modelo').val(); // CORREGIDO: era $('#model').val()
        let precio = $('#precio').val(); // CORREGIDO: era $('#price').val()
        let detalles = $('#detalles').val(); // CORREGIDO: era $('#details').val()
        let unidades = $('#unidades').val(); // CORREGIDO: era $('#units').val()
        let imagen = $('#imagen').val(); // CORREGIDO: era $('#image').val()

        const data = {
            id: id,
            nombre: nombre,
            marca: marca,
            modelo: modelo,
            precio: precio,
            detalles: detalles,
            unidades: unidades,
            imagen: imagen,
        };

        // Convertir a string para poder enviarlo
        let dataJsonString = JSON.stringify(data);        
        
        // Validaciones de datos:
        // Validar nombre (requerido y 100 caracteres o menos)
        if (data.nombre.trim() === "" || data.nombre.length > 100) {
            alert("El nombre es obligatorio y debe tener 100 caracteres o menos.");
            return false;
        }

        // Validar marca (requerida y seleccionada)
        if (data.marca.trim() === "") {
            alert("Debes seleccionar una marca.");
            return false;
        }

        // Validar modelo (requerido, alfanumérico y 25 caracteres o menos)
        const modeloRegex = /^[a-zA-Z0-9]+$/;
        if (data.modelo.trim() === "" || !modeloRegex.test(data.modelo) || data.modelo.length > 25) {
            alert("El modelo es obligatorio, debe ser alfanumérico y tener 25 caracteres o menos.");
            return false;
        }

        // Validar precio (requerido y mayor a 99.99)
        if (isNaN(data.precio) || parseFloat(data.precio) <= 99.99) {
            alert("El precio es obligatorio y debe ser mayor a 99.99.");
            return false;
        }

        // Validar unidades (requerido y mayor o igual a 0)
        if (isNaN(data.unidades) || parseInt(data.unidades) < 0) {
            alert("Las unidades son obligatorias y deben ser un número mayor o igual a 0.");
            return false;
        }

        // Validar detalles (opcional, pero si se usa, máximo 250 caracteres)
        if (data.detalles.length > 250) {
            alert("Los detalles deben tener 250 caracteres o menos.");
            return false;
        }

        console.log("Datos a enviar:", data);
        console.log("JSON string:", dataJsonString);
        
        let url = edit === false ? "backend/product-add.php" : "backend/product-edit.php";
        
        $.post(url, dataJsonString, function (response) {
            console.log("Respuesta del servidor:", response);
            
            try {
                let message = JSON.parse(response);
                let template = `<p>${message.message}</p>`;
                
                // Limpiar formulario
                $("#product-form").trigger("reset");
                init();
                
                // Mostrar mensaje
                alert(message.message);
                
                // Actualizar lista
                fetchProducts();

                if (message.message.length > 0) {
                    $("#product-result").removeClass("d-none");
                }
        
                $("#container").html(template);
                
                // Resetear modo edición si fue exitoso
                if (edit && message.status === "success") {
                    edit = false;
                    $('#submit-btn').text('Agregar Producto');
                }
                
            } catch (error) {
                console.error("Error parsing response:", error);
                alert("Error procesando respuesta del servidor");
            }
        }).fail(function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
            alert("Error de conexión con el servidor");
        });
    });

    // Validar cada campo del formulario y mostrar el estado
    // Validar que el nombre no exista en la base de datos
    $("#name").keyup(function() {
        let name = $("#name").val();
        console.log("Validando nombre:", name);
        
        // Validación básica primero
        if (name.trim() === "" || name.length > 100) {
            $("#name").addClass("is-invalid");
            $("#name").removeClass("is-valid");
            showStatus($("#name-status"), "Nombre inválido", "error");
            return;
        }
        
        // Verificar en la base de datos
        $.ajax({
            url: 'backend/search-name.php',
            type: 'POST',
            data: {name: name},
            success: function(response) {
                try {
                    let products = JSON.parse(response);
                    console.log("Productos encontrados:", products);
                    
                    if (products.length > 0) {
                        $("#name").addClass("is-invalid");
                        $("#name").removeClass("is-valid");
                        showStatus($("#name-status"), "Ya existe un producto con este nombre", "error");
                        
                        // Mostrar mensaje en el contenedor
                        let template = `<li>Producto existente: ${products[0].nombre} <br>Elige un nuevo nombre por favor</li>`;
                        $("#product-result").removeClass("d-none");
                        $('#container').html(template);
                    } else {
                        $("#name").addClass("is-valid");
                        $("#name").removeClass("is-invalid");
                        showStatus($("#name-status"), "Nombre disponible", "success");
                        $("#product-result").addClass("d-none");
                    }
                } catch (error) {
                    console.error("Error parsing name validation response:", error);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error validating name:", error);
            }
        });
    });

    // Validar marca
    $("#marca").change(function() { // Cambiado a 'change' para select
        let marca = $("#marca").val();
        if (marca.trim() === "") {
            $("#marca").addClass("is-invalid");
            $("#marca").removeClass("is-valid");
            showStatus($("#marca-status"), "Selecciona una marca", "error");
        } else {
            $("#marca").addClass("is-valid");
            $("#marca").removeClass("is-invalid");
            showStatus($("#marca-status"), "Marca válida", "success");
        }
    });

    // Validar modelo
    $("#modelo").keyup(function() {
        let modelo = $("#modelo").val();
        const modeloRegex = /^[a-zA-Z0-9]+$/;
        if (modelo.trim() === "" || !modeloRegex.test(modelo) || modelo.length > 25) {
            $("#modelo").addClass("is-invalid");
            $("#modelo").removeClass("is-valid");
            showStatus($("#modelo-status"), "Modelo inválido", "error");
        } else {
            $("#modelo").addClass("is-valid");
            $("#modelo").removeClass("is-invalid");
            showStatus($("#modelo-status"), "Modelo válido", "success");
        }
    });

    // Validar precio
    $("#precio").keyup(function() {
        let precio = $("#precio").val();
        if (isNaN(precio) || parseFloat(precio) <= 99.99) {
            $("#precio").addClass("is-invalid");
            $("#precio").removeClass("is-valid");
            showStatus($("#precio-status"), "Precio debe ser mayor a 99.99", "error");
        } else {
            $("#precio").addClass("is-valid");
            $("#precio").removeClass("is-invalid");
            showStatus($("#precio-status"), "Precio válido", "success");
        }
    });

    // Validar unidades
    $("#unidades").keyup(function() {
        let unidades = $("#unidades").val();
        if (isNaN(unidades) || parseInt(unidades) < 0) {
            $("#unidades").addClass("is-invalid");
            $("#unidades").removeClass("is-valid");
            showStatus($("#unidades-status"), "Unidades deben ser ≥ 0", "error");
        } else {
            $("#unidades").addClass("is-valid");
            $("#unidades").removeClass("is-invalid");
            showStatus($("#unidades-status"), "Unidades válidas", "success");
        }
    });

    // Validar detalles
    $("#detalles").keyup(function() {
        let detalles = $("#detalles").val();
        if (detalles.length > 250) {
            $("#detalles").addClass("is-invalid");
            $("#detalles").removeClass("is-valid");
            showStatus($("#detalles-status"), "Máximo 250 caracteres", "error");
        } else {
            $("#detalles").addClass("is-valid");
            $("#detalles").removeClass("is-invalid");
            showStatus($("#detalles-status"), "Detalles válidos", "success");
        }
    });

    // Validar imagen
    $("#imagen").keyup(function() {
        let imagen = $("#imagen").val();
        if (imagen.trim() === "") {
            $("#imagen").addClass("is-invalid");
            $("#imagen").removeClass("is-valid");
            showStatus($("#imagen-status"), "URL requerida", "error");
        } else {
            $("#imagen").addClass("is-valid");
            $("#imagen").removeClass("is-invalid");
            showStatus($("#imagen-status"), "URL válida", "success");
        }
    });

    // Función auxiliar para mostrar mensajes de estado
    function showStatus(element, message, type) {
        element.text(message);
        element.removeClass("status-success status-error");
        element.addClass(type === "success" ? "status-success" : "status-error");
        element.show();
    }

    // Mostrar productos con jquery
    function fetchProducts() {
        $.ajax({
            url: 'backend/product-list.php',
            type: 'GET',
            success: function (response) {
                try {
                    let products = JSON.parse(response);
                    let template = "";
                    products.forEach(product => {
                        template += `
                        <tr productId="${product.id}">
                            <td>${product.id}</td>
                            <td>
                                <a href="#" class="product-item">${product.nombre}</a>
                            </td>
                            <td>${product.marca}</td>
                            <td>${product.modelo}</td>
                            <td>${product.precio}</td>
                            <td>${product.detalles}</td>
                            <td>${product.unidades}</td>
                            <td>
                                <img src="${product.imagen}" class="img-fluid" alt="Imagen del producto" style="max-width: 100px;">
                            </td>
                            <td>
                                <button class="product-delete btn btn-danger">Eliminar</button>
                            </td>
                        </tr>
                        `;
                    });
                    $('#products').html(template);
                } catch (error) {
                    console.error("Error parsing product list:", error);
                }
            }
        });
    }

    //Enviar Productos con jquery
    $("#product-form").submit(function (e) {
        e.preventDefault();

        // Obtener valores usando los IDs CORRECTOS del HTML
        let id = $('#productId').val();
        let nombre = $('#name').val();
        let marca = $('#marca').val();
        let modelo = $('#modelo').val();
        let precio = $('#precio').val();
        let detalles = $('#detalles').val();
        let unidades = $('#unidades').val();
        let imagen = $('#imagen').val();

        const data = {
            id: id,
            nombre: nombre,
            marca: marca,
            modelo: modelo,
            precio: precio,
            detalles: detalles,
            unidades: unidades,
            imagen: imagen,
        };

        // Convertir a string para poder enviarlo
        let dataJsonString = JSON.stringify(data);        
        
        console.log("Datos a enviar:", data);
        console.log("JSON string:", dataJsonString);
        
        let url = edit === false ? "backend/product-add.php" : "backend/product-edit.php";
        
        console.log("Enviando a:", url);
        
        $.post(url, dataJsonString, function (response, textStatus, xhr) {
            console.log("Respuesta completa:", response);
            console.log("Status:", textStatus);
            console.log("Tipo de respuesta:", typeof response);
            
            // Verificar si la respuesta está vacía o es undefined
            if (response === undefined || response === null || response === "") {
                console.error("Respuesta vacía o undefined del servidor");
                alert("Error: El servidor no respondió correctamente. Verifica la consola para más detalles.");
                return;
            }
            
            try {
                let message = JSON.parse(response);
                console.log("Mensaje parseado:", message);
                
                let template = `<p>${message.message}</p>`;
                
                // Limpiar formulario
                $("#product-form").trigger("reset");
                init();
                
                // Mostrar mensaje
                if (message.status === "success") {
                    alert("Éxito: " + message.message);
                } else {
                    alert("Error: " + message.message);
                }
                
                // Actualizar lista
                fetchProducts();

                if (message.message && message.message.length > 0) {
                    $("#product-result").removeClass("d-none");
                }
        
                $("#container").html(template);
                
                // Resetear modo edición si fue exitoso
                if (edit && message.status === "success") {
                    edit = false;
                    $('#submit-btn').text('Agregar Producto');
                    $('#productId').val('');
                }
                
            } catch (error) {
                console.error("Error parseando JSON:", error);
                console.error("Respuesta cruda recibida:", response);
                console.error("Tipo de respuesta:", typeof response);
                alert("Error: El servidor respondió con datos inválidos. Verifica la consola.");
            }
        }).fail(function(xhr, status, error) {
            console.error("Error AJAX:", status, error);
            console.error("Respuesta del servidor:", xhr.responseText);
            alert("Error de conexión: " + error + "\nVerifica la consola para más detalles.");
        });
    });

    // Eliminar productos con jquery
    $(document).on("click", ".product-delete", function () {
        if (confirm("¿Estás seguro de querer eliminar este producto?")) {
            let element = $(this)[0].parentElement.parentElement;
            let id = $(element).attr("productId");
            $.post("backend/product-delete.php", {id}, function (response) {
                try {
                    let result = JSON.parse(response);
                    alert(result.message);
                    fetchProducts();
                } catch (error) {
                    console.error("Error parsing delete response:", error);
                    alert("Producto eliminado");
                    fetchProducts();
                }
            });
        }
    });

    // Editar productos con jquery
    $(document).on("click", ".product-item", function () {
        let element = $(this)[0].parentElement.parentElement;
        let id = $(element).attr("productId");
        $.post("backend/product-single.php", {id}, function (response) {
            try {
                const product = JSON.parse(response);
                console.log("Producto a editar:", product);
                
                // Llenar el formulario con los datos del producto
                $('#productId').val(product.id);
                $('#name').val(product.nombre);
                $('#marca').val(product.marca);
                $('#modelo').val(product.modelo);
                $('#precio').val(product.precio);
                $('#detalles').val(product.detalles);
                $('#unidades').val(product.unidades);
                $('#imagen').val(product.imagen);
                
                // Validar todos los campos
                $("#name").trigger('keyup');
                $("#marca").trigger('change');
                $("#modelo").trigger('keyup');
                $("#precio").trigger('keyup');
                $("#detalles").trigger('keyup');
                $("#unidades").trigger('keyup');
                $("#imagen").trigger('keyup');
                
                edit = true;
                $('#submit-btn').text('Actualizar Producto');
                
            } catch (error) {
                console.error("Error parsing product data:", error);
            }
        });
    });
});