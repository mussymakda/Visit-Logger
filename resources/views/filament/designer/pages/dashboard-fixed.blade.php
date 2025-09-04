<x-filament-panels::page>
    <div class="max-w-md mx-auto space-y-6">
        
        <!-- QR Scanner Section -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center justify-between">
                    <span>QR Scanner</span>
                    <span id="scanner-status" class="text-sm text-gray-500">Ready</span>
                </div>
            </x-slot>
            
            <div class="space-y-4">
                <!-- QR Reader Display -->
                <div class="flex justify-center">
                    <div id="qr-reader" class="w-full max-w-sm h-64 border-2 border-gray-200 rounded-lg overflow-hidden"></div>
                </div>
                
                <!-- Scanner Controls -->
                <div class="flex justify-center space-x-3">
                    <x-filament::button 
                        id="stop-scanner" 
                        color="danger" 
                        size="sm"
                        style="display: none;"
                    >
                        Stop Scanner
                    </x-filament::button>
                    
                    <x-filament::button 
                        id="test-sponsor-3" 
                        color="warning" 
                        size="sm"
                    >
                        Test ID: 3
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
                            📷 Take Photo
                        </x-filament::button>
                        <x-filament::button id="rescan-qr" color="gray">
                            🔄 Scan Again
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
                                ✓ Use Photo
                            </x-filament::button>
                            <x-filament::button id="retake-photo" color="gray">
                                🔄 Retake
                            </x-filament::button>
                        </div>
                    </div>
                    
                    <!-- Camera Controls -->
                    <div id="camera-controls" class="flex justify-center space-x-3">
                        <x-filament::button id="capture-photo" color="primary">
                            📸 Capture
                        </x-filament::button>
                        <x-filament::button id="stop-camera" color="gray">
                            Stop Camera
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
                    
                    <!-- Submit Controls -->
                    <div class="flex justify-center space-x-3">
                        <x-filament::button id="confirm-submit" color="success">
                            ✓ Submit Visit
                        </x-filament::button>
                        <x-filament::button id="start-over" color="gray">
                            🔄 Start Over
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
                    <x-filament::button id="log-another-visit" color="success">
                        📝 Log Another Visit
                    </x-filament::button>
                </div>
            </x-filament::section>
        </div>
    </div>

    @push('scripts')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    
    <script>
    let html5QrcodeScanner;
    let cameraStream;
    let currentSponsor = null;
    let capturedPhotoBlob = null;

    document.addEventListener('DOMContentLoaded', function() {
        setupEventListeners();
        // Auto-start QR scanner
        setTimeout(startQRScanner, 1000);
    });

    function setupEventListeners() {
        document.getElementById('stop-scanner').addEventListener('click', stopQRScanner);
        document.getElementById('test-sponsor-3').addEventListener('click', () => fetchSponsorData(3));
        document.getElementById('take-photo').addEventListener('click', startPhotoCapture);
        document.getElementById('rescan-qr').addEventListener('click', restartScanning);
        document.getElementById('capture-photo').addEventListener('click', capturePhoto);
        document.getElementById('stop-camera').addEventListener('click', stopCamera);
        document.getElementById('confirm-photo').addEventListener('click', showSubmitSection);
        document.getElementById('retake-photo').addEventListener('click', retakePhoto);
        document.getElementById('confirm-submit').addEventListener('click', submitVisit);
        document.getElementById('start-over').addEventListener('click', startOver);
        document.getElementById('log-another-visit').addEventListener('click', startOver);
    }

    function startQRScanner() {
        updateScannerStatus('Starting camera...');
        
        html5QrcodeScanner = new Html5Qrcode("qr-reader");
        
        html5QrcodeScanner.start(
            { facingMode: "environment" },
            { 
                fps: 10, 
                qrbox: { width: 200, height: 200 },
                aspectRatio: 1.0
            },
            onScanSuccess,
            onScanError
        ).then(() => {
            updateScannerStatus('Scanning...');
            document.getElementById('stop-scanner').style.display = 'inline-block';
        }).catch((error) => {
            console.error('QR Scanner error:', error);
            updateScannerStatus('Camera error');
            showStatus('error', 'Failed to start camera. Please check permissions.');
        });
    }

    function stopQRScanner() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().then(() => {
                updateScannerStatus('Stopped');
                document.getElementById('stop-scanner').style.display = 'none';
            });
        }
    }

    function onScanSuccess(decodedText) {
        console.log('QR Code scanned:', decodedText);
        const sponsorId = parseSponsorId(decodedText);
        
        if (sponsorId) {
            stopQRScanner();
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
            <div class="bg-blue-50 rounded-lg p-4 space-y-2">
                <h4 class="font-semibold text-blue-900">${sponsor.name}</h4>
                <p class="text-blue-700">${sponsor.company_name}</p>
                <div class="text-sm text-blue-600 space-y-1">
                    <div><strong>Contact:</strong> ${sponsor.contact || 'N/A'}</div>
                    <div><strong>Location:</strong> ${sponsor.location || 'N/A'}</div>
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
        
        // Request camera access
        navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: "environment" },
            audio: false 
        })
        .then(stream => {
            cameraStream = stream;
            const video = document.getElementById('camera-video');
            video.srcObject = stream;
            video.style.display = 'block';
            document.getElementById('camera-controls').style.display = 'block';
            document.getElementById('photo-preview').style.display = 'none';
        })
        .catch(error => {
            console.error('Camera error:', error);
            showStatus('error', 'Failed to access camera');
        });
    }

    function capturePhoto() {
        const video = document.getElementById('camera-video');
        const canvas = document.getElementById('photo-canvas');
        const ctx = canvas.getContext('2d');
        
        // Set canvas size to match video
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        // Draw video frame to canvas
        ctx.drawImage(video, 0, 0);
        
        // Convert canvas to blob
        canvas.toBlob(blob => {
            capturedPhotoBlob = blob;
            
            // Show preview
            const previewImg = document.getElementById('preview-image');
            previewImg.src = URL.createObjectURL(blob);
            
            // Hide camera, show preview
            video.style.display = 'none';
            document.getElementById('camera-controls').style.display = 'none';
            document.getElementById('photo-preview').style.display = 'block';
            
            // Stop camera stream
            stopCamera();
        }, 'image/jpeg', 0.8);
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
                <div class="flex items-center"><strong>Photo:</strong> <span class="ml-2 text-green-600">✓ Ready</span></div>
            </div>
        `;
    }

    function submitVisit() {
        if (!currentSponsor || !capturedPhotoBlob) {
            showStatus('error', 'Missing required data');
            return;
        }
        
        // Show progress
        document.getElementById('submit-progress').style.display = 'block';
        
        const formData = new FormData();
        formData.append('sponsor_id', currentSponsor.id);
        formData.append('site_photo', capturedPhotoBlob, 'site-photo.jpg');
        formData.append('visited_at', new Date().toISOString());
        
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
                throw new Error(`HTTP ${response.status}`);
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
        startOver();
        setTimeout(startQRScanner, 500);
    }

    function startOver() {
        // Reset all state
        currentSponsor = null;
        capturedPhotoBlob = null;
        
        // Stop camera if running
        stopCamera();
        
        // Stop QR scanner if running
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().catch(() => {});
        }
        
        // Reset UI
        hideAllSections();
        hideStatus();
        updateScannerStatus('Ready');
        document.getElementById('qr-reader').innerHTML = '';
        document.getElementById('stop-scanner').style.display = 'none';
        
        // Clear photo preview
        document.getElementById('preview-image').src = '';
        
        // Restart QR scanner
        setTimeout(startQRScanner, 1000);
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
    </script>
    @endpush
</x-filament-panels::page>
