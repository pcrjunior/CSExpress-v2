<x-guest-layout>
    <div class="max-w-md mx-auto mt-10 bg-white p-6 shadow-md rounded-lg">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-4">
            Esqueceu sua senha?
        </h2>

        <p class="text-sm text-gray-600 mb-6 text-center">
            Sem problemas. Informe seu e-mail e enviaremos um link para redefinir sua senha.
        </p>

        <!-- Mensagem de status da sessão -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Campo Email -->
            <div class="mb-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input
                    id="email"
                    class="block mt-1 w-full"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Botão -->
            <div class="mt-6">
                <x-primary-button class="w-full justify-center">
                    {{ __('Enviar link de redefinição') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
