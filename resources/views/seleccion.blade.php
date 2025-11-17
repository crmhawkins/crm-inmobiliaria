<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Inmobiliaria - CRM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css"
        integrity="sha512-t4GWSVZO1eC8BM339Xd7Uphw5s17a86tIZIj8qRxhnKub6WoyhnrxeCIMeAqBPgdZGlCcG2PrZjMc+Wr78+5Xg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.bunny.net/css?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

        .selection-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 800px;
        }

        .title-section {
            text-align: center;
            margin-bottom: 50px;
        }

        .title-section h1 {
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .title-section p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
            font-weight: 400;
        }

        .selection-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 30px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
            color: inherit;
        }

        .selection-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 70px rgba(0,0,0,0.4);
            text-decoration: none;
            color: inherit;
        }

        .selection-card .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .selection-card img {
            max-height: 120px;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
            transition: transform 0.3s ease;
        }

        .selection-card:hover img {
            transform: scale(1.05);
        }

        .selection-card h2 {
            color: #2d3748;
            font-size: 1.75rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 10px;
        }

        .selection-card .arrow {
            text-align: center;
            margin-top: 15px;
            color: var(--corporate-green);
            font-size: 1.5rem;
            transition: transform 0.3s ease;
        }

        .selection-card:hover .arrow {
            transform: translateX(5px);
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        @media (max-width: 768px) {
            .title-section h1 {
                font-size: 2rem;
            }

            .selection-card {
                padding: 30px 20px;
            }

            .selection-card img {
                max-height: 100px;
            }

            .cards-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="selection-container">
        <div class="title-section">
            <h1>CRM Inmobiliaria</h1>
            <p>Selecciona la inmobiliaria a la que deseas acceder</p>
        </div>

        <div class="cards-container">
            <a href="{{route('home', ['boton' => 'sayco'])}}" class="selection-card">
                <div class="logo-container">
                    <img src="{{ asset('images/logosayco.png') }}" alt="Sayco">
                </div>
                <h2>SAYCO</h2>
                <div class="arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="{{route('home', ['boton' => 'sancer'])}}" class="selection-card">
                <div class="logo-container">
                    <img src="{{ asset('images/logosancer.png') }}" alt="Sancer">
                </div>
                <h2>SANCER</h2>
                <div class="arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.min.js"
        integrity="sha512-3dZ9wIrMMij8rOH7X3kLfXAzwtcHpuYpEgQg1OA4QAob1e81H8ntUQmQm3pBudqIoySO5j0tHN4ENzA6+n2r4w=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"
        integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>
</html>
