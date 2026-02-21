// Sales Graph JavaScript
let salesChart = null;

function loadSalesGraph() {
    console.log('Loading sales graph...');
    const graphLoading = document.getElementById('graphLoading');
    const graphContent = document.getElementById('graphContent');
    
    graphLoading.style.display = 'block';
    graphContent.style.display = 'none';
    
    fetch("/admin/sales/graph-data")
        .then(response => {
            console.log('Fetch response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Received data:', data);
            graphLoading.style.display = 'none';
            graphContent.style.display = 'block';
            
            renderSalesGraph(data);
        })
        .catch(error => {
            console.error('Error loading graph data:', error);
            graphLoading.style.display = 'none';
            graphContent.style.display = 'block';
            graphContent.innerHTML = 
                '<div class="alert alert-danger">Error loading graph data. Please try again.</div>';
        });
}

function renderSalesGraph(data) {
    console.log('Rendering graph with data:', data);
    const ctx = document.getElementById('salesGraph').getContext('2d');
    
    // Destroy existing chart if it exists
    if (salesChart) {
        salesChart.destroy();
    }
    
    const chartData = {
        labels: data.map(item => item.date),
        datasets: [{
            label: 'Sales Revenue (₱)',
            data: data.map(item => item.sales),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }, {
            label: 'Total Orders',
            data: data.map(item => item.orders),
            borderColor: 'rgb(54, 162, 235)',
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderWidth: 2,
            fill: false,
            tension: 0.4,
            yAxisID: 'y1'
        }]
    };
    
    console.log('Chart data:', chartData);
    
    salesChart = new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                if (context.dataset.label.includes('Revenue')) {
                                    label += '₱' + context.parsed.y.toFixed(2);
                                } else {
                                    label += context.parsed.y;
                                }
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Sales Revenue (₱)'
                    },
                    beginAtZero: true
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Total Orders'
                    },
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
    
    if (salesChart) {
        console.log('Chart created successfully');
    } else {
        console.error('Chart creation failed');
    }
}

function refreshGraph() {
    loadSalesGraph();
}

// Load graph when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadSalesGraph();
});
