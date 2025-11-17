<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Iniciar Sesión - CRM Inmobiliaria</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.1/font/bootstrap-icons.css"
        integrity="sha512-CaTMQoJ49k4vw9XO0VpTBpmMz8XpCWP5JhGmBvuBqCOaOHWENWO1CrVl09u4yp8yBVSID6smD4+gpzDJVQOPwQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css"
        integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --corporate-green: #6b8e6b;
            --corporate-green-dark: #5a7c5a;
            --corporate-green-light: #7fa07f;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--corporate-green) 0%, var(--corporate-green-dark) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
            z-index: 0;
        }

        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-section h1 {
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 30px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .logos-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .logos-container img {
            max-height: 80px;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
            transition: transform 0.3s ease;
        }

        .logos-container img:hover {
            transform: scale(1.05);
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .login-card h2 {
            color: #2d3748;
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            color: #4a5568;
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f7fafc;
        }

        .form-control:focus {
            border-color: var(--corporate-green);
            background: white;
            box-shadow: 0 0 0 3px rgba(107, 142, 107, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: #a0aec0;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            z-index: 1;
        }

        .input-icon .form-control {
            padding-left: 45px;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--corporate-green) 0%, var(--corporate-green-dark) 100%);
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(107, 142, 107, 0.4);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(107, 142, 107, 0.6);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .form-check {
            margin-top: 10px;
        }

        .form-check-input {
            border: 2px solid #e2e8f0;
            border-radius: 4px;
        }

        .form-check-input:checked {
            background-color: var(--corporate-green);
            border-color: var(--corporate-green);
        }

        .form-check-label {
            color: #4a5568;
            font-size: 0.9rem;
        }

        .invalid-feedback {
            font-size: 0.85rem;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .login-card {
                padding: 30px 20px;
            }

            .logo-section h1 {
                font-size: 2rem;
            }

            .logos-container {
                gap: 20px;
            }

            .logos-container img {
                max-height: 60px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="logo-section">
            <h1>CRM Inmobiliaria</h1>
            <div class="logos-container">
                <img src="{{ asset('images/logosayco.png') }}" alt="Sayco">
                <img src="{{ asset('images/logosancer.png') }}" alt="Sancer">
            </div>
        </div>

        <div class="login-card">
            <h2>Iniciar Sesión</h2>
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label">Correo electrónico</label>
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                        <input id="email" type="email"
                            class="form-control @error('email') is-invalid @enderror" name="email"
                            value="{{ old('email') }}" required autocomplete="email"
                            placeholder="tu@email.com" autofocus>
                    </div>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input id="password" type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="••••••••" name="password" required
                            autocomplete="current-password">
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember"
                        id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        Recordar contraseña
                    </label>
                </div>

                <button type="submit" class="btn btn-login mt-4">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Iniciar Sesión
                </button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.1.min.js"
        integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js"
        integrity="sha384-IDwe1+LCz02ROU9k972gdyvl+AESN10+x7tBKgc9I5HFtuNz0wWnPclzo6p9vxnk" crossorigin="anonymous">
    </script>
</body>

</html>
