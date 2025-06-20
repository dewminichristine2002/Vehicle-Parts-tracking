<!DOCTYPE html>
<html>
<head>

    <title>Ideal Motors Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard-style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Add these lines with your other script imports -->
<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts-liquidfill@3/dist/echarts-liquidfill.min.js"></script>

<style> 
.chart-card {
    background-color: #000000;
    padding: 20px;
    border-radius: 20px;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.05);
    margin: 20px 0;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.sidebar {
    position: fixed;
    top: 90px; /* 🔼 raised it up (was 110px) */
    bottom: 20px; /* ⬆ adds space from bottom */
    width: 80px;
    background-color: #000000;
    border-radius: 40px;
    padding: 15px 0;
    z-index: 1000;
    display: flex;
    flex-direction: column;
    align-items: left;
    justify-content: space-between;
    box-shadow: 0 0 15px rgba(0,0,0,0.4);
}
</style>

</head>

<body style="background-color: #191919; color: #fff;">

<!-- Top Header -->

    <div id="main-header" style="
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 80px;
        background-color: black;
        color: white;
        z-index: 9999;
        width: 100%;
        transition: height 0.3s ease;
    ">
    <div class="p-3 text-center fw-bold fs-4 position-relative">
    <a href="https://dealers.idealgrouplk.com/dealer/dashboard">
        <img src="{{ asset('images/logo.webp') }}" class="position-absolute start-0 top-0 m-3" style="height: 50px;" />
    </a>
</div>
    <div class="position-absolute end-0 top-0 mt-3 me-3">
    <a href="#" class="user-avatar">
        <img src="{{ asset('images/cus.png') }}" alt="Profile" />
    </a>
</div>

</div>

<!-- Adjust this padding to match max height of header -->
<div style="padding-top: 60px;"></div>



<!-- Sidebar -->
<div id="sidebar" class="sidebar d-flex flex-column p-2 m-3" onmouseenter="toggleSidebar()" onmouseleave="hidebar()">
  <a href="/dealer/dashboard" class="nav-link mt-1"><span class="icon"><img src="{{ asset('images/dashboard.png') }}"></span><span class="label">Dashboard</span></a>
  <a href="/grn" class="nav-link mt-2"><span class="icon"><img src="{{ asset('images/item.png') }}"></span><span class="label">GRN</span></a>
  <a href="/invoices/create" class="nav-link mt-2"><span class="icon"><img src="{{ asset('images/invo.png') }}"></span><span class="label">Invoice</span></a>
  <a href="/invoices" class="nav-link mt-2"><span class="icon"><img src="{{ asset('images/invohis.png') }}"></span><span class="label">Invoice History</span></a>
  <a href="/grn/history" class="nav-link mt-2"><span class="icon"><img src="{{ asset('images/grnhis.png') }}"></span><span class="label">GRN History</span></a>
  <a href="/dealer/stock" class="nav-link mt-2"><span class="icon"><img src="{{ asset('images/store.png') }}"></span><span class="label">Store</span></a>
  <a href="/targets" class="nav-link mt-1"><span class="icon"><img src="{{ asset('images/target.png') }}"></span><span class="label">Target</span></a>
  
  <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
    <span class="icon"><img src="{{ asset('images/logout.png') }}"></span>
    <span class="label text-danger">Log Out</span>
 </a>

</div>
<form id="logout-form" action="{{ route('dealer.logout') }}" method="POST" style="display: none;">
    @csrf
</form>


 <div style="margin-left: 80px; transition: margin 0.3s;" id="main-content">
 <div class="container-fluid p-4">


    
<div id="summary" class="summary-container">
    <div class="summary-card">
        <div class="card-content">
        <p>Total Invoices</p>
        <h3 id="invoice-count" style="color: #00ffff;">Loading...</h3>
        </div>
        <i class="icon fas fa-file-invoice" style="color: #00ffff;"></i>
    </div>
    <div class="summary-card">
        <div class="card-content">
        <p>Revenue</p>
        <h3 id="income-value" style="color: #00ff00;">Loading...</h3>
        </div>
        <i class="icon fas fa-chart-line" style="color: #00ff00;"></i>
    </div>
    <div class="summary-card">
        <div class="card-content">
        <p>Expense</p>
        <h3 id="expense-value" style="color: #ff4444;">Loading...</h3>
        </div>
        <i class="icon fas fa-chart-area" style="color: #ff4444;"></i>
    </div>
    <div class="summary-card">
        <div class="card-content">
        <p>Profit</p>
        <h3 id="profit-value" style="color: #ffff00;">Loading...</h3>
        </div>
        <i class="icon fas fa-sack-dollar" style="color: #ffff00;"></i>
    </div>
