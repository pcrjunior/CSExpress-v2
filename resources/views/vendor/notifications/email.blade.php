@component('mail::message')
{{-- Logo (opcional) --}}
<table width="100%">
    <tr>
        <td align="center">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" height="60">
        </td>
    </tr>
</table>

# Olá, {{ $user->name ?? 'usuário' }} 👋

Recebemos um pedido para redefinir sua senha.

Clique no botão abaixo para criar uma nova senha segura:

@component('mail::button', ['url' => $actionUrl, 'color' => 'primary'])
Redefinir Senha
@endcomponent

Esse link expira em {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} minutos.

Se você não fez essa solicitação, pode ignorar este e-mail.

---

Se tiver problemas com o botão acima, copie e cole o link abaixo no navegador:

[{{ $actionUrl }}]({{ $actionUrl }})

<br><br>

Obrigado,<br>
{{ config('app.name') }}

@endcomponent
