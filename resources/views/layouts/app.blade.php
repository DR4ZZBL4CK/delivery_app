<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gestión de Contactos')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', sans-serif;
            background-color: #041A48;
            min-height: 100vh;
            line-height: 1.6;
            color: #FFFFFF;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 15% 50%, rgba(18, 198, 235, 0.15), transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(87, 128, 156, 0.15), transparent 25%);
            filter: blur(40px);
            z-index: -1;
            pointer-events: none;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        .header {
            background: rgba(4, 26, 72, 0.8);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(18, 198, 235, 0.2);
            padding: 0.8rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
        }

        .logo img {
            height: 50px;
            width: auto;
            object-fit: contain;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: #12C6EB;
            letter-spacing: 0.5px;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 1.5rem;
            align-items: center;
        }

        .nav-links a {
            color: #FFFFFF;
            text-decoration: none;
            padding: 0.6rem 1.2rem;
            border-radius: 50px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .nav-links a:hover, .nav-links a.active {
            background: rgba(18, 198, 235, 0.15);
            color: #12C6EB;
            box-shadow: 0 0 15px rgba(18, 198, 235, 0.2);
        }

        .btn-logout {
            background: transparent;
            border: 1px solid #57809C;
            color: #57809C;
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
        }

        .btn-logout:hover {
            background: rgba(220, 53, 69, 0.1);
            border-color: #dc3545;
            color: #dc3545;
        }

        /* Main Content */
        .main-content {
            padding: 2.5rem 0;
            min-height: calc(100vh - 80px);
        }

        /* Cards */
        .card {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            margin-bottom: 2rem;
        }

        .card-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.8rem;
            color: #12C6EB;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-weight: 700;
        }
        
        .card-header p {
            color: #B0D4F0 !important;
        }

        /* Buttons */
        .btn {
            padding: 0.8rem 1.8rem;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.95rem;
            letter-spacing: 0.3px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #12C6EB 0%, #0a9ab8 100%);
            color: #041A48;
            box-shadow: 0 4px 15px rgba(18, 198, 235, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(18, 198, 235, 0.4);
            filter: brightness(1.1);
        }

        .btn-success {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 204, 113, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
            color: #041A48;
            box-shadow: 0 4px 15px rgba(241, 196, 15, 0.3);
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(241, 196, 15, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
        }

        .btn-secondary {
            background: rgba(87, 128, 156, 0.2);
            color: #12C6EB;
            border: 1px solid rgba(18, 198, 235, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(18, 198, 235, 0.1);
            border-color: #12C6EB;
            transform: translateY(-2px);
        }

        /* Forms */
        .form-group {
            margin-bottom: 1.8rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.6rem;
            font-weight: 500;
            color: #12C6EB;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 0.9rem 1.2rem;
            background: rgba(4, 26, 72, 0.6);
            border: 1px solid rgba(87, 128, 156, 0.3);
            border-radius: 10px;
            font-size: 1rem;
            color: #FFFFFF;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #12C6EB;
            background: rgba(4, 26, 72, 0.8);
            box-shadow: 0 0 0 3px rgba(18, 198, 235, 0.15);
        }
        
        .form-control::placeholder {
            color: #8AAAC8;
        }

        .form-control.is-invalid {
            border-color: #e74c3c;
        }

        .invalid-feedback {
            color: #e74c3c;
            font-size: 0.85rem;
            margin-top: 0.4rem;
        }

        /* Alerts */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            border: 1px solid transparent;
        }

        .alert-success {
            background: rgba(46, 204, 113, 0.15);
            color: #2ecc71;
            border-color: rgba(46, 204, 113, 0.3);
        }

        .alert-danger {
            background: rgba(231, 76, 60, 0.15);
            color: #e74c3c;
            border-color: rgba(231, 76, 60, 0.3);
        }

        /* Table */
        .table-responsive {
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 0;
        }

        .table th,
        .table td {
            padding: 1.2rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .table th {
            background: rgba(4, 26, 72, 0.9);
            font-weight: 600;
            color: #12C6EB;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }

        .table td {
            background: rgba(255, 255, 255, 0.01);
            color: #e0e0e0;
        }

        .table tbody tr:hover td {
            background: rgba(18, 198, 235, 0.05);
        }

        /* Search */
        .search-box {
            display: flex;
            gap: 1rem;
            margin-bottom: 2.5rem;
            align-items: center;
            background: rgba(255, 255, 255, 0.02);
            padding: 1rem;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .search-input {
            flex: 1;
            max-width: 100%;
            background: transparent;
            border: none;
            padding: 0.5rem;
        }
        
        .search-input:focus {
            background: transparent;
            box-shadow: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav {
                flex-direction: column;
                gap: 1.5rem;
            }

            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
                gap: 1rem;
            }
            
            .logo-text {
                display: none;
            }

            .search-box {
                flex-direction: column;
                align-items: stretch;
            }

            .table-responsive {
                overflow-x: auto;
            }
        }

        /* Dashboard Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }

        .stat-card {
            position: relative;
            background: linear-gradient(135deg, rgba(18, 198, 235, 0.1) 0%, rgba(4, 26, 72, 0.3) 100%);
            padding: 2.5rem;
            border-radius: 20px;
            border: 2px solid rgba(18, 198, 235, 0.2);
            box-shadow: 0 10px 40px rgba(18, 198, 235, 0.15);
            backdrop-filter: blur(10px);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
            cursor: pointer;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(18, 198, 235, 0.05) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .stat-card:hover {
            transform: translateY(-10px) scale(1.02);
            border-color: rgba(18, 198, 235, 0.5);
            box-shadow: 0 20px 60px rgba(18, 198, 235, 0.3);
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #12C6EB 0%, #0a9ab8 100%);
            border-radius: 50%;
            box-shadow: 0 8px 25px rgba(18, 198, 235, 0.4);
            transition: all 0.4s ease;
            animation: pulse 2s ease-in-out infinite;
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.15) rotate(5deg);
            box-shadow: 0 12px 35px rgba(18, 198, 235, 0.6);
        }

        .stat-icon i {
            font-size: 3rem;
            color: #041A48;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: #12C6EB;
            text-align: center;
            margin: 1rem 0 0.5rem;
            text-shadow: 0 0 20px rgba(18, 198, 235, 0.5);
            transition: all 0.3s ease;
        }

        .stat-card:hover .stat-number {
            transform: scale(1.1);
            color: #FFFFFF;
        }

        .stat-label {
            text-align: center;
            font-size: 1.1rem;
            color: #B0D4F0;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: color 0.3s ease;
        }

        .stat-card:hover .stat-label {
            color: #FFFFFF;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 8px 25px rgba(18, 198, 235, 0.4);
            }
            50% {
                box-shadow: 0 8px 35px rgba(18, 198, 235, 0.6);
            }
        }

        /* Loading spinner */
        .spinner {
            border: 3px solid rgba(18, 198, 235, 0.1);
            border-top: 3px solid #12C6EB;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 0.8rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 0.35rem 0.8rem;
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: 6px;
            text-align: center;
            letter-spacing: 0.5px;
        }

        .badge-info {
            background: rgba(18, 198, 235, 0.15);
            color: #12C6EB;
            border: 1px solid rgba(18, 198, 235, 0.3);
        }

        .badge-success {
            background: rgba(46, 204, 113, 0.15);
            color: #2ecc71;
            border: 1px solid rgba(46, 204, 113, 0.3);
        }

        .badge-warning {
            background: rgba(241, 196, 15, 0.15);
            color: #f1c40f;
            border: 1px solid rgba(241, 196, 15, 0.3);
        }

        .badge-danger {
            background: rgba(231, 76, 60, 0.15);
            color: #e74c3c;
            border: 1px solid rgba(231, 76, 60, 0.3);
        }
        
        /* Modal */
        .modal-content {
            background: #06235e !important;
            border: 1px solid rgba(18, 198, 235, 0.2) !important;
            color: white !important;
        }
        
        .modal h2 {
            color: #12C6EB !important;
        }
    </style>
    @stack('styles')
</head>
<body>
    @auth
    <header class="header">
        <nav class="container">
            <div class="nav">
                <a href="{{ route('dashboard') }}" class="logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo">
                </a>
                <ul class="nav-links">
                    <li><a href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="{{ route('paquetes.index') }}"><i class="fas fa-box"></i> Paquetes</a></li>
                    
                    @if(auth()->user()->role === 'admin')
                        <li><a href="{{ route('api.documentation') }}"><i class="fas fa-book"></i> API Docs</a></li>
                        <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-user-shield"></i> Admin</a></li>
                        <li><a href="{{ route('camioneros.index') }}"><i class="fas fa-user"></i> Camioneros</a></li>
                        <li><a href="{{ route('camiones.index') }}"><i class="fas fa-truck-loading"></i> Camiones</a></li>
                    @endif
                    
                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn-logout">
                                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    @endauth

    <main class="main-content">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>
        // Función para mostrar confirmación antes de eliminar
        function confirmDelete(event) {
            if (!confirm('¿Estás seguro de que quieres eliminar este contacto?')) {
                event.preventDefault();
            }
        }

        // Auto-ocultar alertas después de 5 segundos
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 5000);
    </script>
    @stack('scripts')
</body>
</html>
