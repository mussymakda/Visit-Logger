<x-filament-panels::page>
    <div class="max-w-md mx-auto space-y-6">
        
        <!-- Mobile QR Link Banner (shown when accessing via QR code link) -->
        <div id="qr-deep-link" style="display: none;">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                <div class="text-blue-800 font-semibold mb-2">Processing QR Code</div>
                <div class="text-blue-600 text-sm">Loading sponsor information...</div>
                <div class="mt-3">
                    <div class="loading-spinner mx-auto"></div>
                </div>
            </div>
        </div>
        
        <!-- QR Scanner Section -->
        <x-filament::section id="qr-scanner-section">
            <x-slot name="heading">
                <div class="flex items-center justify-between">
                    <span>QR Scanner</span>
                    <span id="scanner-status" class="text-sm text-gray-500">Ready</span>
                </div>
            </x-slot>
            
            <div class="space-y-4">
                <!-- QR Reader Display -->
                <div class="w-full">
                    <div id="qr-reader" class="w-full h-[350px] bg-gray-900 rounded-xl shadow-lg overflow-hidden relative">
                        <!-- Scanner overlay when not active -->
                        <div id="scanner-placeholder" class="absolute inset-0 flex items-center justify-center bg-gray-100 rounded-xl">
                            <div class="text-center">
                                <div class="w-16 h-16 mx-auto mb-3 bg-primary-100 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m-2 0h-2m3-4h2m-3 0V9a3 3 0 00-3-3H9m1-1V4m6 6v1m0-2a3 3 0 013 3v1m0 0v1m0-1h-1m-1 2v4"></path>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-600">Initializing camera...</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Scanner Controls -->
                <div class="flex justify-center space-x-3" style="display: none;">
                    <x-filament::button 
                        id="start-scanner" 
                        color="primary" 
                        size="sm"
                    >
                        Start Scanner
                    </x-filament::button>
                    <x-filament::button 
                        id="stop-scanner" 
                        color="danger" 
                        size="sm"
                    >
                        Stop Scanner
                    </x-filament::button>
                </div>
                
                <p class="text-center text-xs text-gray-500">
                    Point camera at sponsor QR code
                </p>
            </div>
        </x-filament::section>

        <!-- Status Messages -->
        <div id="status-container" class="space-y-2"></div>

        <!-- Sponsor Information -->
        <div id="sponsor-section" style="display: none;">
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        <span>Sponsor Found</span>
                    </div>
                </x-slot>
                
                <div class="space-y-4">
                    <div id="sponsor-details"></div>
                    
                    <div class="flex justify-center space-x-3">
                        <x-filament::button id="take-photo" color="primary">
                            Take Photo
                        </x-filament::button>
                        
                    </div>
                </div>
            </x-filament::section>
        </div>

        <!-- Photo Capture -->
        <div id="photo-section" style="display: none;">
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                        <span>Site Photo</span>
                    </div>
                </x-slot>
                
                <div class="space-y-4">
                    <!-- Camera View -->
                    <div class="flex justify-center">
                        <video 
                            id="camera-video" 
                            class="w-full max-w-sm rounded-lg border-2 border-gray-200"
                            autoplay 
                            muted 
                            playsinline
                            style="display: none;"
                        ></video>
                        <canvas 
                            id="photo-canvas" 
                            class="w-full max-w-sm rounded-lg border-2 border-gray-200"
                            style="display: none;"
                        ></canvas>
                    </div>
                    
                    <!-- Photo Preview -->
                    <div id="photo-preview" class="text-center" style="display: none;">
                        <img 
                            id="preview-image" 
                            class="w-full max-w-sm rounded-lg border-2 border-gray-200 mx-auto mb-4"
                            alt="Photo preview"
                        >
                        <div class="space-x-3">
                            <x-filament::button id="confirm-photo" color="success">
                                Use Photo
                            </x-filament::button>
                            <x-filament::button id="retake-photo" color="gray">
                                Retake
                            </x-filament::button>
                        </div>
                    </div>
                    
                    <!-- Camera Controls -->
                    <div id="camera-controls" class="flex justify-center space-x-3">
                        <x-filament::button id="capture-photo" color="primary">
                            Capture
                        </x-filament::button>
                        <x-filament::button id="toggle-camera" color="gray" size="sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Flip Camera
                        </x-filament::button>
                    </div>
                </div>
            </x-filament::section>
        </div>

        <!-- Submit Visit -->
        <div id="submit-section" style="display: none;">
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        <span>Submit Visit</span>
                    </div>
                </x-slot>
                
                <div class="space-y-4">
                    <!-- Visit Summary -->
                    <div id="visit-summary" class="bg-gray-50 rounded-lg p-4 text-sm"></div>
                    
                    <!-- Visit Notes -->
                    <div class="space-y-2">
                        <label for="visit-notes" class="text-sm font-medium text-gray-700">Visit Notes (Optional)</label>
                        <textarea
                            id="visit-notes"
                            rows="3"
                            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500"
                            placeholder="Add any notes about your visit..."
                        ></textarea>
                    </div>
                    
                    <!-- Submit Controls -->
                    <div class="flex justify-center space-x-3 mt-4">
                        <x-filament::button id="confirm-submit" color="success">
                            <span id="submit-button-text">Submit Visit</span>
                        </x-filament::button>
                    </div>
                    
                    <!-- Progress Indicator -->
                    <div id="submit-progress" class="text-center space-y-2" style="display: none;">
                        <x-filament::loading-indicator />
                        <p class="text-sm text-gray-600">Submitting visit...</p>
                    </div>
                </div>
            </x-filament::section>
        </div>

        <!-- Success Message -->
        <div id="success-section" style="display: none;">
            <x-filament::section>
                <div class="text-center space-y-4">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                        <span class="text-2xl text-green-600">✓</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-green-800">Success!</h3>
                        <p class="text-green-600">Visit logged successfully</p>
                    </div>
                    <x-filament::button 
                        id="log-another-visit" 
                        color="success"
                        onclick="window.location.reload()"
                    >
                        Log Another Visit
                    </x-filament::button>
                </div>
            </x-filament::section>
        </div>
    </div>

    @push('styles')
    <style>
        .loading-spinner {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* QR Scanner Styles - Inline for server compatibility */
        #qr-reader {
            border: none !important;
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
            box-shadow: none !important;
        }

        #qr-reader.active #scanner-placeholder {
            display: none !important;
        }

        #qr-reader__scan_region {
            border: none !important;
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        #qr-reader video,
        #qr-reader__scan_region > img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            border-radius: 0.75rem !important;
            border: none !important;
        }

        #qr-reader__dashboard {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px) !important;
            border: none !important;
            border-radius: 0.5rem !important;
            margin: 0.5rem !important;
            padding: 0.75rem !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        }

        #qr-reader__dashboard button {
            background: rgb(59 130 246) !important;
            color: white !important;
            border: none !important;
            border-radius: 0.5rem !important;
            padding: 0.5rem 1rem !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
        }

        #qr-reader__dashboard button:hover {
            background: rgb(37 99 235) !important;
            transform: translateY(-1px) !important;
        }

        #qr-reader__header {
            background: transparent !important;
            border: none !important;
            display: none !important;
        }

        #qr-reader__camera_permission_button,
        #qr-reader__dashboard_section_swaplink,
        #qr-reader__dashboard_section_csr,
        #qr-reader select,
        #qr-reader__status_span,
        #qr-reader__filescan_input,
        #qr-reader__scan_type_change,
        .qr-reader__dashboard_section_csr {
            display: none !important;
        }

        #qr-reader * {
            box-shadow: none !important;
        }

        #qr-reader div[style*="border"] {
            border: none !important;
        }
    </style>
    @endpush

    @push('scripts')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    
    <script>
    let html5QrcodeScanner;
    let cameraStream;
    let currentSponsor = null;
    let capturedPhotoBlob = null;
    let currentFacingMode = "user"; // Start with front camera for selfies

    // Mobile navigation fix for Filament
    document.addEventListener('DOMContentLoaded', function() {
        if (window.innerWidth <= 1024) {
            console.log('=== MOBILE NAVIGATION SETUP ===');
            
            // Wait for Filament to load
            setTimeout(function() {
                // Find the sidebar toggle button (multiple possible selectors)
                const toggleBtn = document.querySelector('[aria-label="Toggle navigation"]') ||
                                document.querySelector('[data-sidebar-toggle]') ||
                                document.querySelector('.fi-topbar-nav-toggle') ||
                                document.querySelector('[x-on\\:click*="sidebar"]') ||
                                document.querySelector('button[type="button"]');
                
                const sidebar = document.querySelector('.fi-sidebar');
                
                console.log('Found toggle button:', !!toggleBtn);
                console.log('Found sidebar:', !!sidebar);
                
                if (toggleBtn && sidebar) {
                    // Override the toggle functionality
                    toggleBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        console.log('Mobile nav: Toggle clicked');
                        
                        // Check current state
                        const isHidden = sidebar.style.transform === 'translateX(-100%)' || 
                                       sidebar.style.display === 'none' ||
                                       sidebar.classList.contains('hidden');
                        
                        if (isHidden) {
                            // Show sidebar
                            sidebar.style.position = 'fixed';
                            sidebar.style.top = '0';
                            sidebar.style.left = '0';
                            sidebar.style.height = '100vh';
                            sidebar.style.width = '16rem';
                            sidebar.style.zIndex = '50';
                            sidebar.style.background = 'white';
                            sidebar.style.transform = 'translateX(0)';
                            sidebar.style.transition = 'transform 0.3s ease';
                            sidebar.style.boxShadow = '0 10px 25px -5px rgba(0, 0, 0, 0.25)';
                            sidebar.style.display = 'block';
                            sidebar.classList.remove('hidden');
                            
                            // Add backdrop
                            const backdrop = document.createElement('div');
                            backdrop.className = 'mobile-sidebar-backdrop';
                            backdrop.style.cssText = `
                                position: fixed;
                                inset: 0;
                                background: rgba(0, 0, 0, 0.5);
                                z-index: 40;
                            `;
                            document.body.appendChild(backdrop);
                            
                            // Close on backdrop click
                            backdrop.addEventListener('click', function() {
                                closeSidebar();
                            });
                            
                            console.log('Mobile nav: Sidebar opened');
                        } else {
                            closeSidebar();
                        }
                    });
                    
                    function closeSidebar() {
                        sidebar.style.transform = 'translateX(-100%)';
                        const backdrop = document.querySelector('.mobile-sidebar-backdrop');
                        if (backdrop) {
                            backdrop.remove();
                        }
                        console.log('Mobile nav: Sidebar closed');
                    }
                    
                    // Initially hide sidebar on mobile
                    sidebar.style.transform = 'translateX(-100%)';
                    
                    console.log('Mobile navigation setup complete');
                } else {
                    console.log('Mobile navigation: Could not find required elements');
                    console.log('All buttons:', document.querySelectorAll('button').length);
                    console.log('Sidebar elements:', document.querySelectorAll('[class*="sidebar"]').length);
                }
            }, 1000); // Wait for Filament to fully load
        }
    });

    // Run initial JavaScript test
    console.log('=== INITIAL JAVASCRIPT TEST ===');
    console.log('JavaScript is executing');
    
    // Test DOM readiness
    if (document.readyState === 'loading') {
        console.log('DOM still loading...');
    } else {
        console.log('DOM already loaded');
    }
    
    // Test basic DOM access
    const testElement = document.querySelector('body');
    console.log('Can access body:', !!testElement);
    
    // Test element counts
    const buttons = document.querySelectorAll('button');
    console.log(`Found ${buttons.length} buttons on page`);
    
    const inputs = document.querySelectorAll('input');
    console.log(`Found ${inputs.length} inputs on page`);
    
    const divs = document.querySelectorAll('div');
    console.log(`Found ${divs.length} divs on page`);
    
    console.log('=== END INITIAL TEST ===');

    document.addEventListener('DOMContentLoaded', function() {
        console.log('=== DASHBOARD LOADED ===');
        console.log('DOM ready, setting up event listeners...');
        
        // Test if basic JS is working
        try {
            setupEventListeners();
            console.log('Event listeners set up successfully');
            
            // Check for URL parameters first (for direct QR links)
            const urlParams = new URLSearchParams(window.location.search);
            const sponsorFromUrl = urlParams.get('sponsor');
            
            if (sponsorFromUrl) {
                console.log('Sponsor ID found in URL:', sponsorFromUrl);
                // Show mobile banner and hide scanner for direct QR access
                document.getElementById('qr-link-banner').style.display = 'block';
                hideScannerSection();
                showStatus('info', 'Processing QR code link...');
                setTimeout(() => {
                    fetchSponsorData(parseInt(sponsorFromUrl));
                    // Hide the banner after processing
                    setTimeout(() => {
                        document.getElementById('qr-link-banner').style.display = 'none';
                    }, 2000);
                }, 500);
            } else {
                // Auto-start QR scanner by default
                showScannerSection();
                // Start scanner with a short delay to ensure DOM is ready
                window.setTimeout(() => {
                    console.log('Auto-starting QR scanner...');
                    try {
                        startQRScanner();
                    } catch (error) {
                        console.error('Failed to auto-start scanner:', error);
                        showStatus('error', 'Scanner failed to start automatically');
                    }
                }, 500);
            }
        } catch (error) {
            console.error('Error during initialization:', error);
            alert('JavaScript error during initialization: ' + error.message);
        }
    });

    function setupEventListeners() {
        console.log('Setting up event listeners...');
        
        const elements = {
            'start-scanner': startQRScanner,
            'stop-scanner': stopQRScanner,
            'take-photo': startPhotoCapture,
            'rescan-qr': restartScanning,
            'capture-photo': capturePhoto,
            'toggle-camera': toggleCamera,
            'stop-camera': stopCamera,
            'confirm-photo': showSubmitSection,
            'retake-photo': retakePhoto,
            'confirm-submit': submitVisit,
            'start-over': startOver,
            'log-another-visit': startOver
        };
        
        for (const [id, handler] of Object.entries(elements)) {
            const element = document.getElementById(id);
            if (element) {
                console.log(`✓ Found element: ${id}`);
                element.addEventListener('click', handler);
            } else {
                console.warn(`✗ Element not found: ${id}`);
            }
        }
        
        console.log('Event listener setup complete');
    }

    function startQRScanner() {
        console.log('Starting QR scanner...');
        updateScannerStatus('Starting camera...');
        
        // Clean up any existing scanner
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().catch(() => {});
        }
        
        // Clear and prepare the QR reader element
        const qrReader = document.getElementById('qr-reader');
        const placeholder = document.getElementById('scanner-placeholder');
        
        // Hide placeholder and mark as active
        if (placeholder) {
            placeholder.style.display = 'none';
        }
        qrReader.classList.add('active');
        
        try {
            html5QrcodeScanner = new Html5Qrcode("qr-reader");
            
            html5QrcodeScanner.start(
                { facingMode: "environment" },
                { 
                    fps: 10, 
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                },
                (decodedText) => {
                    console.log('QR Code scanned - RAW TEXT:', decodedText);
                    console.log('Type of decoded text:', typeof decodedText);
                    console.log('Length:', decodedText.length);
                    
                    const sponsorId = parseSponsorId(decodedText);
                    console.log('Parsed sponsor ID:', sponsorId);
                    
                    if (sponsorId) {
                        stopQRScanner();
                        hideScannerSection();
                        fetchSponsorData(sponsorId);
                    } else {
                        console.error('Failed to parse sponsor ID from:', decodedText);
                        showStatus('error', `Invalid QR code format: ${decodedText}`);
                    }
                },
                (errorMessage) => {
                    // Suppress frequent scan errors
                    if (!errorMessage.includes('No QR code found')) {
                        console.error('QR Scan error:', errorMessage);
                    }
                }
            ).then(() => {
                console.log('QR Scanner started successfully');
                updateScannerStatus('Scanning...');
            }).catch((error) => {
                console.error('QR Scanner error:', error);
                updateScannerStatus('Camera error');
                showStatus('error', 'Failed to start camera. Please check permissions.');
                resetScannerButtons();
            });
        } catch (error) {
            console.error('Failed to initialize QR scanner:', error);
            updateScannerStatus('Failed to start');
            showStatus('error', 'Could not start QR scanner: ' + error.message);
        }
    }

    function stopQRScanner() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().then(() => {
                updateScannerStatus('Stopped');
                resetScannerButtons();
            }).catch(() => {
                resetScannerButtons();
            });
        } else {
            resetScannerButtons();
        }
    }

    function resetScannerButtons() {
        // Reset scanner button visibility (hidden by default)
        document.getElementById('start-scanner').style.display = 'none';
        document.getElementById('stop-scanner').style.display = 'none';
        // Clear any scanner classes
        document.getElementById('qr-reader').classList.remove('scanning');
        document.getElementById('qr-reader').innerHTML = '';
    }

    function hideScannerSection() {
        document.getElementById('qr-scanner-section').style.display = 'none';
    }

    function showScannerSection() {
        document.getElementById('qr-scanner-section').style.display = 'block';
    }

    function onScanSuccess(decodedText) {
        console.log('QR Code scanned:', decodedText);
        const sponsorId = parseSponsorId(decodedText);
        
        if (sponsorId) {
            stopQRScanner();
            hideScannerSection();
            fetchSponsorData(sponsorId);
        } else {
            showStatus('error', 'Invalid QR code format');
        }
    }

    function onScanError(error) {
        // Suppress frequent scan errors
    }

    function updateScannerStatus(status) {
        document.getElementById('scanner-status').textContent = status;
    }

    function parseSponsorId(qrText) {
        // Handle direct URLs with sponsor parameter
        try {
            const url = new URL(qrText);
            const sponsorFromUrl = url.searchParams.get('sponsor');
            if (sponsorFromUrl) return parseInt(sponsorFromUrl);
        } catch (e) {
            // Not a valid URL, continue with other parsing
        }
        
        // Handle plain numbers
        if (/^\d+$/.test(qrText)) return parseInt(qrText);
        
        // Handle formatted strings
        const match = qrText.match(/sponsor[=_-](\d+)/i);
        return match ? parseInt(match[1]) : null;
    }

    function fetchSponsorData(sponsorId) {
        console.log('Fetching sponsor data for ID:', sponsorId);
        showStatus('info', 'Looking up sponsor...');
        
        fetch(`/api/sponsors/${sponsorId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            console.log('API Response status:', response.status);
            if (!response.ok) {
                const err = new Error(`HTTP ${response.status}`);
                // attach status for downstream handling
                err.status = response.status;
                throw err;
            }
            return response.json();
        })
        .then(data => {
            console.log('API Response data:', data);
            console.log('Sponsor location:', data.location);
            console.log('Is location empty?', !data.location);
            
            if (data.id) {
                currentSponsor = data;
                displaySponsorInfo(data);
                showSponsorSection();
                showStatus('success', 'Sponsor verified!');
            } else {
                showStatus('error', 'Sponsor not found');
                // Auto-resume scanning so the user can try another code
                setTimeout(() => {
                    restartScanning();
                }, 1000);
            }
        })
        .catch(error => {
            console.error('API Error:', error);
            const status = error?.status ?? 0;
            if (status === 401) {
                showStatus('error', 'Not signed in (401). Please log in again, then try scanning.');
            } else if (status === 404) {
                showStatus('error', 'Sponsor not found (404).');
            } else {
                const message = (error && error.message) ? error.message : 'Unknown error';
                showStatus('error', `Error fetching sponsor data: ${message}`);
            }
            // Ensure the scanner comes back automatically after an error
            setTimeout(() => {
                restartScanning();
            }, 1200);
        });
    }

    function displaySponsorInfo(sponsor) {
        console.log('Displaying sponsor info:', sponsor);
        console.log('Location value:', sponsor.location, 'Type:', typeof sponsor.location);
        
        const locationHtml = sponsor.location && sponsor.location.trim() 
            ? `<br><small class="text-gray-600"><strong>Location:</strong> ${sponsor.location}</small>` 
            : '<br><small class="text-gray-500"><em>No location specified</em></small>';
            
        document.getElementById('sponsor-details').innerHTML = `
            <div class="sponsor-info">
                <div class="sponsor-name text-lg font-semibold">${sponsor.name}</div>
                <div class="sponsor-details text-sm">
                    <strong>Company:</strong> ${sponsor.company_name}
                    ${locationHtml}
                    ${sponsor.contact ? `<br><small class="text-gray-600"><strong>Contact:</strong> ${sponsor.contact}</small>` : ''}
                </div>
            </div>
        `;
    }

    function showSponsorSection() {
        hideAllSections();
        document.getElementById('sponsor-section').style.display = 'block';
    }

    function startPhotoCapture() {
        hideAllSections();
        document.getElementById('photo-section').style.display = 'block';
        showStatus('info', 'Starting camera...');
        
        // Request camera access with highest quality
        navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: currentFacingMode,
                width: { ideal: 1920, min: 640 },
                height: { ideal: 1080, min: 480 },
                frameRate: { ideal: 30, min: 15 }
            },
            audio: false 
        })
        .then(stream => {
            cameraStream = stream;
            const video = document.getElementById('camera-video');
            video.srcObject = stream;
            
            // Wait for video to be ready
            video.onloadedmetadata = () => {
                console.log('Camera ready, video dimensions:', video.videoWidth, 'x', video.videoHeight);
                video.style.display = 'block';
                document.getElementById('camera-controls').style.display = 'block';
                document.getElementById('photo-preview').style.display = 'none';
                showStatus('success', 'Camera ready! Tap Capture to take photo.');
            };
            
            // Handle video errors
            video.onerror = (error) => {
                console.error('Video error:', error);
                showStatus('error', 'Video stream error');
            };
        })
        .catch(error => {
            console.error('Camera error:', error);
            showStatus('error', 'Failed to access camera: ' + error.message);
        });
    }

    function toggleCamera() {
        console.log('=== TOGGLE CAMERA ===');
        
        // Stop current camera stream
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
        }
        
        // Toggle facing mode
        currentFacingMode = currentFacingMode === "user" ? "environment" : "user";
        console.log('Switching to camera:', currentFacingMode);
        
        // Start camera with new facing mode and highest quality
        navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: currentFacingMode,
                width: { ideal: 1920, min: 640 },
                height: { ideal: 1080, min: 480 },
                frameRate: { ideal: 30, min: 15 }
            },
            audio: false 
        })
        .then(stream => {
            cameraStream = stream;
            const video = document.getElementById('camera-video');
            video.srcObject = stream;
            
            // Wait for video to be ready
            video.onloadedmetadata = () => {
                console.log('New camera stream ready:', {
                    facingMode: currentFacingMode,
                    videoWidth: video.videoWidth,
                    videoHeight: video.videoHeight
                });
                showStatus('success', `Switched to ${currentFacingMode === "user" ? "front" : "back"} camera`);
            };
        })
        .catch(error => {
            console.error('Camera toggle error:', error);
            showStatus('error', 'Failed to switch camera: ' + error.message);
            
            // Revert to previous facing mode
            currentFacingMode = currentFacingMode === "user" ? "environment" : "user";
        });
    }

    function capturePhoto() {
        console.log('=== CAPTURE PHOTO START ===');
        
        const video = document.getElementById('camera-video');
        const canvas = document.getElementById('photo-canvas');
        
        if (!video) {
            console.error('Video element not found');
            showStatus('error', 'Video element not found');
            return;
        }
        
        if (!canvas) {
            console.error('Canvas element not found');
            showStatus('error', 'Canvas element not found');
            return;
        }
        
        const ctx = canvas.getContext('2d');
        
        console.log('Video state:', {
            videoWidth: video.videoWidth,
            videoHeight: video.videoHeight,
            readyState: video.readyState,
            currentSrc: video.currentSrc ? 'has source' : 'no source'
        });
        
        // Check if video is ready
        if (video.videoWidth === 0 || video.videoHeight === 0) {
            console.error('Video not ready - dimensions are 0');
            showStatus('error', 'Camera not ready. Please try again.');
            return;
        }
        
        // Set canvas size to match video
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        console.log('Canvas size set to:', canvas.width, 'x', canvas.height);
        
        // Make canvas temporarily visible for capture
        canvas.style.display = 'block';
        
        try {
            // Draw video frame to canvas
            ctx.drawImage(video, 0, 0);
            console.log('Video frame drawn to canvas');
            
            // Convert canvas to blob
            canvas.toBlob(blob => {
                console.log('Canvas.toBlob callback triggered');
                
                if (!blob) {
                    console.error('Failed to create blob from canvas');
                    showStatus('error', 'Failed to capture photo. Please try again.');
                    return;
                }
                
                console.log('Photo blob created:', {
                    size: blob.size,
                    type: blob.type
                });
                
                capturedPhotoBlob = blob;
                
                // Show preview
                const previewImg = document.getElementById('preview-image');
                if (!previewImg) {
                    console.error('Preview image element not found');
                    showStatus('error', 'Preview element not found');
                    return;
                }
                
                const imageUrl = URL.createObjectURL(blob);
                console.log('Object URL created:', imageUrl);
                
                previewImg.onload = () => {
                    console.log('Preview image loaded successfully');
                    showStatus('success', 'Photo captured successfully!');
                };
                
                previewImg.onerror = (error) => {
                    console.error('Preview image load error:', error);
                    showStatus('error', 'Failed to display photo preview');
                };
                
                previewImg.src = imageUrl;
                console.log('Preview image src set');
                
                // Hide camera and canvas, show preview
                video.style.display = 'none';
                canvas.style.display = 'none';
                
                const cameraControls = document.getElementById('camera-controls');
                const photoPreview = document.getElementById('photo-preview');
                
                if (cameraControls) cameraControls.style.display = 'none';
                if (photoPreview) {
                    photoPreview.style.display = 'block';
                    console.log('Photo preview shown');
                }
                
                // Stop camera stream
                stopCamera();
                
            }, 'image/jpeg', 1.0); // Maximum quality (100%)
            
        } catch (error) {
            console.error('Error during photo capture:', error);
            showStatus('error', 'Photo capture failed: ' + error.message);
        }
        
        console.log('=== CAPTURE PHOTO END ===');
    }

    function stopCamera() {
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }
    }

    function retakePhoto() {
        capturedPhotoBlob = null;
        document.getElementById('photo-preview').style.display = 'none';
        startPhotoCapture(); // Restart camera
    }

    function showSubmitSection() {
        if (!capturedPhotoBlob) {
            showStatus('error', 'Please take a photo first');
            return;
        }
        
        hideAllSections();
        document.getElementById('submit-section').style.display = 'block';
        
        // Update button text based on Google reviews link
        const submitButtonText = document.getElementById('submit-button-text');
        if (currentSponsor && currentSponsor.google_reviews_link) {
            submitButtonText.textContent = 'Submit & Review on Google';
        } else {
            submitButtonText.textContent = 'Submit Visit';
        }
        
        displayVisitSummary();
    }

    function displayVisitSummary() {
        const now = new Date();
        document.getElementById('visit-summary').innerHTML = `
            <h4 class="font-semibold mb-3">Visit Summary</h4>
            <div class="space-y-2">
                <div><strong>Sponsor:</strong> ${currentSponsor.name}</div>
                <div><strong>Company:</strong> ${currentSponsor.company_name}</div>
                <div><strong>Location:</strong> ${currentSponsor.location || 'N/A'}</div>
                <div><strong>Date:</strong> ${now.toLocaleDateString()}</div>
                <div><strong>Time:</strong> ${now.toLocaleTimeString()}</div>
                <div class="flex items-center"><strong>Photo:</strong> <span class="ml-2 text-green-600">Ready</span></div>
            </div>
        `;
    }

    function submitVisit() {
        if (!currentSponsor || !capturedPhotoBlob) {
            showStatus('error', 'Missing required data');
            return;
        }
        
        // Debug authentication status
        console.log('=== SUBMITTING VISIT ===');
        console.log('Current user info from dashboard:', {
            name: '{{ auth()->user()->name }}',
            id: '{{ auth()->user()->id }}',
            role: '{{ auth()->user()->role }}'
        });
        console.log('CSRF token:', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Get notes if provided
        const notes = document.getElementById('visit-notes').value.trim();
        console.log('Visit notes:', notes || 'No notes provided');
        
        // Show progress
        document.getElementById('submit-progress').style.display = 'block';
        
        const formData = new FormData();
        formData.append('sponsor_id', currentSponsor.id);
        formData.append('site_photo', capturedPhotoBlob, 'site-photo.jpg');
        formData.append('visited_at', new Date().toISOString());
        if (notes) {
            formData.append('notes', notes);
        }
        
        fetch('/api/visits', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Submit response status:', response.status);
            if (!response.ok) {
                // Check for authentication error specifically
                if (response.status === 401) {
                    console.error('Authentication failed - user may need to log in again');
                    throw new Error('Authentication failed. Please log out and log back in.');
                }
                
                // Try to get the error response body for 422 errors
                return response.json().then(errorData => {
                    console.log('Validation errors:', errorData);
                    throw new Error(`HTTP ${response.status}: ${JSON.stringify(errorData)}`);
                }).catch(() => {
                    throw new Error(`HTTP ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Submit response:', data);
            document.getElementById('submit-progress').style.display = 'none';
            
            if (data.success) {
                showSuccessSection();
                hideStatus();
                
                // Redirect to Google reviews if available
                if (currentSponsor && currentSponsor.google_reviews_link) {
                    setTimeout(() => {
                        if (confirm('Visit submitted successfully! Would you like to leave a Google review for this sponsor?')) {
                            window.open(currentSponsor.google_reviews_link, '_blank');
                        }
                    }, 1000); // Delay to show success message first
                }
            } else {
                showStatus('error', data.message || 'Submission failed');
            }
        })
        .catch(error => {
            console.error('Submit error:', error);
            document.getElementById('submit-progress').style.display = 'none';
            showStatus('error', `Submission failed: ${error.message}`);
        });
    }

    function showSuccessSection() {
        hideAllSections();
        document.getElementById('success-section').style.display = 'block';
    }

    function restartScanning() {
        // Stop current scanner if running
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().catch(() => {});
        }
        
        hideAllSections();
        showScannerSection();
        clearStatus();
        currentSponsor = null;
        updateScannerStatus('Ready');
        
        setTimeout(() => {
            startQRScanner();
        }, 300);
    }

    function startOver() {
        console.log('Starting over...');
        
        // Reset all state
        currentSponsor = null;
        capturedPhotoBlob = null;
        
        // Stop camera if running
        stopCamera();
        
        // Stop QR scanner if running
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().then(() => {
                // Clear the QR scanner element
                const qrReader = document.getElementById('qr-reader');
                if (qrReader) {
                    qrReader.innerHTML = '';
                }
                
                // Show scanner section and start new scan
                hideAllSections();
                showScannerSection();
                setTimeout(() => {
                    startQRScanner();
                }, 500);
            }).catch(() => {
                console.error('Error stopping QR scanner');
                // Try to continue anyway
                hideAllSections();
                showScannerSection();
                setTimeout(() => {
                    startQRScanner();
                }, 500);
            });
        } else {
            // No active scanner, just start fresh
            hideAllSections();
            showScannerSection();
            setTimeout(() => {
                startQRScanner();
            }, 500);
        }
        
        // Reset UI state
        hideStatus();
        updateScannerStatus('Ready');
        resetScannerButtons();
        
        // Clear photo preview
        const previewImage = document.getElementById('preview-image');
        if (previewImage) {
            previewImage.src = '';
        }
        
        // Auto-restart QR scanner after a short delay
        setTimeout(() => {
            startQRScanner();
        }, 500);
    }

    function hideAllSections() {
        const sections = ['sponsor-section', 'photo-section', 'submit-section', 'success-section'];
        sections.forEach(id => {
            document.getElementById(id).style.display = 'none';
        });
    }

    function showStatus(type, message) {
        const colors = {
            success: 'bg-green-50 text-green-800 border-green-200',
            error: 'bg-red-50 text-red-800 border-red-200',
            info: 'bg-blue-50 text-blue-800 border-blue-200'
        };
        
        document.getElementById('status-container').innerHTML = `
            <div class="p-3 rounded-lg border text-center ${colors[type]}">${message}</div>
        `;
        
        if (type === 'success' || type === 'info') {
            setTimeout(hideStatus, 3000);
        }
    }

    function hideStatus() {
        document.getElementById('status-container').innerHTML = '';
    }

    function clearStatus() {
        document.getElementById('status-container').innerHTML = '';
    }
    </script>
    @endpush
</x-filament-panels::page>
