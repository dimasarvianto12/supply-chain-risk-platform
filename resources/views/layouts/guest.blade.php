<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Supply Chain Risk Platform')</title>
    <!-- Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f3f4f6; /* Light gray background */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }
        
        .auth-logo {
            margin-bottom: 2rem;
            color: #4b5563; /* Gray-600 */
        }
        
        .auth-logo i {
            font-size: 4rem;
        }

        .auth-card {
            width: 100%;
            max-width: 440px;
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 2rem 2.5rem;
            border: none;
        }

        .auth-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 0.5rem;
        }

        .auth-input {
            border-radius: 0.375rem;
            border: 1px solid #d1d5db;
            padding: 0.6rem 1rem;
            font-size: 1rem;
            color: #111827;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .auth-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25);
            outline: 0;
        }

        .auth-btn {
            background-color: #1f2937; /* Dark Gray / Almost Black */
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.6rem 1.25rem;
            border-radius: 0.375rem;
            border: none;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            transition: background-color 0.15s ease-in-out;
        }

        .auth-btn:hover {
            background-color: #374151;
            color: white;
        }

        .auth-link {
            font-size: 0.875rem;
            color: #4b5563;
            text-decoration: underline;
            transition: color 0.15s ease-in-out;
        }

        .auth-link:hover {
            color: #111827;
        }
    </style>
</head>
<body>
    
    <div class="auth-logo text-center">
        <!-- Logo -->
        <a href="/" class="text-decoration-none text-secondary">
            <i class="fas fa-shield-halved"></i>
        </a>
    </div>

    <div class="auth-card">
        @yield('content')
    </div>

</body>
</html>
