@if ($paginator->hasPages())
    <nav aria-label="Page navigation" class="flex justify-center items-center space-x-2">
        <ul class="inline-flex items-center -space-x-px">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span class="relative block py-2 px-3 ml-0 leading-tight text-stone-500 bg-white border border-stone-300 rounded-l-lg hover:bg-stone-100 cursor-not-allowed">
                        <span class="sr-only">Previous</span>
                        <i class="bi bi-chevron-left"></i>
                    </span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" 
                       class="relative block py-2 px-3 ml-0 leading-tight text-stone-700 bg-white border border-stone-300 rounded-l-lg hover:bg-stone-100 hover:text-stone-800 transition-colors">
                        <span class="sr-only">Previous</span>
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li>
                        <span class="relative block py-2 px-3 leading-tight text-stone-500 bg-white border border-stone-300 hover:bg-stone-100">
                            {{ $element }}
                        </span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span aria-current="page" 
                                      class="relative block py-2 px-3 leading-tight text-white bg-stone-700 border border-stone-700 hover:bg-stone-800">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}" 
                                   class="relative block py-2 px-3 leading-tight text-stone-700 bg-white border border-stone-300 hover:bg-stone-100 hover:text-stone-800 transition-colors">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" 
                       class="relative block py-2 px-3 leading-tight text-stone-700 bg-white border border-stone-300 rounded-r-lg hover:bg-stone-100 hover:text-stone-800 transition-colors">
                        <span class="sr-only">Next</span>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            @else
                <li>
                    <span class="relative block py-2 px-3 leading-tight text-stone-500 bg-white border border-stone-300 rounded-r-lg hover:bg-stone-100 cursor-not-allowed">
                        <span class="sr-only">Next</span>
                        <i class="bi bi-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
        
        {{-- Page Info --}}
        <div class="ml-6 text-sm text-stone-600">
            Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}
        </div>
    </nav>
@endif
