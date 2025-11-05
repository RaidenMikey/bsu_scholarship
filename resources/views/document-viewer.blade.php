<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Document - {{ $document->document_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        .viewer-container {
            width: 100vw;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .viewer-header {
            background: #dc2626;
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .viewer-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        iframe {
            width: 100%;
            flex: 1;
            border: none;
        }
        .fallback-message {
            padding: 2rem;
            text-align: center;
            background: #f3f4f6;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="viewer-container">
        <div class="viewer-header">
            <div>
                <h1 class="text-xl font-bold">{{ $document->document_name }}</h1>
                <p class="text-sm opacity-90">{{ $document->getFileTypeDisplayName() }} • {{ $document->getFileSizeFormatted() }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ asset('storage/' . ltrim($document->file_path, '/')) }}" 
                   download 
                   class="bg-white text-red-700 px-4 py-2 rounded hover:bg-gray-100 transition-colors font-semibold">
                    Download
                </a>
                <button onclick="window.close()" 
                        class="bg-white text-red-700 px-4 py-2 rounded hover:bg-gray-100 transition-colors font-semibold">
                    Close
                </button>
            </div>
        </div>
        
        <div class="viewer-content">
            @if($isLocalhost || empty($viewers))
                <!-- Localhost or no viewers available - show download option -->
                <div class="fallback-message">
                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-700 mb-2">Document Viewer</h2>
                    <p class="text-gray-600 mb-4">
                        @if($isLocalhost)
                            Online document viewers are not available on localhost. Please download the document to view it.
                        @else
                            Document viewers require a publicly accessible URL. Please download the document to view it.
                        @endif
                    </p>
                    <a href="{{ $downloadUrl }}" 
                       download 
                       class="bg-red-700 text-white px-6 py-3 rounded-lg hover:bg-red-800 transition-colors font-semibold inline-block">
                        Download Document
                    </a>
                </div>
            @else
                <!-- Try viewers in order -->
                @foreach($viewers as $index => $viewer)
                    <iframe src="{{ $viewer['url'] }}" 
                            id="docViewer{{ $index }}" 
                            class="viewer-iframe {{ $index > 0 ? 'hidden' : '' }}"></iframe>
                @endforeach
                
                <!-- Fallback if all viewers fail -->
                <div class="fallback-message hidden" id="fallback">
                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-700 mb-2">Unable to display document</h2>
                    <p class="text-gray-600 mb-4">The document viewer is not available. Please download the document to view it.</p>
                    <a href="{{ $downloadUrl }}" 
                       download 
                       class="bg-red-700 text-white px-6 py-3 rounded-lg hover:bg-red-800 transition-colors font-semibold inline-block">
                        Download Document
                    </a>
                </div>
            @endif
        </div>
    </div>

    @if(!$isLocalhost && !empty($viewers))
    <script>
        // Check if iframes loaded successfully
        let currentViewerIndex = 0;
        const viewers = @json($viewers);
        const fallback = document.getElementById('fallback');
        
        function tryNextViewer() {
            if (currentViewerIndex < viewers.length - 1) {
                // Hide current viewer
                const currentIframe = document.getElementById('docViewer' + currentViewerIndex);
                if (currentIframe) {
                    currentIframe.classList.add('hidden');
                }
                
                // Try next viewer
                currentViewerIndex++;
                const nextIframe = document.getElementById('docViewer' + currentViewerIndex);
                if (nextIframe) {
                    nextIframe.classList.remove('hidden');
                }
            } else {
                // All viewers failed, show fallback
                document.querySelectorAll('.viewer-iframe').forEach(iframe => {
                    iframe.style.display = 'none';
                });
                if (fallback) {
                    fallback.classList.remove('hidden');
                }
            }
        }
        
        // Check if first iframe loaded successfully
        const firstIframe = document.getElementById('docViewer0');
        if (firstIframe) {
            firstIframe.onerror = function() {
                tryNextViewer();
            };
            
            // Timeout check - if iframe doesn't load in 10 seconds, try next viewer
            setTimeout(function() {
                try {
                    // Try to access iframe content
                    firstIframe.contentWindow.document;
                } catch(e) {
                    // Cross-origin error is normal, but check if iframe is actually visible
                    if (firstIframe.offsetHeight === 0 || firstIframe.offsetWidth === 0) {
                        tryNextViewer();
                    }
                }
            }, 10000);
        }
    </script>
    @endif
</body>
</html>

