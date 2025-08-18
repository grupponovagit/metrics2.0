<x-admin.wrapper>
    <x-slot name="title">
        {{ __('Fornitori') }}
    </x-slot>

    @can('adminCreate', \Plank\Mediable\Media::class)
    <x-admin.add-link href="{{ route('admin.providers.create') }}">
        {{ __('Aggiungi Fornitore') }}
    </x-admin.add-link>
    @endcan

    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Nome') }}</th>
                <th>{{ __('Immagine') }}</th>
                <th>{{ __('Servizi') }}</th>
                <th>{{ __('Creato il') }}</th>
                <th>{{ __('Azioni') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($providers as $provider)
                <tr>
                    <td>{{ $provider->id }}</td>
                    <td>{{ $provider->name }}</td>
                    <td>
                        @if ($provider->image_providers)
                            <img src="{{ asset('storage/' . $provider->image_providers) }}" alt="{{ $provider->name }}" width="80">
                        @else
                            {{ __('Nessuna Immagine') }}
                        @endif
                    </td>
                    <td>{{ $provider->service_type }}</td>
                    <td>{{ $provider->created_at }}</td>
                    <td>
                        <a href="{{ route('admin.providers.edit', $provider->id) }}" class="btn btn-md btn-primary">{{ __('Modifica') }}</a>
                        <form action="{{ route('admin.providers.destroy', $provider->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-md btn-danger">{{ __('Elimina') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-admin.wrapper>
