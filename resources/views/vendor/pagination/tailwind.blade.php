@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="w-full flex justify-center my-10">
        <ul class="flex items-center gap-2 text-sm font-medium text-gray-700">
            {{-- Previous Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span class="px-3 py-1 rounded-md text-gray-400 cursor-default">
                        &lt; Previous
                    </span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1 rounded-md text-gray-800 hover:bg-gray-100 transition">
                        &lt; Previous
                    </a>
                </li>
            @endif

            {{-- Page Links --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li>
                        <span class="px-3 py-1 text-gray-500">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span class="px-3 py-1 rounded-md border border-gray-300 bg-white text-black shadow-sm">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}" class="px-3 py-1 rounded-md text-gray-800 hover:bg-gray-100 transition">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1 rounded-md text-gray-800 hover:bg-gray-100 transition">
                        Next &gt;
                    </a>
                </li>
            @else
                <li>
                    <span class="px-3 py-1 rounded-md text-gray-400 cursor-default">
                        Next &gt;
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
