<div class="grid grid-cols-2 gap-6">
    @php
        use Illuminate\Support\Facades\Storage;
    @endphp
    @if (Storage::exists('public/' . $state))
        <div class="text-center">
            <div style="display: flex; justify-content: center">
                {{-- <img src="{{ asset('assets/img/json-file.png') }}" alt="" width="70"> --}}
                <iframe src="{{ asset('storage/' . $state) }}" frameborder="0"></iframe>
            </div>
            <small>Archivo de estado de exportación por MSP</small>
            <br>
            <small
                style="display: block;font-size: 10px;">{{ \Carbon\Carbon::parse(Storage::lastModified('public/' . $state))->timezone('America/Mexico_City')->format('d-m-Y h:i A') }}</small>
            <br>
            <i class="fas fa-trash-alt text-red-600" style="cursor: pointer"
                wire:click.prevent="deleteState('{{ str_replace('\\', '/', $state) }}')"></i>
        </div>
    @endif
    @if (Storage::exists('public/' . $stateAll))
        <div class="text-center">
            <div style="display: flex; justify-content: center">
                {{-- <img src="{{ asset('assets/img/json-file.png') }}" alt="" width="70"> --}}
                <iframe src="{{ asset('storage/' . $stateAll) }}" frameborder="0"></iframe>
            </div>
            <small>Archivo de estado de exportación masiva</small>
            <br>
            <small
                style="display: block;font-size: 10px;">{{ \Carbon\Carbon::parse(Storage::lastModified('public/' . $stateAll))->timezone('America/Mexico_City')->format('d-m-Y h:i A') }}</small>
            <br>
            <i class="fas fa-trash-alt text-red-600" style="cursor: pointer"
                wire:click.prevent="deleteState('{{ str_replace('\\', '/', $stateAll) }}')"></i>
        </div>
    @endif
</div>