</div>

  <!-- Main Charts Grid - Updated Layout -->
        <div class="container-fluid">
  <div class="row g-2">

    <!-- Top Selling Parts Chart -->
    <div class="col-lg-6 col-md-12">
      <div class="chart-card" style="height: 320px; width: 100%; background: #000000; border-radius: 20px; padding: 20px;">
        <canvas id="topSellingPartsChart"></canvas>
      </div>
    </div>

    <!-- Monthly Revenue Chart -->
    <div class="col-lg-6 col-md-12">
      <div class="chart-card" style="height: 320px; padding: 20px; background: #000000; border-radius: 20px;">
        <canvas id="monthlyRevenueChart"></canvas>
      </div>
    </div>

                <!-- 3. Monthly Target Visualization -->
                <div class="col-lg-6 col-md-12">
    <div class="chart-card text-center"> <!-- Center-align text and contents -->
        <h5 class="text-white mb-3">Monthly Target</h5>
        <div class="d-flex justify-content-center">
            <div id="monthlyTarget" style="height: 250px; width: 100%; max-width: 400px;"></div>
        </div>
    </div>
</div>


                <!-- Latest Invoices Chart -->
    <div class="col-lg-6 col-md-12">
      <div class="chart-card" style="height: 320px; padding: 20px; background: #000000; border-radius: 20px;">
        <canvas id="latestInvoicesChart"></canvas>
      </div>
    </div>
    
   <div class="col-lg-6 col-md-12">
  <div class="chart-card" style="height: 320px; padding: 0; background: #000000; border-radius: 20px; overflow: hidden;">
    <img src="https://dealers.idealgrouplk.com/ads/1.gif" 
         alt="Ad Image"
         style="width: 100%; height: 100%; object-fit: cover; border-radius: 20px;">
  </div>
</div>

              
<!-- Low Stock List (Dynamic) -->
<div class="col-lg-6 col-md-12 d-flex justify-content-center align-self-start">
  <div class="low-stock-card w-100" style="max-width: 950px; background: #fff; border-radius: 20px; padding: 20px;">
    <div class="low-stock-header d-flex justify-content-between align-items-center mb-2">
      <h5 class="fw-bold m-0 text-dark">Low Stock</h5>
    </div>
    <ul id="low-stock-list" class="list-unstyled mt-2 mb-0">
      <li class="text-muted">Loading...</li>
    </ul>
  </div>
</div>

            </div>
        </div>

    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

        function toggleSidebar() {
      const sidebar = document.getElementById("sidebar");
      const content = document.getElementById("main-content");
      sidebar.classList.toggle("expanded");
      content.style.marginLeft = sidebar.classList.contains("expanded") ? "240px" : "80px";
    }

    function hidebar() {
      const sidebar = document.getElementById("sidebar");
      const content = document.getElementById("main-content");
      sidebar.classList.remove("expanded");
      content.style.marginLeft = "80px";
    }

function loadFinanceSummary() {
    $.ajax({
        url: '/dealer/finance-summary',
        method: 'GET',
        success: function(data) {
            $('#income-value').text(data.income);
            $('#expense-value').text(data.expense);
            $('#profit-value').text(data.profit);
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", error);
            console.log("Response:", xhr.responseText);
            $('#income-value').text('Error');
            $('#expense-value').text('Error');
            $('#profit-value').text('Error');
        }
    });
}

loadFinanceSummary();
setInterval(loadFinanceSummary, 60000);

