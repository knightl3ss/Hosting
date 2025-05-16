@props(['serviceGroups'])

{{-- Chart Section --}}
<div class="card mt-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 text-primary">
            <i class="fas fa-chart-pie me-2"></i>Employee Years of Service Distribution
        </h5>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="toggleChartType()">
                <i class="fas fa-exchange-alt me-1"></i>Switch View
            </button>
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="downloadChart()">
                <i class="fas fa-download me-1"></i>Download
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="chart-container" style="position: relative; height: 400px;">
            <canvas id="yearsOfServiceChart"></canvas>
        </div>
    </div>
</div>

{{-- Table Section --}}
<div class="card mt-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 text-primary">
            <i class="fas fa-table me-2"></i>Years of Service Summary
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fw-semibold text-center">Service Group</th>
                        <th class="fw-semibold text-center">Number of Employees</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">
                            <span class="badge text-primary px-3 py-2">
                                <i class="fas fa-users me-2"></i>10 Years+
                            </span>
                        </td>
                        <td class="text-center">
                            @php
                                $tenYearsCount = $serviceGroups['10 Years+']->count();
                            @endphp
                            <a href="#" data-bs-toggle="modal" data-bs-target="#modal-10years" class="text-decoration-none">
                                <span class="badge bg-primary rounded-pill px-3 py-2" id="ten-years-count">
                                    {{ $tenYearsCount }}
                                </span>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            <span class="badge text-success px-3 py-2">
                                <i class="fas fa-user-friends me-2"></i>5-9 Years
                            </span>
                        </td>
                        <td class="text-center">
                            @php
                                $fiveToNineCount = $serviceGroups['5-9 Years']->count();
                            @endphp
                            <a href="#" data-bs-toggle="modal" data-bs-target="#modal-5to9years" class="text-decoration-none">
                                <span class="badge bg-success rounded-pill px-3 py-2" id="five-to-nine-count">
                                    {{ $fiveToNineCount }}
                                </span>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            <span class="badge text-info px-3 py-2">
                                <i class="fas fa-user-plus me-2"></i>Below 5 Years
                            </span>
                        </td>
                        <td class="text-center">
                            @php
                                $belowFiveCount = $serviceGroups['Below 5 Years']->count();
                            @endphp
                            <a href="#" data-bs-toggle="modal" data-bs-target="#modal-below5years" class="text-decoration-none">
                                <span class="badge bg-info rounded-pill px-3 py-2" id="below-five-count">
                                    {{ $belowFiveCount }}
                                </span>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
// Wait for both DOM and Chart.js to be ready
window.addEventListener('load', function() {
    let chart = null;
    let isDoughnut = true;
    
    // Add event listener for custom refresh event
    document.addEventListener('service-data-updated', function() {
        console.log('Service data update event received');
        initChart();
    });

    function initChart() {
        const ctx = document.getElementById('yearsOfServiceChart');
        if (!ctx) {
            console.error('Chart canvas not found');
            return;
        }

        // Get the counts directly to ensure they're current
        @php
            $tenYearsCount = $serviceGroups['10 Years+']->count();
            $fiveToNineCount = $serviceGroups['5-9 Years']->count();
            $belowFiveCount = $serviceGroups['Below 5 Years']->count();
        @endphp
        
        const tenYearsCount = {{ $tenYearsCount }};
        const fiveToNineCount = {{ $fiveToNineCount }};
        const belowFiveCount = {{ $belowFiveCount }};
        
        // Debug output to console
        console.log('Service Groups Data (from DOM):', {
            '10Years+': tenYearsCount,
            '5-9Years': fiveToNineCount,
            'Below5Years': belowFiveCount
        });
        
        // Ensure we have valid numbers
        const validTenYears = isNaN(tenYearsCount) ? 0 : tenYearsCount;
        const validFiveToNine = isNaN(fiveToNineCount) ? 0 : fiveToNineCount;
        const validBelowFive = isNaN(belowFiveCount) ? 0 : belowFiveCount;
        
        const data = {
            labels: [
                '10 Years+',
                '5-9 Years',
                'Below 5 Years'
            ],
            datasets: [{
                data: [
                    validTenYears,
                    validFiveToNine,
                    validBelowFive
                ],
                backgroundColor: [
                    'rgba(13, 110, 253, 0.8)',  // Primary blue
                    'rgba(25, 135, 84, 0.8)',   // Success green
                    'rgba(13, 202, 240, 0.8)'   // Info blue
                ],
                borderColor: [
                    'rgba(13, 110, 253, 1)',
                    'rgba(25, 135, 84, 1)',
                    'rgba(13, 202, 240, 1)'
                ],
                borderWidth: 1,
                hoverOffset: 4
            }]
        };

        const config = {
            type: isDoughnut ? 'doughnut' : 'bar',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 14
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Employee Distribution by Years of Service',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: isDoughnut ? '60%' : undefined,
                scales: !isDoughnut ? {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                } : undefined
            }
        };

        if (chart) {
            chart.destroy();
        }
        
        try {
            chart = new Chart(ctx, config);
        } catch (error) {
            console.error('Error initializing chart:', error);
        }
    }

    // Make functions globally available
    window.toggleChartType = function() {
        isDoughnut = !isDoughnut;
        initChart();
    }

    window.downloadChart = function() {
        const canvas = document.getElementById('yearsOfServiceChart');
        if (!canvas) return;
        
        const link = document.createElement('a');
        link.download = 'employee-years-of-service.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    }

    // Initialize the chart
    initChart();
});
</script>
@endpush

@push('styles')
<style>
.card {
    border: none;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
    border-radius: 10px;
}

.table {
    margin-bottom: 0;
}

.table th {
    font-weight: 600;
    border-top: none;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-weight: 500;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,0.02);
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0,0,0,0.01);
}

.table-light th {
    background-color: #f8f9fa;
}

.btn-group .btn {
    border-radius: 5px;
    margin-left: 5px;
}

.btn-outline-primary {
    color: #4e73df;
    border-color: #4e73df;
}

.btn-outline-primary:hover {
    background-color: #4e73df;
    color: white;
}

.chart-container {
    position: relative;
    margin: auto;
    height: 400px;
    width: 100%;
}
</style>
@endpush 