<aside style="background: #ffffff"
    class="max-w-62.5 ease-nav-brand z-990 fixed inset-y-0 block w-full -translate-x-full flex-wrap items-center justify-between overflow-y-auto border-0 p-0 antialiased shadow-none transition-transform duration-200 xl:left-0 xl:translate-x-0 xl:bg-transparent">
    <div class="h-14 bg-white w-full flex justify-center" style="position: sticky;top:0; align-items: center">
        <i class="text-white absolute top-0 right-0 hidden p-4 opacity-50 cursor-pointer fas fa-times text-black xl:hidden"
            sidenav-close="" aria-hidden="true"></i>
        <a class="block m-0 text-sm whitespace-nowrap text-black text-center">
            <img src="{{ asset('assets/img/logos/folder-documents.png') }}"
                class="inline transition-all duration-200 ease-nav-brand" alt="main_logo" style="height: 43px;">
            <strong class="pt-10" style="font-size: 15px">MONDRIAN</strong>
        </a>
    </div>

    <hr class="h-px my-1 bg-transparent bg-gradient-to-r from-transparent via-black/40 to-transparent" />

    <div class="items-center block w-auto max-h-screen overflow-auto grow basis-full">
        <ul class="flex flex-col pl-0 mb-0">
            <li class="w-full">
                <span
                    class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors text-black">
                    <i class="fas fa-info-circle mr-2"></i> Exportación
                </span>
            </li>
            <li class="mt-0.5 w-full">
                <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap rounded-lg px-4 transition-colors text-black"
                    href="{{ route('home') }}">
                    <div class=" {{ request()->is('/') ? 'from-blue-700 to-green-500 bg-gradient-to-tl text-white' : '' }} shadow-soft-2xl mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5"
                        style="background: #FECC2F">
                        <i class="text-black fas fa-home "></i>
                    </div>
                    <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Exportar Por MSP</span>
                </a>
            </li>

            <li class="mt-0.5 w-full">
                <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors text-black"
                    href="{{ route('export-all') }}">
                    <div style="background: #FECC2F"
                        class="{{ request()->is('export-all') ? 'from-blue-700 to-green-500 bg-gradient-to-tl text-white' : '' }} shadow-soft-2xl shadow-soft-2xl mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                        <i class="text-black fas fa-file-download"></i>
                    </div>
                    <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Exportar Todo</span>
                </a>
            </li>
            <li class="w-full">
                <span
                    class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors text-black">
                    <i class="fas fa-info-circle mr-2"></i> Configuración
                </span>
            </li>
            <li class="mt-0.5 w-full">
                <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors text-black"
                    href="{{ route('sort-msps') }}">
                    <div style="background: #FECC2F"
                        class="{{ request()->is('sort-msps') ? 'from-blue-700 to-green-500 bg-gradient-to-tl text-white' : '' }} shadow-soft-2xl shadow-soft-2xl mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                        <i class="text-black fas fa-grip-vertical"></i>
                    </div>
                    <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Ordenar Exportación
                        M</span>
                </a>
            </li>
            <li class="mt-0.5 w-full">
                <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors text-black"
                    href="{{ route('downloaded-information') }}">
                    <div style="background: #FECC2F"
                        class="{{ request()->is('downloaded-information') ? 'from-blue-700 to-green-500 bg-gradient-to-tl text-white' : '' }} shadow-soft-2xl shadow-soft-2xl mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                        <i class="text-black fas fa-share"></i>
                    </div>
                    <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Archivos Exportados</span>
                </a>
            </li>
            <li class="mt-0.5 w-full">
                <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors text-black"
                    href="{{ route('administracion') }}">
                    <div style="background: #FECC2F"
                        class="{{ request()->is('administracion') ? 'from-blue-700 to-green-500 bg-gradient-to-tl text-white' : '' }} shadow-soft-2xl shadow-soft-2xl mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                        <i class="text-black fas fa-save"></i>
                    </div>
                    <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Estados Almacenados</span>
                </a>
            </li>
            <li class="mt-0.5 w-full">
                <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors text-black"
                    href="{{ route('cron') }}">
                    <div style="background: #FECC2F"
                        class="{{ request()->is('cron') ? 'from-blue-700 to-green-500 bg-gradient-to-tl text-white' : '' }} shadow-soft-2xl shadow-soft-2xl mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                        <i class="text-black fa-solid fa-clock"></i>
                    </div>
                    <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Tarea Programada</span>
                </a>
            </li>
            <li class="mt-0.5 w-full">
                <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors text-black"
                    href="{{ route('notificaciones') }}">
                    <div style="background: #FECC2F"
                        class="{{ request()->is('notificaciones') ? 'from-blue-700 to-green-500 bg-gradient-to-tl text-white' : '' }} shadow-soft-2xl shadow-soft-2xl mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                        <i class="text-black fa-solid fa-bell"></i>
                    </div>
                    <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Notificaciones</span>
                </a>
            </li>
            <li class="mt-0.5 w-full">
                <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors text-black"
                    href="{{ route('folder') }}">
                    <div style="background: #FECC2F"
                        class="{{ request()->is('folder') ? 'from-blue-700 to-green-500 bg-gradient-to-tl text-white' : '' }} shadow-soft-2xl shadow-soft-2xl mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                        <i class="text-black fas fa-folder"></i>
                    </div>
                    <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Gestor de Archivos</span>
                </a>
            </li>
            <li class="mt-0.5 w-full">
                <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors text-black"
                    href="{{ route('logs') }}">
                    <div style="background: #FECC2F"
                        class="{{ request()->is('logs') ? 'from-blue-700 to-green-500 bg-gradient-to-tl text-white' : '' }} shadow-soft-2xl shadow-soft-2xl mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                        <i class="text-black fas fa-laptop-medical"></i>
                    </div>
                    <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Logs</span>
                </a>
            </li>
        </ul>
    </div>
</aside>
