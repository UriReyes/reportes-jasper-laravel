@extends('layouts.layout')

@section('content')
    @livewire('reportes-site-jasper', ['customers_its'=>$customers_its])
@endsection

    <!-- Font Awesome if you need it
 <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
 -->

    <link rel="stylesheet" href="https://unpkg.com/tailwindcss@2.2.19/dist/tailwind.min.css" />
    <!--Replace with your tailwind.css once created-->

    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700" rel="stylesheet">

    <!-- Animation CSS-->
    <style>
        .slide-in-bottom {
            -webkit-animation: slide-in-bottom .5s cubic-bezier(.25, .46, .45, .94) both;
            animation: slide-in-bottom .5s cubic-bezier(.25, .46, .45, .94) both
        }

        .slide-in-bottom-h1 {
            -webkit-animation: slide-in-bottom .5s cubic-bezier(.25, .46, .45, .94) .5s both;
            animation: slide-in-bottom .5s cubic-bezier(.25, .46, .45, .94) .5s both
        }

        .slide-in-bottom-subtitle {
            -webkit-animation: slide-in-bottom .5s cubic-bezier(.25, .46, .45, .94) .75s both;
            animation: slide-in-bottom .5s cubic-bezier(.25, .46, .45, .94) .75s both
        }

        .fade-in {
            -webkit-animation: fade-in 1.2s cubic-bezier(.39, .575, .565, 1.000) 1s both;
            animation: fade-in 1.2s cubic-bezier(.39, .575, .565, 1.000) 1s both
        }

        .bounce-top-icons {
            -webkit-animation: bounce-top .9s 1s both;
            animation: bounce-top .9s 1s both
        }

        @-webkit-keyframes slide-in-bottom {
            0% {
                -webkit-transform: translateY(1000px);
                transform: translateY(1000px);
                opacity: 0
            }

            100% {
                -webkit-transform: translateY(0);
                transform: translateY(0);
                opacity: 1
            }
        }

        @keyframes slide-in-bottom {
            0% {
                -webkit-transform: translateY(1000px);
                transform: translateY(1000px);
                opacity: 0
            }

            100% {
                -webkit-transform: translateY(0);
                transform: translateY(0);
                opacity: 1
            }
        }

        @-webkit-keyframes bounce-top {
            0% {
                -webkit-transform: translateY(-45px);
                transform: translateY(-45px);
                -webkit-animation-timing-function: ease-in;
                animation-timing-function: ease-in;
                opacity: 1
            }

            24% {
                opacity: 1
            }

            40% {
                -webkit-transform: translateY(-24px);
                transform: translateY(-24px);
                -webkit-animation-timing-function: ease-in;
                animation-timing-function: ease-in
            }

            65% {
                -webkit-transform: translateY(-12px);
                transform: translateY(-12px);
                -webkit-animation-timing-function: ease-in;
                animation-timing-function: ease-in
            }

            82% {
                -webkit-transform: translateY(-6px);
                transform: translateY(-6px);
                -webkit-animation-timing-function: ease-in;
                animation-timing-function: ease-in
            }

            93% {
                -webkit-transform: translateY(-4px);
                transform: translateY(-4px);
                -webkit-animation-timing-function: ease-in;
                animation-timing-function: ease-in
            }

            25%,
            55%,
            75%,
            87% {
                -webkit-transform: translateY(0);
                transform: translateY(0);
                -webkit-animation-timing-function: ease-out;
                animation-timing-function: ease-out
            }

            100% {
                -webkit-transform: translateY(0);
                transform: translateY(0);
                -webkit-animation-timing-function: ease-out;
                animation-timing-function: ease-out;
                opacity: 1
            }
        }

        @keyframes bounce-top {
            0% {
                -webkit-transform: translateY(-45px);
                transform: translateY(-45px);
                -webkit-animation-timing-function: ease-in;
                animation-timing-function: ease-in;
                opacity: 1
            }

            24% {
                opacity: 1
            }

            40% {
                -webkit-transform: translateY(-24px);
                transform: translateY(-24px);
                -webkit-animation-timing-function: ease-in;
                animation-timing-function: ease-in
            }

            65% {
                -webkit-transform: translateY(-12px);
                transform: translateY(-12px);
                -webkit-animation-timing-function: ease-in;
                animation-timing-function: ease-in
            }

            82% {
                -webkit-transform: translateY(-6px);
                transform: translateY(-6px);
                -webkit-animation-timing-function: ease-in;
                animation-timing-function: ease-in
            }

            93% {
                -webkit-transform: translateY(-4px);
                transform: translateY(-4px);
                -webkit-animation-timing-function: ease-in;
                animation-timing-function: ease-in
            }

            25%,
            55%,
            75%,
            87% {
                -webkit-transform: translateY(0);
                transform: translateY(0);
                -webkit-animation-timing-function: ease-out;
                animation-timing-function: ease-out
            }

            100% {
                -webkit-transform: translateY(0);
                transform: translateY(0);
                -webkit-animation-timing-function: ease-out;
                animation-timing-function: ease-out;
                opacity: 1
            }
        }

        @-webkit-keyframes fade-in {
            0% {
                opacity: 0
            }

            100% {
                opacity: 1
            }
        }

        @keyframes fade-in {
            0% {
                opacity: 0
            }

            100% {
                opacity: 1
            }
        }

    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @livewireStyles
