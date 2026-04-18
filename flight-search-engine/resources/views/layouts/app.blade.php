<!DOCTYPE html>
<html lang="{{ session('ui_lang', 'id') }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', config('app.name', 'Laravel')) — Flight Search</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-full bg-slate-950 text-slate-100 antialiased font-sans selection:bg-sky-500/30">
        @php
            $uiLang = session('ui_lang', 'id');
            \Illuminate\Support\Facades\App::setLocale(in_array($uiLang, ['id', 'en'], true) ? $uiLang : 'id');
        @endphp
        <div class="relative isolate min-h-full overflow-hidden">
            <div
                class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(ellipse_80%_50%_at_50%_-20%,rgba(14,165,233,0.22),transparent)]"
                aria-hidden="true"
            ></div>
            <div
                class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-px bg-gradient-to-r from-transparent via-sky-400/40 to-transparent"
                aria-hidden="true"
            ></div>

            <header class="border-b border-white/5 bg-slate-950/80 backdrop-blur-md">
                <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                    <a href="{{ route('flights.search') }}" class="group flex items-center gap-3">
                        <span
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-indigo-600 shadow-lg shadow-sky-500/20 ring-1 ring-white/10 transition group-hover:shadow-sky-500/35"
                        >
                            <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M2 12h20M12 2v20M4 8l16 8M20 8L4 16" stroke-linecap="round" />
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold tracking-tight text-white">Flight Search Engine</p>
                            <p class="text-xs text-slate-400">{{ __('brand_subtitle') }}</p>
                        </div>
                    </a>
                    <nav class="flex items-center gap-2 text-sm">
                        <a
                            href="{{ route('flights.search') }}"
                            class="rounded-lg px-3 py-2 font-medium text-slate-300 transition hover:bg-white/5 hover:text-white"
                        >
                            {{ __('menu_search_flights') }}
                        </a>
                        <div class="ml-2 flex items-center gap-1 rounded-lg border border-white/10 bg-white/5 px-2 py-1">
                            <span class="px-1 text-xs text-slate-400">{{ __('label_language') }}</span>
                            <form method="POST" action="{{ route('language.switch', ['lang' => 'id']) }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="rounded-md px-2 py-1 text-xs font-medium transition {{ $uiLang === 'id' ? 'bg-sky-500/20 text-sky-200 ring-1 ring-sky-500/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                                >
                                    ID
                                </button>
                            </form>
                            <form method="POST" action="{{ route('language.switch', ['lang' => 'en']) }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="rounded-md px-2 py-1 text-xs font-medium transition {{ $uiLang === 'en' ? 'bg-sky-500/20 text-sky-200 ring-1 ring-sky-500/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                                >
                                    EN
                                </button>
                            </form>
                        </div>
                    </nav>
                </div>
            </header>

            <main class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
                @yield('content')
            </main>

            <footer class="border-t border-white/5 py-8 text-center text-xs text-slate-500">
                <p>{{ config('app.name') }} — MVC · Repository · Service</p>
            </footer>
        </div>
        @stack('scripts')
    </body>
</html>
