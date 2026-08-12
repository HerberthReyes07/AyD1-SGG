<x-guest-layout>
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <p class="text-muted mb-3">
        {{ __('Elige cómo quieres recibir tu código de verificación y luego ingrésalo abajo.') }}
    </p>

    <form method="POST" action="{{ route('two-factor.send') }}" class="mb-4">
        @csrf

        <x-input-label for="channel" :value="__('Enviar código por')" />

        <div class="d-flex align-items-center gap-2 mt-1">
            <select id="channel" name="channel" class="form-select w-auto">
                @foreach ($channels as $channel)
                    <option value="{{ $channel->value }}">{{ $channel->label() }}</option>
                @endforeach
            </select>

            <x-secondary-button type="submit">
                {{ __('Enviar código') }}
            </x-secondary-button>
        </div>

        <x-input-error :messages="$errors->get('channel')" class="mt-2" />
    </form>

    <form method="POST" action="{{ route('two-factor.challenge') }}">
        @csrf

        <div>
            <x-input-label for="code" :value="__('Código de verificación')" />

            <x-text-input
                id="code"
                class="mt-1"
                type="text"
                name="code"
                inputmode="numeric"
                autocomplete="one-time-code"
                maxlength="6"
                autofocus
                required
            />

            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="d-flex justify-content-end mt-3">
            <x-primary-button>
                {{ __('Verificar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
