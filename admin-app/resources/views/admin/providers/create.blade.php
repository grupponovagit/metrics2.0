<x-admin.wrapper>
    <x-slot name="title">
        {{ __('Crea Fornitore') }}
    </x-slot>

    <form action="{{ route('admin.providers.store') }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column align-items-start p-4 border rounded-4 shadow bg-light">
        @csrf

        <div class="form-group mb-3 w-100">
            <label for="name" class="form-label fs-5">{{ __('Nome') }}</label>
            <input type="text" id="name" name="name" class="form-control form-control-lg rounded-pill" placeholder="Nome del fornitore" required>
        </div>

        <div class="form-group mb-3 w-100">
            <label for="image_providers" class="form-label fs-5">{{ __('Immagine') }}</label>
            <input type="file" id="image_providers" name="image_providers" class="form-control form-control-lg rounded-pill">
        </div>

        <div class="form-group mb-3 w-100">
            <label for="service_type" class="form-label fs-5">{{ __('Tipo di Servizio') }}</label>
            <select id="service_type" name="service_type" class="form-select form-select-lg rounded-pill" required>
                <option value="luce">{{ __('Luce') }}</option>
                <option value="gas">{{ __('Gas') }}</option>
                <option value="luce e gas">{{ __('Luce e Gas') }}</option>
                <option value="noleggio">{{ __('Noleggio') }}</option>
                <option value="assicurazioni">{{ __('Assicurazioni') }}</option>
                <option value="fibra internet">{{ __('Fibra Internet') }}</option>
            </select>            
        </div>

        <div class="d-grid w-100">
            <button type="submit" class="btn btn-primary btn-md rounded-pill">{{ __('Salva Fornitore') }}</button>
        </div>
    </form>
</x-admin.wrapper>
