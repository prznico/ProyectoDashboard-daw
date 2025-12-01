// VARIABLES GLOBALES
let currentResourceId = null;
let allResources = [];

// INICIALIZACIÓN
function init() {
  checkSession();
  loadResources();
  setupEventListeners();
  loadCharts();
}

// VERIFICAR SESIÓN
function checkSession() {
  $.ajax({
    url: 'backend/usuarios/validate-session.php',
    method: 'POST',
    success: function(response) {
      if (response.status === 'success' && response.usuario.rol === 'admin') {
        $('#user-info').html('Usuario: <strong>' + response.usuario.nombre + '</strong>');
      } else {
        alert('Acceso denegado. Solo administradores pueden acceder.');
        window.location.href = 'login.html';
      }
    },
    error: function() {
      alert('Sesión expirada. Redirigiendo...');
      window.location.href = 'login.html';
    }
  });
}

// CARGAR RECURSOS
function loadResources() {
  $.ajax({
    url: 'backend/recursos/resource-list.php',
    method: 'POST',
    dataType: 'json',
    success: function(data) {
      allResources = Array.isArray(data) ? data : [];
      renderResourceTable();
    },
    error: function() {
      $('#resources-tbody').html('<tr><td colspan="5" class="text-danger">Error cargando recursos</td></tr>');
    }
  });
}

// RENDERIZAR TABLA DE RECURSOS
function renderResourceTable() {
  let html = '';
  if (allResources.length === 0) {
    html = '<tr><td colspan="5" class="text-center text-muted">No hay recursos</td></tr>';
  } else {
    allResources.forEach(function(resource) {
      html += '<tr>' +
        '<td>' + resource.id + '</td>' +
        '<td>' + (resource.nombre || '-') + '</td>' +
        '<td>' + (resource.autor || '-') + '</td>' +
        '<td><span class="badge badge-info">' + (resource.tipo_archivo || 'N/A') + '</span></td>' +
        '<td>' +
          '<button class="btn btn-sm btn-warning mr-1" onclick="editResource(' + resource.id + ')">✎</button>' +
          '<button class="btn btn-sm btn-danger" onclick="deleteResource(' + resource.id + ')">✕</button>' +
        '</td>' +
      '</tr>';
    });
  }
  $('#resources-tbody').html(html);
}

// CONFIGURAR LISTENERS
function setupEventListeners() {
  $('#resource-form').on('submit', function(e) {
    e.preventDefault();
    submitResource();
  });

  $('#btn-logout').on('click', function(e) {
    e.preventDefault();
    logout();
  });

  $('#btn-cancel').on('click', function() {
    resetForm();
  });
}

// ENVIAR FORMULARIO (AGREGAR O EDITAR)
function submitResource() {
  const resourceId = $('#resourceId').val();
  const isEdit = resourceId && resourceId !== '';

  if (isEdit) {
    editSubmit();
  } else {
    addSubmit();
  }
}

// AGREGAR RECURSO
function addSubmit() {
  const formData = new FormData($('#resource-form')[0]);
  
  $.ajax({
    url: 'backend/recursos/resource-add.php',
    method: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function(response) {
      if (response.status === 'success') {
        alert('Recurso agregado exitosamente');
        resetForm();
        loadResources();
        loadCharts();
      } else {
        alert('Error: ' + (response.message || 'No se pudo agregar el recurso'));
      }
    },
    error: function(xhr) {
      alert('Error en la solicitud');
      console.log(xhr.responseText);
    }
  });
}

// EDITAR RECURSO
function editResource(id) {
  const resource = allResources.find(r => r.id == id);
  if (!resource) {
    alert('Recurso no encontrado');
    return;
  }

  $('#nombre').val(resource.nombre || '');
  $('#autor').val(resource.autor || '');
  $('#departamento').val(resource.departamento || '');
  $('#empresa').val(resource.empresa_institucion || '');
  $('#fecha_creacion').val(resource.fecha_creacion || '');
  $('#descripcion').val(resource.descripcion || '');
  $('#resourceId').val(id);
  $('#submit-btn').text('Actualizar Recurso');
  $('#btn-cancel').removeClass('d-none');
  
  // Scroll al formulario
  $('html, body').animate({ scrollTop: 0 }, 'fast');
}

// ENVIAR EDICIÓN
function editSubmit() {
  const resourceId = $('#resourceId').val();
  const formData = new FormData($('#resource-form')[0]);
  formData.append('id', resourceId);

  $.ajax({
    url: 'backend/recursos/resource-edit.php',
    method: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function(response) {
      if (response.status === 'success') {
        alert('Recurso actualizado exitosamente');
        resetForm();
        loadResources();
        loadCharts();
      } else {
        alert('Error: ' + (response.message || 'No se pudo actualizar'));
      }
    },
    error: function() {
      alert('Error en la solicitud');
    }
  });
}

// ELIMINAR RECURSO
function deleteResource(id) {
  if (!confirm('¿Deseas eliminar este recurso? Esta acción no se puede deshacer.')) {
    return;
  }

  $.ajax({
    url: 'backend/recursos/resource-delete.php',
    method: 'POST',
    data: { id: id },
    dataType: 'json',
    success: function(response) {
      if (response.status === 'success') {
        alert('Recurso eliminado');
        loadResources();
        loadCharts();
      } else {
        alert('Error: ' + (response.message || 'No se pudo eliminar'));
      }
    },
    error: function() {
      alert('Error en la solicitud');
    }
  });
}

// RESETEAR FORMULARIO
function resetForm() {
  $('#resource-form')[0].reset();
  $('#resourceId').val('');
  $('#submit-btn').text('Agregar Recurso');
  $('#btn-cancel').addClass('d-none');
  currentResourceId = null;
}

// CERRAR SESIÓN
function logout() {
  $.ajax({
    url: 'backend/usuarios/logout.php',
    method: 'POST',
    success: function() {
      alert('Sesión cerrada');
      window.location.href = 'login.html';
    },
    error: function() {
      window.location.href = 'login.html';
    }
  });
}

// CARGAR GRÁFICAS (Llamado desde charts.js)
function loadCharts() {
  if (typeof initCharts === 'function') {
    initCharts();
  }
}