</head>


<body class="leading-normal tracking-normal text-gray-900" style="font-family: 'Source Sans Pro', sans-serif;">
    <div class="h-screen pb-14 bg-right bg-cover">
        <nav class="bg-white border-gray-200 px-2 sm:px-4 py-2.5 rounded dark:bg-gray-800">
            <div class="container flex flex-wrap justify-between items-center mx-auto">
                <a href="{{url('/')}}" class="flex items-center">
                    <img src="{{ asset('assets/img/logo-KIO.webp') }}" class="mr-3 h-6 sm:h-9" alt="Flowbite Logo">
                    <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">KIO NETWORKS</span>
                </a>
                <button data-collapse-toggle="mobile-menu" type="button"
                    class="inline-flex items-center p-2 ml-3 text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                    aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <svg class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>
                <div class="hidden w-full md:block md:w-auto" id="mobile-menu">
                    <ul class="flex flex-col mt-4 md:flex-row md:space-x-8 md:mt-0 md:text-sm md:font-medium">
                        <li>
                            <a href="{{ url('/') }}"
                                class="block py-2 pr-4 pl-3 text-white bg-blue-700 rounded md:bg-transparent md:text-blue-700 md:p-0 dark:text-white"
                                aria-current="page">Inicio</a>
                        </li>
                        <li>
                            <a href="{{ route('pdfs') }}"
                                class="block py-2 pr-4 pl-3 text-gray-700 border-b border-gray-100 hover:bg-gray-50 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-gray-400 md:dark:hover:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Storage</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!--Main-->
        <div class="container pt-6 md:pt-6 px-6 mx-auto flex flex-wrap flex-col md:flex-row items-center">
            <!--Left Col-->
            @livewire('reportes-site-jasper', ['customers_its'=>$customers_its])
            <!--Footer-->
            <div class="w-full pt-7 pb-3 text-sm text-center md:text-left fade-in">
                <a class="text-gray-500 no-underline hover:no-underline" href="#">&copy; KIO Networks
                    {{ date('Y') }}</a>
            </div>

        </div>


    </div>
    @livewireScripts

    <script>
        Pusher.logToConsole = false;
        var pusher = new Pusher('97627475d5972b5f156a', {
            cluster: 'us2'
        });
        var channel = pusher.subscribe('progress-report');
        channel.bind('process-report', function(data) {
            Livewire.emit('current-percentage', data);
        });
    </script>
@endsection
