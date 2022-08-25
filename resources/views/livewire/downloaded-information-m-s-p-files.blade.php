<div class="w-full" style="background: white;padding: 10px;border-radius: 8px;">
    <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">
        Exportar PDFs por Información descargada
    </h5>
    <div class="grid grid-cols-5 gap-2 mt-8">
        @php
            use Illuminate\Support\Facades\Storage;
            
        @endphp
        @foreach ($downloaded_files as $file)
            {{-- <iframe src="{{ asset('storage/' . $file) }}" frameborder="0"></iframe> --}}
            <div class="text-center">
                <div style="display: flex;justify-content: center;">
                    <img src="{{ asset('assets/img/json-file.png') }}" alt="" width="50px">
                </div>
                <small style="font-size: 10px">{{ explode('_', explode('.', explode('/', $file)[1])[0])[0] }}</small>
                <small
                    style="display: block;font-size: 10px;">{{ \Carbon\Carbon::parse(Storage::lastModified('public/' . $file))->timezone('America/Mexico_City')->format('d-m-Y h:i A') }}</small>
                <i class="fas fa-play" style="cursor: pointer!important;"
                    wire:click.prevent='exportByDownloadedInformation("{{ $file }}")' wire:loading.remove></i>
                <i class="fa-solid fa-spinner fa-spin-pulse" wire:loading
                    wire:target="exportByDownloadedInformation"></i>
                <small style="display: block;font-size: 10px;" id="progressTextType">Descargando Archivos...</small>
                <small id="progressText{{ explode('_', explode('.', explode('/', $file)[1])[0])[1] }}"
                    class="text-xs font-medium text-blue-700 dark:text-white">0% (0 de 0
                    monitores)</small>
                <div class="ml-2 w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                    <div id="percentageBar{{ explode('_', explode('.', explode('/', $file)[1])[0])[1] }}"
                        class="bg-blue-600 h-2.5 rounded-full" style="width: 0%">
                    </div>
                </div>
            </div>
        @endforeach
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
                console.table(data);
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
