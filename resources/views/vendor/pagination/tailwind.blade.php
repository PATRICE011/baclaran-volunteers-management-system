@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="w-full flex justify-center my-6 sm:my-10">
        <ul class="flex items-center gap-1 sm:gap-2 text-xs sm:text-sm font-medium text-gray-700">
            {{-- Previous Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span class="px-2 py-1 sm:px-3 sm:py-1 rounded-md text-gray-400 cursor-default">
                        <span class="hidden sm:inline">&lt; Previous</span>
                        <span class="sm:hidden">&lt;</span>
                    </span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}"
                        class="px-2 py-1 sm:px-3 sm:py-1 rounded-md text-gray-800 hover:bg-gray-100 transition">
                        <span class="hidden sm:inline">&lt; Previous</span>
                        <span class="sm:hidden">&lt;</span>
                    </a>
                </li>
            @endif

            {{-- Page Links --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="hidden sm:block">
                        <span class="px-3 py-1 text-gray-500">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @php
                            $currentPage = $paginator->currentPage();
                            $showOnMobile = $page == $currentPage ||
                                $page == $currentPage - 1 ||
                                $page == $currentPage + 1 ||
                                $page == 1 ||
                                $page == $paginator->lastPage();
                        @endphp

                        <li class="{{ $showOnMobile ? '' : 'hidden sm:block' }}">
                            @if ($page == $paginator->currentPage())
                                <span class="px-2 py-1 sm:px-3 sm:py-1 rounded-md border border-gray-300 bg-white text-black shadow-sm">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                    class="px-2 py-1 sm:px-3 sm:py-1 rounded-md text-gray-800 hover:bg-gray-100 transition">
                                    {{ $page }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- Next Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}"
                        class="px-2 py-1 sm:px-3 sm:py-1 rounded-md text-gray-800 hover:bg-gray-100 transition">
                        <span class="hidden sm:inline">Next &gt;</span>
                        <span class="sm:hidden">&gt;</span>
                    </a>
                </li>
            @else
                <li>
                    <span class="px-2 py-1 sm:px-3 sm:py-1 rounded-md text-gray-400 cursor-default">
                        <span class="hidden sm:inline">Next &gt;</span>
                        <span class="sm:hidden">&gt;</span>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif