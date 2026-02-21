<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Redefinição de Senha</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 30px 0;">
    <tr>
      <td align="center">
        <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.05);">

          {{-- Cabeçalho com logo --}}
          <tr>
            <!-- <td align="center" style="padding: 30px;">
              <img src="{{ asset('images/ecs-logo.png') }}" alt="Logo" height="50" style="display:block; margin: 0 auto;">
            </td> -->
          </tr>

          {{-- Conteúdo principal --}}
          <tr>
            <td style="padding: 0 30px 30px 30px; font-family: Arial, sans-serif;">
              <h2 style="color: #333;">Olá, {{ $user->name ?? 'usuário' }} 👋</h2>
              <p style="color: #555; font-size: 16px; line-height: 1.6;">
                Recebemos uma solicitação para redefinir sua senha.
              </p>
              <p style="color: #555; font-size: 16px; line-height: 1.6;">
                Clique no botão abaixo para criar uma nova senha segura:
              </p>

              <p style="text-align: center; margin: 30px 0;">
                <a href="{{ $actionUrl }}" style="background-color: #2d89ef; color: #ffffff; padding: 15px 25px; font-size: 16px; font-weight: bold; text-decoration: none; border-radius: 6px; display: inline-block;">
                  Redefinir Senha
                </a>
              </p>

              <p style="color: #999; font-size: 14px;">
                Este link expira em {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} minutos.
              </p>

              <p style="color: #555; font-size: 16px; line-height: 1.6;">
                Se você não solicitou essa alteração, pode ignorar este e-mail.
              </p>

              {{-- Link alternativo --}}
              <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
              <p style="color: #999; font-size: 13px;">
                Caso o botão acima não funcione, copie e cole o link abaixo no navegador:
              </p>
              <p style="color: #2d89ef; word-break: break-all; font-size: 14px;">
                <a href="{{ $actionUrl }}" style="color: #2d89ef;">{{ $actionUrl }}</a>
              </p>

              {{-- Rodapé --}}
              <p style="margin-top: 40px; font-size: 13px; color: #aaa; text-align: center;">
                © {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
