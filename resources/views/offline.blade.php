<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amigos 98 Community - Offline</title>
    <meta name="theme-color" content="#008000">
    <link rel="icon" type="image/png" href="{{ asset('storage/icons/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('storage/icons/favicon.ico') }}" />
    <link rel="shortcut icon" href="{{ asset('storage/icons/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('storage/icons/apple-touch-icon.png') }}" />
    <link rel="manifest" href="{{ asset('site.webmanifest') }}" />
    
    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root,
        [data-bs-theme="light"] {
            --bs-body-bg: #f8f9fa;
            --bs-body-color: #333333;
            --primary-blue: #0084fe;
            --secondary-blue: #0b2f6c;
            --accent-color: #0b2f6c;
            --light-bg: #ffffff;
            --dark-text: #000000;
            --darker-blue: #0b2f6c;
            --light-blue: #e3f2fd;
        }

        [data-bs-theme="dark"] {
            --bs-body-bg: #1a0d2e;
            --bs-body-color: #e8e3ff;
            --primary-blue: #0b78df;
            --secondary-blue: #0b2f6c;
            --accent-color: #0b2f6c;
            --light-bg: #1a3d6b;
            --dark-text: #e8e3ff;
            --darker-blue: #0b2f6c;
            --light-blue: #1a3d6b;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--bs-body-bg) 0%, var(--light-blue) 100%);
            color: var(--bs-body-color);
            transition: all 0.3s ease;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .offline-container {
            max-width: 500px;
            text-align: center;
            padding: 2rem;
            background: var(--light-bg);
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(11, 47, 108, 0.3);
            margin: 1rem;
            width: 100%;
        }

        [data-bs-theme="dark"] .offline-container {
            background: rgba(26, 13, 46, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .logo-container {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--darker-blue) 100%);
            border-radius: 50%;
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 25px rgba(11, 47, 108, 0.3);
        }

        .logo-text {
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--darker-blue) 100%);
            border: none;
            box-shadow: 0 4px 15px rgba(11, 47, 108, 0.3);
            padding: 12px 20px;
            font-size: 1.1rem;
            border-radius: 12px;
            min-height: 48px;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--darker-blue) 0%, var(--primary-blue) 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(11, 47, 108, 0.4);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-blue);
            color: var(--primary-blue);
            padding: 12px 20px;
            font-size: 1.1rem;
            border-radius: 12px;
            min-height: 48px;
        }

        .btn-outline-primary:hover {
            background: var(--primary-blue);
            color: white;
        }

        /* Mobile Responsive Adjustments */
        @media (max-width: 576px) {
            .offline-container {
                padding: 1.5rem 1rem;
                margin: 0.5rem;
            }
            
            h2 {
                font-size: 1.5rem !important;
            }
            
            p {
                font-size: 1rem !important;
            }
            
            .btn {
                font-size: 1rem !important;
                padding: 12px 15px !important;
                min-height: 48px !important;
                width: 100%;
            }
            
            .logo-container {
                width: 80px;
                height: 80px;
            }
        }
        
        /* Ensure mobile-first design */
        @media (max-width: 768px) {
            body {
                padding: 0.5rem;
            }
            
            .offline-container {
                border-radius: 15px;
                box-shadow: 0 4px 15px rgba(11, 47, 108, 0.2);
            }
        }
    </style>
</head>
<body>
    <div class="offline-container">
        <div class="logo-container">
            <img src="{{ asset('storage/icons/ebili-logo.png') }}" alt="Amigos 98 Community Logo" style="width: 70px; height: 70px; object-fit: contain;">
        </div>
        
        <h2 class="mb-3 fw-bold" style="color: var(--primary-blue); font-size: 1.8rem;">You're Offline</h2>
        <p class="mb-4" style="font-size: 1.1rem;">It seems you're currently offline. Please check your internet connection and try again.</p>
        
        <div class="d-flex flex-column gap-3">
            <button class="btn btn-primary btn-lg" onclick="window.location.reload()">
                <i class="bi bi-arrow-clockwise me-2"></i>Retry Connection
            </button>
            <a href="{{ route('welcome') }}" class="btn btn-outline-primary btn-lg">
                <i class="bi bi-house me-2"></i>Go Home
            </a>
        </div>
        
        <div class="mt-4">
            <p class="small text-muted mb-0" style="font-size: 0.9rem;">
                <i class="bi bi-info-circle me-1"></i>When you're back online, this app will work normally again.
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>