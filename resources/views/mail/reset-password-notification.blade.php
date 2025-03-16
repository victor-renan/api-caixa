<x-mail::message>
# Redefinir a Senha

Uma requisição para mudar de senha foi feita agora mesmo.

Se não foi você, ignore esta mensagem.

<x-mail::button :url="$url">
Redefinir Senha
</x-mail::button>

Atenciosamente,<br>
{{ config('app.name') }}
</x-mail::message>
