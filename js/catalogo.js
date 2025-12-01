// VARIABLES GLOBALES
let allResources = [];
let filteredResources = [];
let currentPage = 1;
const itemsPerPage = 6;

// INICIALIZACIÓN
function init() {
  checkUserStatus();
  loadResources();
  setupEventListeners();
}

// VERIFICAR ESTADO DE USUARIO
function checkUserStatus() {
  $.ajax({
    url: 'backend/usuarios/validate-session.php',
    method: 'POST',
    success: function(response) {
      if (response.status === 'success') {
        $('#user-info').html('Bienvenido: <strong>' + response.usuario.nombre + '</strong> | <a href="dashboard.html">Dashboard</a> | <a href="#" onclick="logout()">Logout</a>');
      }
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
      filteredResources = allResources;
      $('#total-count').text(allResources.length);
      renderResources();
    },
    error: function() {
      $('#resources-grid').html('<div class="col-12 text-danger text-center py-5">Error cargando recursos</div>');
    }
  });
}

// RENDERIZAR GRID DE RECURSOS
function renderResources() {
  const start = (currentPage - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  const pageItems = filteredResources.slice(start, end);

  let html = '';
  if (pageItems.length === 0) {
    html = '<div class="col-12 text-center text-muted py-5">No hay recursos disponibles</div>';
  } else {
    pageItems.forEach(function(resource) {
      const fileIcon = getFileIcon(resource.tipo_archivo);
      const fecha = new Date(resource.fecha_creacion).toLocaleDateString('es-ES');
      
      html += '<div class="col-md-4">' +
        '<div class="card resource-card">' +
          '<div class="card-body">' +
            '<div class="file-icon">' + fileIcon + '</div>' +
            '<h5 class="card-title text-truncate">' + (resource.nombre || '-') + '</h5>' +
            '<p class="card-text text-muted small">' + (resource.descripcion ? resource.descripcion.substring(0, 100) + '...' : 'Sin descripción') + '</p>' +
            '<div class="resource-meta mb-2">' +
              '<p class="mb-1"><strong>Autor:</strong> ' + (resource.autor || '-') + '</p>' +
              '<p class="mb-1"><strong>Tipo:</strong> ' + (resource.tipo_archivo || '-').toUpperCase() + '</p>' +
              '<p class="mb-1"><strong>Tamaño:</strong> ' + (resource.tamaño_mb || 0) + ' MB</p>' +
              '<p class="mb-1"><strong>Creado:</strong> ' + fecha + '</p>' +
            '</div>' +
            '<div class="btn-group-vertical">' +
              '<button class="btn btn-sm btn-info" onclick="viewDetails(' + resource.id + ')">Ver Detalles</button>' +
              '<button class="btn btn-sm btn-success" onclick="downloadResource(' + resource.id + ')">Descargar</button>' +
            '</div>' +
          '</div>' +
        '</div>' +
      '</div>';
    });
  }

  $('#resources-grid').html(html);
  renderPagination();
}

// OBTENER ICONO POR TIPO DE ARCHIVO
function getFileIcon(tipo) {
  const icons = {
    'pdf': '📄',
    'doc': '📝',
    'docx': '📝',
    'xls': '📊',
    'xlsx': '📊',
    'ppt': '🎯',
    'pptx': '🎯',
    'zip': '📦',
    'rar': '📦',
    'txt': '📋',
    'jpg': '🖼️',
    'png': '🖼️',
    'gif': '🖼️'
  };
  return icons[tipo?.toLowerCase()] || '📎';
}

// VER DETALLES EN MODAL
function viewDetails(id) {
  const resource = allResources.find(r => r.id == id);
  if (!resource) return;

  const fecha = new Date(resource.fecha_creacion).toLocaleDateString('es-ES');

  $('#modal-nombre').text(resource.nombre || '-');
  $('#modal-autor').text(resource.autor || '-');
  $('#modal-departamento').text(resource.departamento || '-');
  $('#modal-empresa').text(resource.empresa_institucion || '-');
  $('#modal-fecha').text(fecha);
  $('#modal-tipo').text((resource.tipo_archivo || '-').toUpperCase());
  $('#modal-tamanio').text(resource.tamaño_mb || 0);
  $('#modal-descripcion').text(resource.descripcion || 'Sin descripción');
  $('#modal-download-btn').attr('onclick', 'downloadResource(' + id + '); $("#modal-resource").modal("hide");');

  $('#modal-resource').modal('show');
}

// DESCARGAR RECURSO
function downloadResource(id) {
  const resource = allResources.find(r => r.id == id);
  if (!resource) {
    alert('Recurso no encontrado');
    return;
  }

  // Registrar descarga
  $.ajax({
    url: 'backend/bitacora/record_download.php',
    method: 'POST',
    data: { recurso_id: id },
    success: function() {
      console.log('Descarga registrada');
    }
  });

  // Descargar archivo
  window.location.href = 'backend/recursos/resource-download.php?id=' + id;
}

// CONFIGURAR LISTENERS
function setupEventListeners() {
  $('#btn-search').on('click', function() {
    search();
  });

  $('#search-input').on('keypress', function(e) {
    if (e.which === 13) {
      search();
      return false;
    }
  });

  $('#btn-reset').on('click', function() {
    resetSearch();
  });
}

// BÚSQUEDA
function search() {
  const query = $('#search-input').val().toLowerCase().trim();
  
  if (!query) {
    filteredResources = allResources;
  } else {
    filteredResources = allResources.filter(function(resource) {
      const nombre = (resource.nombre || '').toLowerCase();
      const autor = (resource.autor || '').toLowerCase();
      const descripcion = (resource.descripcion || '').toLowerCase();
      
      return nombre.includes(query) || autor.includes(query) || descripcion.includes(query);
    });
  }

  currentPage = 1;
  renderResources();
}

// RESETEAR BÚSQUEDA
function resetSearch() {
  $('#search-input').val('');
  filteredResources = allResources;
  currentPage = 1;
  renderResources();
}

// RENDERIZAR PAGINACIÓN
function renderPagination() {
  const totalPages = Math.ceil(filteredResources.length / itemsPerPage);
  let html = '';

  if (totalPages <= 1) {
    $('#pagination').html('');
    return;
  }

  // Botón anterior
  if (currentPage > 1) {
    html += '<li class="page-item"><a class="page-link" href="#" onclick="previousPage(); return false;">Anterior</a></li>';
  }

  // Números de página
  for (let i = 1; i <= totalPages; i++) {
    const active = i === currentPage ? 'active' : '';
    html += '<li class="page-item ' + active + '"><a class="page-link" href="#" onclick="goToPage(' + i + '); return false;">' + i + '</a></li>';
  }

  // Botón siguiente
  if (currentPage < totalPages) {
    html += '<li class="page-item"><a class="page-link" href="#" onclick="nextPage(); return false;">Siguiente</a></li>';
  }

  $('#pagination').html(html);
}

// NAVEGAR A PÁGINA
function goToPage(page) {
  currentPage = page;
  renderResources();
  $('html, body').animate({ scrollTop: 0 }, 'fast');
}

// PÁGINA SIGUIENTE
function nextPage() {
  const totalPages = Math.ceil(filteredResources.length / itemsPerPage);
  if (currentPage < totalPages) {
    currentPage++;
    renderResources();
  }
}

// PÁGINA ANTERIOR
function previousPage() {
  if (currentPage > 1) {
    currentPage--;
    renderResources();
  }
}

// LOGOUT
function logout() {
  $.ajax({
    url: 'backend/usuarios/logout.php',
    method: 'POST',
    success: function() {
      window.location.href = 'catalogo.html';
    }
  });
}
