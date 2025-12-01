// ALMACENAR INSTANCIAS DE GRÁFICAS
let chartType = null;
let chartDay = null;
let chartHour = null;

// INICIALIZAR Y CARGAR GRÁFICAS
function initCharts() {
  loadChartType();
  loadChartDay();
  loadChartHour();
}

// GRÁFICA: DESCARGAS POR TIPO DE ARCHIVO (PIE)
function loadChartType() {
  $.ajax({
    url: 'backend/bitacora/get-download-stats.php',
    method: 'POST',
    dataType: 'json',
    success: function(response) {
      if (response.status === 'success' && response.data) {
        const labels = response.data.map(item => item.tipo_archivo.toUpperCase());
        const data = response.data.map(item => item.cantidad);
        const colors = generateColors(labels.length);

        const ctx = document.getElementById('chart-type');
        if (chartType) chartType.destroy();
        
        chartType = new Chart(ctx, {
          type: 'doughnut',
          data: {
            labels: labels,
            datasets: [{
              data: data,
              backgroundColor: colors,
              borderColor: '#375a7f',
              borderWidth: 2
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                position: 'bottom',
                labels: {
                  color: '#adb5bd',
                  font: { size: 12 }
                }
              }
            }
          }
        });
      }
    },
    error: function() {
      console.log('Error cargando estadísticas por tipo');
    }
  });
}

// GRÁFICA: DESCARGAS POR DÍA DE LA SEMANA (BAR)
function loadChartDay() {
  $.ajax({
    url: 'backend/bitacora/get-downloads-by-day.php',
    method: 'POST',
    dataType: 'json',
    success: function(response) {
      if (response.status === 'success' && response.data) {
        const labels = response.data.map(item => item.dia_es);
        const data = response.data.map(item => item.cantidad);

        const ctx = document.getElementById('chart-day');
        if (chartDay) chartDay.destroy();
        
        chartDay = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: labels,
            datasets: [{
              label: 'Descargas',
              data: data,
              backgroundColor: '#375a7f',
              borderColor: '#5a8cc8',
              borderWidth: 2
            }]
          },
          options: {
            indexAxis: 'x',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                labels: {
                  color: '#adb5bd'
                }
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                ticks: {
                  color: '#adb5bd'
                },
                grid: {
                  color: '#495057'
                }
              },
              x: {
                ticks: {
                  color: '#adb5bd'
                },
                grid: {
                  color: '#495057'
                }
              }
            }
          }
        });
      }
    },
    error: function() {
      console.log('Error cargando estadísticas por día');
    }
  });
}

// GRÁFICA: DESCARGAS POR HORA DEL DÍA (LINE)
function loadChartHour() {
  $.ajax({
    url: 'backend/bitacora/get-downloads-by-hour.php',
    method: 'POST',
    dataType: 'json',
    success: function(response) {
      if (response.status === 'success' && response.data) {
        const labels = response.data.map(item => item.hora);
        const data = response.data.map(item => item.cantidad);

        const ctx = document.getElementById('chart-hour');
        if (chartHour) chartHour.destroy();
        
        chartHour = new Chart(ctx, {
          type: 'line',
          data: {
            labels: labels,
            datasets: [{
              label: 'Descargas por Hora',
              data: data,
              borderColor: '#5a8cc8',
              backgroundColor: 'rgba(90, 140, 200, 0.1)',
              borderWidth: 2,
              tension: 0.4,
              fill: true,
              pointBackgroundColor: '#375a7f',
              pointBorderColor: '#5a8cc8',
              pointRadius: 4,
              pointHoverRadius: 6
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                labels: {
                  color: '#adb5bd'
                }
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                ticks: {
                  color: '#adb5bd'
                },
                grid: {
                  color: '#495057'
                }
              },
              x: {
                ticks: {
                  color: '#adb5bd'
                },
                grid: {
                  color: '#495057'
                }
              }
            }
          }
        });
      }
    },
    error: function() {
      console.log('Error cargando estadísticas por hora');
    }
  });
}

// GENERAR COLORES ALEATORIOS PARA GRÁFICAS
function generateColors(count) {
  const colors = [
    '#375a7f', '#5a8cc8', '#4db8ff', '#99d6ff',
    '#66b3ff', '#3d94ff', '#1a75ff', '#0052cc',
    '#0039b3', '#002080', '#003d99', '#0047b3'
  ];
  
  const result = [];
  for (let i = 0; i < count; i++) {
    result.push(colors[i % colors.length]);
  }
  return result;
}
