<div>
    <x-loading-indicator wire:loading wire:target="save" />
    <div class="mb-4">
        <div class="relative mb-1">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                </svg>
            </div>
            <input type="text" id="input-group-1" wire:model="email"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500
                @error('email') border-red-600 @enderror"
                placeholder="ejemplo@ejemplo.com">
        </div>

        <p wire:loading class="m-0"><small class="text-blue-600"><i class="fa-solid fa-spinner fa-spin-pulse"></i>
                Validando</small>
        </p>
        @error('email')
            <p class="m-0"><small class="text-red-600"><i class="fas fa-info"></i> {{ $message }}</small></p>
        @enderror
    </div>
    <div class="flex justify-start">
        <button wire:click.defer="save" class="bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded">
            <i class="fas fa-save"></i> Guardar
        </button>
    </div>
</div>
