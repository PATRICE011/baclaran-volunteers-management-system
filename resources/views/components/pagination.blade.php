@if ($paginator->hasPages())
  <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
      <span class="px-3 py-2 text-sm text-gray-400 border border-gray-300 rounded-l-md bg-white cursor-default">&laquo;</span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 text-sm text-gray-700 border border-gray-300 rounded-l-md bg-white hover:bg-gray-100">&laquo;</a>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($elements as $element)
      @if (is_string($element))
        <span class="px-3 py-2 text-sm text-gray-400 border border-gray-300 bg-white cursor-default">{{ $element }}</span>
      @endif

      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <span class="px-3 py-2 text-sm text-white bg-blue-600 border border-blue-600">{{ $page }}</span>
          @else
            <a href="{{ $url }}" class="px-3 py-2 text-sm text-gray-700 border border-gray-300 bg-white hover:bg-gray-100">{{ $page }}</a>
          @endif
        @endforeach
      @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 text-sm text-gray-700 border border-gray-300 rounded-r-md bg-white hover:bg-gray-100">&raquo;</a>
    @else
      <span class="px-3 py-2 text-sm text-gray-400 border border-gray-300 rounded-r-md bg-white cursor-default">&raquo;</span>
    @endif
  </nav>
@endif
