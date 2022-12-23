<nav class="relative mb-4 flex flex-wrap items-center justify-between px-0 py-2 transition-all shadow-none duration-250 ease-soft-in lg:flex-nowrap lg:justify-start bg-black h-14"
    style="position: sticky;top: 0;z-index: 4" navbar-main navbar-scroll="true">
    <a style="position: absolute;top:20px;left: 10px;" class="block p-0 transition-all ease-nav-brand text-sm text-white"
        sidenav-trigger>
        <div class="w-4.5 overflow-hidden">
            <i class="ease-soft mb-0.75 relative block h-0.5 rounded-sm bg-white transition-all"></i>
            <i class="ease-soft mb-0.75 relative block h-0.5 rounded-sm bg-white transition-all"></i>
            <i class="ease-soft relative block h-0.5 rounded-sm bg-white transition-all"></i>
        </div>
    </a>
    <div class="flex items-center justify-between w-full pr-4 py-1 mx-auto flex-wrap-inherit">
        <nav>
            <h6 style="margin: 0">@yield('title')</h6>
        </nav>

        <div class="flex items-center mt-2 grow sm:mt-0 sm:mr-6 md:mr-0 lg:flex lg:basis-auto">
            <div class="flex items-center md:ml-auto md:pr-4">
                <div class="relative flex flex-wrap items-stretch w-full transition-all rounded-lg ease-soft">

                </div>
            </div>
            <ul class="flex flex-row justify-end pl-0 mb-0 list-none md-max:w-full">
                <!-- online builder btn  -->
                <!-- <li class="flex items-center">
    <a class="inline-block px-8 py-2 mb-0 mr-4 font-bold text-center uppercase align-middle transition-all bg-transparent border border-solid rounded-lg shadow-none cursor-pointer leading-pro border-fuchsia-500 ease-soft-in text-xs hover:scale-102 active:shadow-soft-xs text-fuchsia-500 hover:border-fuchsia-500 active:bg-fuchsia-500 active:hover:text-fuchsia-500 hover:text-fuchsia-500 tracking-tight-soft hover:bg-transparent hover:opacity-75 hover:shadow-none active:text-white active:hover:bg-transparent" target="_blank" href="https://www.creative-tim.com/builder/soft-ui?ref=navbar-dashboard&amp;_ga=2.76518741.1192788655.1647724933-1242940210.1644448053">Online Builder</a>
  </li> -->
                <li class="flex items-center pl-4">

                </li>


                <!-- notifications -->

            </ul>
        </div>
    </div>
</nav>
