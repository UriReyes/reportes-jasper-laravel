<div class="w-full" style="background: white;padding: 10px;border-radius: 8px;">
    <div class="">
        <div class="w-full">
            <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                {{ $titleErrorAPI }}
            </h5>
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Upps!</strong>
                <span class="block sm:inline">{{ $errorAPI }}</span>

            </div>
            <div class="mt-5" style="display: flex;justify-content: center;">
                <img src="{{ asset('assets/img/errors/cannot_access_api.svg') }}" width="300px" alt="ErrorAPI">
            </div>
            <div class="mt-5" style="text-align: center">
                <button onclick="window.location.reload();return 0;">
                    <i class="fa-solid fa-rotate-right"></i> Recargar
                </button>
            </div>
        </div>
    </div>
</div>
