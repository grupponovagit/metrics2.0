<x-admin.wrapper>
    <x-slot name="title">
        {{ __('Modifica Fornitore') }}
    </x-slot>

    <form action="{{ route('admin.providers.update', $provider->id) }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column align-items-start p-4 border rounded-4 shadow bg-light">
        @csrf
        @method('PUT') <!-- Aggiunto per il metodo PUT di aggiornamento -->

        <div class="form-group mb-3 w-100">
            <label for="name" class="form-label fs-5">{{ __('Nome') }}</label>
            <input type="text" id="name" name="name" class="form-control form-control-lg rounded-pill" value="{{ old('name', $provider->name) }}" placeholder="Nome del fornitore" required>
        </div>

        <div class="form-group mb-3 w-100">
            <label for="service_type" class="form-label fs-5">{{ __('Tipo di Servizio') }}</label>
            <select id="service_type" name="service_type" class="form-select form-select-lg rounded-pill" required>
                <option value="luce" {{ $provider->service_type == 'luce' ? 'selected' : '' }}>{{ __('Luce') }}</option>
                <option value="gas" {{ $provider->service_type == 'gas' ? 'selected' : '' }}>{{ __('Gas') }}</option>
                <option value="luce e gas" {{ $provider->service_type == 'luce e gas' ? 'selected' : '' }}>{{ __('Luce e Gas') }}</option>
                <option value="noleggio" {{ $provider->service_type == 'noleggio' ? 'selected' : '' }}>{{ __('Noleggio') }}</option>
                <option value="assicurazioni" {{ $provider->service_type == 'assicurazioni' ? 'selected' : '' }}>{{ __('Assicurazioni') }}</option>
                <option value="fibra internet" {{ $provider->service_type == 'fibra internet' ? 'selected' : '' }}>{{ __('Fibra Internet') }}</option>
            </select>
        </div>

        <div class="form-group mb-3 w-100">
            <label for="image_providers" class="form-label fs-5">{{ __('Immagine') }}</label>
            @if($provider->image_providers)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $provider->image_providers) }}" alt="{{ $provider->name }}" class="img-thumbnail" style="width: 100px; height: auto;">
                </div>
            @endif
            <input type="file" id="image_providers" name="image_providers" class="form-control form-control-lg rounded-pill">
        </div>

        <div class="d-grid w-100">
            <button type="submit" class="btn btn-primary btn-md rounded-pill">{{ __('Aggiorna Fornitore') }}</button>
        </div>
    </form>
</x-admin.wrapper>
