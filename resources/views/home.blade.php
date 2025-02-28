@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Tarjeta de Usuarios -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>150</h3>
                        <p>Usuarios Registrados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="{{ route('admin.ministerios.index') }}" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- Tarjeta de Ventas -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>53<sup style="font-size: 20px">%</sup></h3>
                        <p>Crecimiento de Ventas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <a href="#" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- Tarjeta de Nuevas Órdenes -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>44</h3>
                        <p>Nuevas Órdenes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <a href="#" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- Tarjeta de Soporte -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>65</h3>
                        <p>Consultas de Soporte</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <a href="#" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Sección de Gráficos -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ventas Mensuales</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Usuarios Activos</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="usersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Últimos Registros -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Últimos Usuarios Registrados</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Fecha de Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Juan Pérez</td>
                            <td>juan@example.com</td>
                            <td>2025-02-28</td>
                        </tr>
                        <tr>
                            <td>María García</td>
                            <td>maria@example.com</td>
                            <td>2025-02-27</td>
                        </tr>
                        <tr>
                            <td>Carlos López</td>
                            <td>carlos@example.com</td>
                            <td>2025-02-26</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sección del Calendario -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Calendario de Eventos</h3>
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>

    <script>
        // Gráfico de Ventas
        var ctx1 = document.getElementById('salesChart').getContext('2d');
        var salesChart = new Chart(ctx1, {
            type: 'line',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                datasets: [{
                    label: 'Ventas',
                    data: [10, 20, 15, 30, 25, 40],
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            }
        });

        // Gráfico de Usuarios Activos
        var ctx2 = document.getElementById('usersChart').getContext('2d');
        var usersChart = new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: ['Activos', 'Inactivos'],
                datasets: [{
                    data: [80, 20],
                    backgroundColor: ['#28a745', '#dc3545']
                }]
            }
        });

        // Configuración del Calendario
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                events: [{
                        title: 'Evento 1',
                        start: '2025-02-10'
                    },
                    {
                        title: 'Evento 2',
                        start: '2025-02-15',
                        end: '2025-02-17'
                    },
                    {
                        title: 'Reunión',
                        start: '2025-02-20T10:30:00'
                    }
                ]
            });
            calendar.render();
        });
    </script>
@endsection