document.addEventListener("DOMContentLoaded", function () {
        fetch("/api/dealer/invoice-count")
            .then(response => response.json())
            .then(data => {
                document.getElementById("invoice-count").innerText = data.count;
            })
            .catch(error => {
                console.error("Error fetching invoice count:", error);
                document.getElementById("invoice-count").innerText = "Error";
            });
    });

     const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const revenueData = new Array(12).fill(0); // Always 12 months

    fetch('/api/dealer/monthly-revenue')
        .then(res => res.json())
        .then(data => {
            data.forEach(item => {
                const index = item.month - 1;
                revenueData[index] = parseFloat(item.total);
            });

            const ctx = document.getElementById('monthlyRevenueChart').getContext('2d');

            // 🎨 Gradient Bar Colors
            const gradientColors = [
                '#00c6ff', '#0072ff', '#00bfff', '#00e5ff',
                '#00ffe0', '#00ffbf', '#00ffaa', '#00ff95',
                '#00ff80', '#00ffcc', '#00ffff', '#1affff'
            ];

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: monthLabels,
                    datasets: [{
                        label: 'Revenue (Monthly)',
                        data: revenueData,
                        backgroundColor: gradientColors,
                        borderRadius: 12,
                        barThickness: 30,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        title: {
                            display: true,
                            text: 'Revenue (Monthly)',
                            color: '#ffffff',
                            font: { size: 18 }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: '#ccc' },
                            grid: { display: false }
                        },
                        y: {
                            ticks: {
                                color: '#ccc',
                                callback: value => value.toLocaleString()
                            },
                            beginAtZero: true,
                            suggestedMax: Math.max(...revenueData) * 1.2
                        }
                    }
                }
            });
        });


    fetch('/api/dealer/latest-invoices')
        .then(res => res.json())
        .then(data => {
            const labels = data.map(inv => `#${inv.invoice_no}`);
            const totals = data.map(inv => parseFloat(inv.grand_total));

            const ctx = document.getElementById('latestInvoicesChart').getContext('2d');

            // ✅ Create horizontal linear gradient
            const gradient = ctx.createLinearGradient(0, 100, 400, 0); // left to right
            gradient.addColorStop(0, '#0D3823');
            gradient.addColorStop(1, '#35ED95');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels.reverse(),
                    datasets: [{
                        label: 'Latest Invoices',
                        data: totals.reverse(),
                        backgroundColor: gradient,
                        borderRadius: 12,
                        barThickness: 20,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        title: {
                            display: true,
                            text: 'Latest Invoices',
                            color: '#ffffff',
                            font: { size: 18 }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: '#ccc' },
                            beginAtZero: true
                        },
                        y: {
                            ticks: { color: '#ccc' }
                        }
                    }
                }
            });
        });

    fetch('/api/dealer/top-selling-parts')
        .then(res => res.json())
        .then(data => {
            const labels = data.map(item => item.part_name);
            const quantities = data.map(item => item.total_quantity);
            const colors = ['#ff66cc', '#66ffcc', '#3399ff', '#ffe066', '#cc99ff'];

            const ctx = document.getElementById('topSellingPartsChart').getContext('2d');

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: quantities,
                        backgroundColor: colors.slice(0, labels.length),
                        borderWidth: 0,
                        hoverOffset: 10,
                        cutout: '70%',
                        borderRadius: {
                            innerStart: 15,
                            outerStart: 15,
                            innerEnd: 15,
                            outerEnd: 15
                        },
                        spacing: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                color: '#ccc',
                                usePointStyle: true,
                                padding: 20,
                                font: { size: 14 }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Top Selling Products',
                            color: '#ffffff',
                            font: { size: 18 }
                        }
                    }
                }
            });
        });


        document.addEventListener("DOMContentLoaded", function () {
    fetch('/dealer/low-stock')
        .then(res => res.json())
        .then(data => {
            const list = document.getElementById('low-stock-list');
            list.innerHTML = ''; // clear loading text
            data.forEach((item, index) => {
                const li = document.createElement('li');
                li.className = 'd-flex justify-content-between border-bottom py-1';
                li.innerHTML = `<span>${index + 1}. ${item.part_name}</span><span class="text-danger">${item.quantity}pcs</span>`;
                list.appendChild(li);
            });
        })
        .catch(err => {
            console.error("Failed to fetch low stock items", err);
        });
});

// Add this new ECharts gauge initialization:
document.addEventListener("DOMContentLoaded", function() {
    fetch('/dealer/target-percentage')
        .then(response => response.json())
        .then(data => {
            const percentage = data.percentage ? parseFloat(data.percentage) / 100 : 0;
            
            var chart = echarts.init(document.getElementById('monthlyTarget'));
            
            var option = {
                series: [{
                    type: 'liquidFill',
                    data: [percentage, percentage - 0.03, percentage - 0.06],
                    radius: '90%',
                    center: ['50%', '50%'],
                    amplitude: 6,
                    waveAnimation: true,
                    animationDuration: 2500,
                    animationDurationUpdate: 1000,
                    backgroundStyle: {
                        color: '#111'
                    },
                    label: {
                        formatter: (percentage * 100).toFixed(1) + '%',
                        fontSize: 28,
                        fontWeight: 'bold',
                        color: '#ffffff',
                        insideColor: '#000000'
                    },
                    outline: {
                        show: true,
                        borderDistance: 5,
                        itemStyle: {
                            borderColor: '#1e90ff',
                            borderWidth: 2,
                            shadowBlur: 10,
                            shadowColor: '#1e90ff'
                        }
                    },
                    color: [{
                        type: 'linear',
                        x: 0,
                        y: 0,
                        x2: 0,
                        y2: 1,
                        colorStops: [
                            { offset: 0, color: '#00c6ff' },
                            { offset: 1, color: '#0072ff' }
                        ]
                    }]
                }]
            };

            chart.setOption(option);
            
            window.addEventListener('resize', function() {
                chart.resize();
            });
        })
        .catch(error => {
            console.error('Error loading target percentage:', error);
        });
});
</script>


</body>
</html>
