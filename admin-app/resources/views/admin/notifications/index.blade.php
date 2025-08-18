<x-admin.wrapper>
    <x-slot name="title">Gestione Notifiche</x-slot>

    <div class="d-flex justify-content-between mb-4">
        <h3>Elenco Notifiche</h3>
        <a href="{{ route('admin.notifications.create') }}" class="btn btn-success">Crea Notifica</a>
    </div>

    @if ($notifications->isEmpty())
        <div class="alert alert-warning">Non ci sono notifiche disponibili.</div>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Titolo</th>
                    <th>Messaggio</th>
                    <th>Data Creazione</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($notifications as $notification)
                    <tr>
                        <td>{{ $notification->id }}</td>
                        <td>{{ $notification->title }}</td> <!-- Aggiunto titolo -->
                        <td>{{ Str::limit($notification->message, 50) }}</td>
                        <td>{{ $notification->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.notifications.edit', $notification->id) }}"
                                class="btn btn-primary btn-sm">Modifica</a>
                            <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST"
                                style="display:inline;"
                                onsubmit="return confirm('Sei sicuro di voler eliminare questa notifica?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Elimina</button>
                            </form>
                            <!-- Pulsante per inviare la notifica -->
                            <form action="{{ route('admin.notifications.send', $notification->id) }}" method="POST"
                                style="display:inline;"
                                onsubmit="return confirm('Sei sicuro di voler inviare questa notifica a tutti gli utenti?');">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Invia Notifica</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</x-admin.wrapper>
