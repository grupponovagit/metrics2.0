<x-admin.wrapper>
    <x-slot name="title">Modifica Notifica</x-slot>

    <form action="{{ route('admin.notifications.update', $notification->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Titolo -->
        <div class="form-group mt-3">
            <label for="title">Titolo Notifica</label>
            <input type="text" name="title" id="title" class="form-control rounded" value="{{ $notification->title }}" required>
        </div>

        <!-- Contenuto -->
        <div class="form-group mt-3">
            <label for="message">Contenuto Notifica</label>
            <textarea name="message" id="message" class="form-control rounded" rows="4" required>{{ $notification->message }}</textarea>
        </div>

        <!-- Immagine -->
        <div class="form-group mt-3">
            <label for="image_notification">Immagine (opzionale)</label>
            <input type="file" name="image_notification" id="image_notification" class="form-control">
            @if($notification->image_notification)
                <img src="{{ Storage::url($notification->image_notification) }}" alt="Immagine" width="50" class="mt-2">
            @endif
        </div>

        <!-- Link -->
        <div class="form-group mt-3">
            <label for="link_notification">Link (opzionale)</label>
            <input type="url" name="link_notification" id="link_notification" class="form-control" value="{{ $notification->link_notification }}">
        </div>

        <div class="form-group mt-4">
            <button type="submit" class="btn btn-primary">Aggiorna Notifica</button>
        </div>
    </form>
</x-admin.wrapper>
