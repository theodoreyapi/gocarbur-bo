<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — GoCarbu Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background: var(--secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .login-card {
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, .3);
        }

        .login-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
        }

        .login-logo-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .login-logo-text {
            font-size: 20px;
            font-weight: 800;
            color: var(--text);
        }

        .login-logo-text span {
            display: block;
            font-size: 12px;
            font-weight: 400;
            color: var(--text-muted);
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="login-logo">
            <div class="login-logo-icon">⛽</div>
            <div class="login-logo-text">GoCarbu <span>Espace Administrateur</span></div>
        </div>

        <h2 style="font-size:22px;font-weight:700;margin-bottom:6px">Connexion</h2>
        <p style="color:var(--text-muted);font-size:14px;margin-bottom:28px">Accédez à votre tableau de bord
            d'administration.</p>

        <div style="margin-bottom:16px">
            <label class="form-label">Adresse email</label>
            <div style="position:relative">
                <i class="fa-solid fa-envelope"
                    style="position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:14px"></i>
                <input type="email" class="form-control" style="padding-left:38px" placeholder="admin@gocarbu.com"
                    id="email" value="admin@gocarbu.com">
            </div>
        </div>

        <div style="margin-bottom:24px">
            <label class="form-label">Mot de passe</label>
            <div style="position:relative">
                <i class="fa-solid fa-lock"
                    style="position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:14px"></i>
                <input type="password" class="form-control" style="padding-left:38px;padding-right:40px"
                    placeholder="••••••••" id="password" value="password">
                <i class="fa-solid fa-eye" id="eyeIcon"
                    style="position:absolute;right:13px;top:50%;transform:translateY(-50%);color:var(--text-muted);cursor:pointer;font-size:14px"
                    onclick="togglePwd()"></i>
            </div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px">
                <input type="checkbox" checked> Se souvenir de moi
            </label>
            <a href="#" style="font-size:13px;color:var(--primary);text-decoration:none;font-weight:500">Mot de
                passe oublié ?</a>
        </div>

        <button class="btn btn-primary" style="width:100%;justify-content:center;font-size:15px" onclick="doLogin()"
            id="loginBtn">
            <i class="fa-solid fa-right-to-bracket"></i> Se connecter
        </button>

        <div style="margin-top:20px;text-align:center;font-size:12px;color:var(--text-muted)">
            <i class="fa-solid fa-shield-halved"></i> Connexion sécurisée — GoCarbu v1.0
        </div>
    </div>

    <div class="toast-container"></div>
    <script src="../js/app.js"></script>
    <script>
        function togglePwd() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.className = 'fa-solid fa-eye-slash';
            } else {
                pwd.type = 'password';
                icon.className = 'fa-solid fa-eye';
            }
            icon.style.cssText =
                'position:absolute;right:13px;top:50%;transform:translateY(-50%);color:var(--text-muted);cursor:pointer;font-size:14px';
        }

        function doLogin() {
            const btn = document.getElementById('loginBtn');
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Connexion...';
            btn.disabled = true;
            setTimeout(() => {
                localStorage.setItem('admin_token', 'demo_token_123');
                window.location.href = '../index';
            }, 1200);
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Enter') doLogin();
        });
    </script>
</body>

</html>
