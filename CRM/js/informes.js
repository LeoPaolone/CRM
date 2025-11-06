document.addEventListener('DOMContentLoaded', function() {
    // --- Funcionalidad para la página de Informes con Gráfico ---
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const salesChartCanvas = document.getElementById('salesChart'); // Asumiendo que el canvas tiene este ID

    // Solo ejecutar este código si estamos en la página de informes (donde existen estos elementos)
    if (startDateInput && endDateInput && salesChartCanvas) {
        let salesChart;

        // Función para obtener datos de ventas y actualizar el gráfico
        async function fetchSalesData() {
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;

            // Validar que las fechas no estén vacías
            if (!startDate || !endDate) {
                // No mostrar error en consola, simplemente no hacer nada si no hay fechas.
                return;
            }

            try {
                // Usamos la API real para obtener los datos
                const response = await fetch(`../api/getSalesData.php?start_date=${startDate}&end_date=${endDate}`);
                if (!response.ok) {
                    throw new Error('La respuesta de la red no fue correcta.');
                }
                const data = await response.json();
                updateChart(data);
            } catch (error) {
                console.error('Error al obtener los datos de ventas:', error);
            }
        }

        // Función para actualizar el gráfico con nuevos datos
        function updateChart(data) {
            const labels = data.map(item => item.fecha_venta);
            const amounts = data.map(item => item.monto);

            if (salesChart) {
                salesChart.destroy(); // Destruir el gráfico anterior para evitar solapamientos
            }

            salesChart = new Chart(salesChartCanvas, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Ventas',
                        data: amounts,
                        borderColor: '#137fec',
                        backgroundColor: 'rgba(19, 127, 236, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        // Event listeners para los campos de fecha
        startDateInput.addEventListener('change', fetchSalesData);
        endDateInput.addEventListener('change', fetchSalesData);

        // Cargar datos iniciales si las fechas ya tienen un valor
        fetchSalesData();
    }
});