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
                <div class="flex justify-center">
                    <div id="qr-reader" class="w-full max-w-lg h-80 border-2 border-gray-200 rounded-lg overflow-hidden"></div>
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
                        
                    </div>
                    
                    <!-- Alternative File Upload -->
                    <div class="text-center">
                        <p class="text-xs text-gray-500 mb-2">Or choose from gallery:</p>
                        <input type="file" id="file-photo" accept="image/*" capture="environment" style="display: none;">
                        <x-filament::button 
                            onclick="document.getElementById('file-photo').click()" 
                            color="gray" 
                            size="sm"
                        >
                            Choose File
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
                            Submit Visit
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
            'stop-camera': stopCamera,
            'file-photo': handleFileSelection,
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
                if (id === 'file-photo') {
                    element.addEventListener('change', handler);
                } else {
                    element.addEventListener('click', handler);
                }
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
        qrReader.innerHTML = '';
        qrReader.classList.remove('empty');
        qrReader.classList.add('scanning');
        
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
                    console.log('QR Code scanned:', decodedText);
                    const sponsorId = parseSponsorId(decodedText);
                    
                    if (sponsorId) {
                        stopQRScanner();
                        hideScannerSection();
                        fetchSponsorData(sponsorId);
                    } else {
                        showStatus('error', 'Invalid QR code format');
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
        showStatus('info', 'Looking up sponsor...');
        
        fetch(`/api/sponsors/${sponsorId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.id) {
                currentSponsor = data;
                displaySponsorInfo(data);
                showSponsorSection();
                showStatus('success', 'Sponsor verified!');
            } else {
                showStatus('error', 'Sponsor not found');
            }
        })
        .catch(error => {
            console.error('API Error:', error);
            showStatus('error', 'Error fetching sponsor data');
        });
    }

    function displaySponsorInfo(sponsor) {
        document.getElementById('sponsor-details').innerHTML = `
            <div class="sponsor-info">
                <div class="sponsor-name">${sponsor.name}</div>
                <div class="sponsor-details">
                    ${sponsor.company_name}
                    ${sponsor.location ? `<br><small class="text-gray-600">${sponsor.location}</small>` : ''}
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
        
        // Request camera access
        navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: "environment",
                width: { ideal: 640 },
                height: { ideal: 480 }
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
                
            }, 'image/jpeg', 0.9);
            
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

    function handleFileSelection(event) {
        const file = event.target.files[0];
        console.log('File selection event triggered');
        console.log('Files array:', event.target.files);
        
        if (!file) {
            console.log('No file selected');
            showStatus('error', 'No file selected');
            return;
        }
        
        console.log('File selected:', {
            name: file.name,
            size: file.size,
            type: file.type,
            lastModified: file.lastModified
        });
        
        // Check file type
        if (!file.type.startsWith('image/')) {
            console.error('Invalid file type:', file.type);
            showStatus('error', 'Please select an image file');
            return;
        }
        
        // Check file size (max 10MB)
        if (file.size > 10 * 1024 * 1024) {
            console.error('File too large:', file.size);
            showStatus('error', 'File too large. Please choose a file under 10MB.');
            return;
        }
        
        console.log('File validation passed, creating preview...');
        
        // Set the blob for submission
        capturedPhotoBlob = file;
        
        // Show preview
        const reader = new FileReader();
        reader.onload = (e) => {
            console.log('FileReader onload triggered');
            console.log('Data URL length:', e.target.result.length);
            
            const previewImg = document.getElementById('preview-image');
            if (!previewImg) {
                console.error('Preview image element not found!');
                showStatus('error', 'Preview element not found');
                return;
            }
            
            console.log('Setting preview image src...');
            previewImg.src = e.target.result;
            
            // Add onload handler to image
            previewImg.onload = () => {
                console.log('Image loaded successfully');
                showStatus('success', 'Photo selected successfully!');
            };
            
            previewImg.onerror = (error) => {
                console.error('Image load error:', error);
                showStatus('error', 'Failed to load image preview');
            };
            
            // Hide camera, show preview
            const cameraVideo = document.getElementById('camera-video');
            const cameraControls = document.getElementById('camera-controls');
            const photoPreview = document.getElementById('photo-preview');
            
            console.log('Hiding camera elements...');
            if (cameraVideo) cameraVideo.style.display = 'none';
            if (cameraControls) cameraControls.style.display = 'none';
            
            console.log('Showing photo preview...');
            if (photoPreview) {
                photoPreview.style.display = 'block';
                console.log('Photo preview display set to block');
            } else {
                console.error('Photo preview element not found!');
            }
            
            // Stop camera if running
            stopCamera();
        };
        
        reader.onerror = (error) => {
            console.error('FileReader error:', error);
            showStatus('error', 'Failed to read file');
        };
        
        console.log('Starting FileReader...');
        reader.readAsDataURL(file);
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
