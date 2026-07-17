<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Paiement échoué</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #f4e9ea;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .card {
            background: #fff;
            width: 420px;
            border-radius: 14px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .amount {
            text-align: right;
            font-size: 26px;
            font-weight: 700;
        }

        .circle {
            width: 110px;
            height: 110px;
            background: #e74c3c;
            border-radius: 50%;
            margin: 30px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            color: #fff;
        }

        .status {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .info-box {
            background: #fdecea;
            padding: 20px;
            border-radius: 10px;
            text-align: left;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: #e74c3c;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
        }

        .footer-link {
            margin-top: 20px;
            display: block;
            color: #555;
            text-decoration: none;
        }

        .secure {
            margin-top: 15px;
            font-size: 13px;
            color: #777;
        }
    </style>
</head>

<body>

    <div class="card">

        <!-- Montant -->
        <div class="amount">
            {{ number_format($amount ?? 0, 0, ',', ' ') }} XOF
        </div>

        <!-- Icône erreur -->
        <div class="circle">✕</div>

        <div class="status">Paiement échoué</div>

        <!-- Message erreur -->
        <div class="info-box">
            <p>{{ $message ?? 'Une erreur est survenue pendant le rechargement.' }}</p>
            <p>Veuillez réessayer.</p>
        </div>

        <!-- Retry via app -->
        <a href="gocarbu://payment/error"
            onclick="setTimeout(function(){
       window.location.href='https://www.gocarbu.com';
   }, 2000);"
            class="btn">
            Retourner dans l’application
        </a>

        <!-- Retour site
        <a href="{{ url('/') }}" class="footer-link">
            Retourner sur le site
        </a>
        -->

        <div class="secure">Paiement sécurisé</div>

    </div>

</body>

</html>
