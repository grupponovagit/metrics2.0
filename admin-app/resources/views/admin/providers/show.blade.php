<x-admin.wrapper>
    <x-slot name="title">
        {{ __('Fornitori') }}
    </x-slot>

    <div class="w-full py-2">
        <div class="min-w-full border-base-200 shadow">
            <table class="table-fixed w-full text-sm">
                <tbody>
                    <tr>
                        <td class="border-b border-slate-100 p-4 pl-8 text-slate-500">{{ __('Nome') }}</td>
                        <td class="border-b border-slate-100 p-4 text-slate-500">{{ $provider->name }}</td>
                    </tr>
                    <tr>
                        <td class="border-b border-slate-100 p-4 pl-8 text-slate-500">{{ __('Tipologia') }}</td>
                        <td class="border-b border-slate-100 p-4 text-slate-500">{{ $provider->type }}</td>
                    </tr>
                    <tr>
                        <td class="border-b border-slate-100 p-4 pl-8 text-slate-500">{{ __('Created') }}</td>
                        <td class="border-b border-slate-100 p-4 text-slate-500">{{ $provider->created_at->toDateTimeString() }}</td>
                    </tr>
                    <tr>
                        <td class="border-b border-slate-100 p-4 pl-8 text-slate-500">{{ __('Updated') }}</td>
                        <td class="border-b border-slate-100 p-4 text-slate-500">{{ $provider->updated_at->toDateTimeString() }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-admin.wrapper>
