<div class="w-full" style="background: white;padding: 10px;border-radius: 8px;">
    <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">
        <i class="fas fa-file mr-2"></i> Exportar PDFs por Información descargada
    </h5>
    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative text-xs" role="alert">
        <strong class="font-bold">Importante!</strong>
        <span class="block sm:inline">Los archivos generados son sobreescritos de acuerdo a la
            fecha de modificación, exporte el más reciente de acuerdo a su última "Descarga/Exportación" individual
            o masiva.</span>
    </div>
    <div class="w-full mt-4" style="text-align: end">
        @php
            use Illuminate\Support\Facades\Storage;
        @endphp
        <div class="container flex mx-auto">
            <div class="flex border-2 rounded">
                <a class="flex items-center justify-center px-4 border-r">
                    <svg class="w-6 h-6 text-gray-600" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24">
                        <path
                            d="M16.32 14.9l5.39 5.4a1 1 0 0 1-1.42 1.4l-5.38-5.38a8 8 0 1 1 1.41-1.41zM10 16a6 6 0 1 0 0-12 6 6 0 0 0 0 12z">
                        </path>
                    </svg>
                </a>
                <input wire:loading.attr="disabled" type="text" class="px-4 py-2 w-80" placeholder="Busca..."
                    wire:model="filterDownloaded">
            </div>
            <div style="text-align: end; width: 100%">
                <strong>{{ count($downloaded_files) }} archivo(s)</strong>
            </div>
        </div>
        <div style="height: calc(100vh - 280px); overflow-y: scroll;overflow-x: unset"
            class="grid grid-cols-5 gap-2 mt-4">
            @foreach ($downloaded_files as $file)
                {{-- <iframe src="{{ asset('storage/' . $file) }}" frameborder="0"></iframe> --}}
                <div class="text-center">
                    <div style="display: flex;justify-content: center;">
                        <img src="{{ asset('assets/img/json-file.png') }}" alt="" width="50px">
                    </div>
                    <small
                        style="font-size: 10px">{{ explode('_', explode('.', explode('/', $file)[1])[0])[0] }}</small>
                    <small
                        style="display: block;font-size: 10px;">{{ \Carbon\Carbon::parse(Storage::lastModified('public/' . $file))->timezone('America/Mexico_City')->format('d-m-Y h:i A') }}</small>
                    <i class="fas fa-play" style="cursor: pointer!important;"
                        wire:click.prevent='exportByDownloadedInformation("{{ $file }}")'
                        wire:loading.remove></i>
                    <i class="fa-solid fa-spinner fa-spin-pulse" wire:loading
                        wire:target="exportByDownloadedInformation"></i>
                    <small style="display: block;font-size: 10px;" id="progressTextType">Descargando Archivos...</small>
                    <small id="progressText{{ explode('_', explode('.', explode('/', $file)[1])[0])[1] }}"
                        class="text-xs font-medium text-blue-700 dark:text-white">0% (0 de 0
                        monitores)</small>
                    <div class="m-0 w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                        <div id="percentageBar{{ explode('_', explode('.', explode('/', $file)[1])[0])[1] }}"
                            class="bg-blue-600 h-2.5 rounded-full" style="width: 0%">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('current-percentage', (data) => {
                let {
                    progress,
                    completed_reports,
                    total_monitors,
                    zaaid,
                    customer,
                    textType
                } = data;
                //console.table(data);
                if (zaaid != '0000') {
                    document.getElementById(`percentageBar${zaaid}`).style.width = progress + '%';
                    document.getElementById(`progressText${zaaid}`).innerHTML = `
                    ${progress}% (${completed_reports} de ${total_monitors} monitores)
                    `;
                }
            });

        });
    </script>
</div>
