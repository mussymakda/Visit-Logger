# Visit Logger - Designer API Documentation

Complete API documentation for the Flutter mobile app to integrate with the Laravel backend.

## 📋 Table of Contents

1. [Authentication](#authentication)
2. [API Endpoints](#api-endpoints)
3. [Request/Response Examples](#request-response-examples)
4. [Error Handling](#error-handling)
5. [Flutter Integration Guide](#flutter-integration-guide)
6. [Testing](#testing)

---

## 🔐 Authentication

The API uses Laravel Sanctum for token-based authentication. All protected endpoints require the `Authorization: Bearer {token}` header.

### Base URL

**For Android Emulator (Recommended):**
```
http://10.0.2.2:8000/api/designer
```

**For iOS Simulator / Physical Device on same network:**
```
http://YOUR_PC_IP:8000/api/designer
```

**For Local Browser Testing:**
```
http://127.0.0.1:8000/api/designer
```

### Network Configuration Notes
- **Android Emulator**: Use `10.0.2.2` - this is the emulator's special IP for the host machine
- **iOS Simulator**: Use your PC's actual IP address (find with `ipconfig`)
- **Physical Device**: Ensure device and PC are on same WiFi network
- **Laravel Server**: Must run with `--host=0.0.0.0` to accept external connections

### Authentication Flow
1. **Login**: POST `/login` with email/password
2. **Get Token**: Receive API token in response
3. **Use Token**: Include in Authorization header for all requests
4. **Logout**: POST `/logout` to revoke token

### Test Credentials (Local Development)
- **Email**: `test@example.com`
- **Password**: `password`

---

## 📡 API Endpoints

### Authentication Endpoints

#### POST `/login`
Authenticate interior designer and receive API token.

**Request Body:**
```json
{
    "email": "designer@example.com",
    "password": "password123"
}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Designer",
            "email": "designer@example.com",
            "role": "interior_designer"
        },
        "token": "1|abcdef123456..."
    }
}
```

**Error Response (401):**
```json
{
    "success": false,
    "message": "Invalid credentials"
}
```

---

#### GET `/profile`
Get authenticated user profile information.

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Designer",
            "email": "designer@example.com",
            "role": "interior_designer",
            "created_at": "2025-09-26T10:00:00.000000Z"
        }
    }
}
```

---

#### POST `/logout`
Revoke the current API token and logout.

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

---

### QR Code & Sponsor Endpoints

#### POST `/verify-qr`
Verify QR code and get sponsor information.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "qr_data": "SPONSOR-123"
}
```

**Supported QR Formats:**
- `SPONSOR-123`
- `sponsor=123`
- `123` (plain number)
- URLs containing sponsor ID

**Success Response (200):**
```json
{
    "success": true,
    "message": "QR code verified successfully",
    "data": {
        "sponsor": {
            "id": 123,
            "name": "IKEA Showroom",
            "company_name": "IKEA Ltd",
            "contact": "+1-555-0123",
            "location": "123 Furniture St, Design City",
            "description": "Modern furniture and home solutions",
            "google_reviews_link": "https://g.page/ikea-showroom",
            "qr_code": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA..."
        }
    }
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Sponsor not found"
}
```

---

### Visit Management Endpoints

#### POST `/visits`
Submit a new visit log with photo.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body (Form Data):**
```
sponsor_id: 123
notes: Kitchen consultation completed. Discussed cabinet options and measurements.
photo: [image file - JPEG/PNG, max 10MB]
visit_date: 2025-09-26T14:30:00Z (optional, defaults to now)
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Visit logged successfully",
    "data": {
        "visit": {
            "id": 456,
            "sponsor_name": "IKEA Showroom",
            "notes": "Kitchen consultation completed...",
            "photo_url": "https://your-domain.com/storage/visit-photos/1727357800_1_kitchen.jpg",
            "visit_date": "2025-09-26T14:30:00.000000Z",
            "created_at": "2025-09-26T14:35:00.000000Z"
        }
    }
}
```

**Validation Errors (422):**
```json
{
    "success": false,
    "message": "Validation error",
    "errors": {
        "sponsor_id": ["The sponsor id field is required."],
        "photo": ["The photo field is required."]
    }
}
```

---

#### GET `/visits`
Get paginated visit history for authenticated designer.

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `limit` (optional): Items per page (default: 20)

**Example Request:**
```
GET /visits?page=1&limit=10
```

**Success Response (200):**
```json
{
    "success": true,
    "data": {
        "visits": [
            {
                "id": 456,
                "sponsor": {
                    "id": 123,
                    "name": "IKEA Showroom",
                    "company_name": "IKEA Ltd",
                    "location": "123 Furniture St, Design City"
                },
                "notes": "Kitchen consultation completed...",
                "photo_url": "https://your-domain.com/storage/visit-photos/1727357800_1_kitchen.jpg",
                "visit_date": "2025-09-26T14:30:00.000000Z",
                "created_at": "2025-09-26T14:35:00.000000Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 5,
            "per_page": 20,
            "total": 95,
            "has_more_pages": true
        }
    }
}
```

---

#### GET `/visits/search`
Search visits by sponsor name or notes.

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `query` (required): Search term (minimum 2 characters)

**Example Request:**
```
GET /visits/search?query=IKEA
```

**Success Response (200):**
```json
{
    "success": true,
    "data": {
        "visits": [
            {
                "id": 456,
                "sponsor": {
                    "id": 123,
                    "name": "IKEA Showroom",
                    "company_name": "IKEA Ltd",
                    "location": "123 Furniture St, Design City"
                },
                "notes": "Kitchen consultation completed...",
                "photo_url": "https://your-domain.com/storage/visit-photos/1727357800_1_kitchen.jpg",
                "visit_date": "2025-09-26T14:30:00.000000Z",
                "created_at": "2025-09-26T14:35:00.000000Z"
            }
        ],
        "query": "IKEA",
        "total_results": 12
    }
}
```

---

#### GET `/visits/{visitId}`
Get detailed information about a specific visit.

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "success": true,
    "data": {
        "visit": {
            "id": 456,
            "sponsor": {
                "id": 123,
                "name": "IKEA Showroom",
                "company_name": "IKEA Ltd",
                "contact": "+1-555-0123",
                "location": "123 Furniture St, Design City",
                "description": "Modern furniture and home solutions",
                "google_reviews_link": "https://g.page/ikea-showroom"
            },
            "notes": "Kitchen consultation completed. Discussed cabinet options and measurements. Customer interested in white oak finish.",
            "photo_url": "https://your-domain.com/storage/visit-photos/1727357800_1_kitchen.jpg",
            "visit_date": "2025-09-26T14:30:00.000000Z",
            "created_at": "2025-09-26T14:35:00.000000Z",
            "updated_at": "2025-09-26T14:35:00.000000Z"
        }
    }
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Visit not found"
}
```

---

### Statistics & Analytics Endpoints

#### GET `/stats`
Get visit statistics for the authenticated designer.

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "success": true,
    "data": {
        "stats": {
            "visits_today": 3,
            "visits_this_week": 12,
            "visits_this_month": 45,
            "total_visits": 156,
            "unique_sponsors": 23
        },
        "recent_visits": [
            {
                "id": 456,
                "sponsor_name": "IKEA Showroom",
                "visit_date": "2025-09-26T14:30:00.000000Z",
                "notes": "Kitchen consultation completed. Discussed cabinet options and measurements..."
            }
        ]
    }
}
```

---

## ⚠️ Error Handling

All API responses follow a consistent format:

### Success Response Structure
```json
{
    "success": true,
    "message": "Operation completed successfully",
    "data": { /* response data */ }
}
```

### Error Response Structure
```json
{
    "success": false,
    "message": "Error description",
    "errors": { /* validation errors if applicable */ }
}
```

### HTTP Status Codes
- **200**: Success
- **201**: Created
- **400**: Bad Request
- **401**: Unauthorized (invalid/missing token)
- **403**: Forbidden (insufficient permissions)
- **404**: Not Found
- **422**: Validation Error
- **500**: Internal Server Error

---

## 📱 Flutter Integration Guide

### Required Dependencies

Add these to your `pubspec.yaml`:

```yaml
dependencies:
  # HTTP client
  http: ^1.1.0
  
  # Photo capture and handling
  camera: ^0.10.5
  image_picker: ^1.0.4
  path: ^1.8.3
  path_provider: ^2.1.1
  
  # MIME type handling
  mime: ^1.0.4
  
  # Optional: Image compression
  image: ^4.1.3
  flutter_image_compress: ^2.1.0

dev_dependencies:
  # Testing
  mockito: ^5.4.2
  http_mock_adapter: ^0.6.1
```

### Required Permissions

#### Android (`android/app/src/main/AndroidManifest.xml`)
```xml
<uses-permission android:name="android.permission.CAMERA" />
<uses-permission android:name="android.permission.WRITE_EXTERNAL_STORAGE" />
<uses-permission android:name="android.permission.READ_EXTERNAL_STORAGE" />
```

#### iOS (`ios/Runner/Info.plist`)
```xml
<key>NSCameraUsageDescription</key>
<string>This app needs camera access to capture visit photos</string>
<key>NSPhotoLibraryUsageDescription</key>
<string>This app needs photo library access to select visit photos</string>
```

### 1. HTTP Client Setup

```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

class ApiService {
  // Android Emulator - Use this for testing
  static const String baseUrl = 'http://10.0.2.2:8000/api/designer';
  
  // Alternative for iOS Simulator or Physical Device
  // static const String baseUrl = 'http://YOUR_PC_IP:8000/api/designer';
  
  String? _token;

  void setToken(String token) {
    _token = token;
  }

  Map<String, String> get _headers => {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    if (_token != null) 'Authorization': 'Bearer $_token',
  };
}
```

### 2. Authentication

```dart
class AuthService extends ApiService {
  Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login'),
      headers: {'Content-Type': 'application/json'},
      body: json.encode({
        'email': email,
        'password': password,
      }),
    );

    final data = json.decode(response.body);
    
    if (response.statusCode == 200 && data['success']) {
      setToken(data['data']['token']);
      return data['data'];
    } else {
      throw Exception(data['message']);
    }
  }

  Future<void> logout() async {
    await http.post(
      Uri.parse('$baseUrl/logout'),
      headers: _headers,
    );
    _token = null;
  }
}
```

### 3. QR Code Verification

```dart
Future<Map<String, dynamic>> verifyQR(String qrData) async {
  final response = await http.post(
    Uri.parse('$baseUrl/verify-qr'),
    headers: _headers,
    body: json.encode({'qr_data': qrData}),
  );

  final data = json.decode(response.body);
  
  if (response.statusCode == 200 && data['success']) {
    return data['data']['sponsor'];
  } else {
    throw Exception(data['message']);
  }
}
```

### 4. Submit Visit with Photo

There are multiple ways to handle photo upload in Flutter:

#### Option A: Using Camera Package (Recommended)
```dart
import 'dart:io';
import 'package:camera/camera.dart';
import 'package:path/path.dart' as path;
import 'package:path_provider/path_provider.dart';

class PhotoService {
  static Future<File> capturePhoto() async {
    final cameras = await availableCameras();
    final controller = CameraController(cameras.first, ResolutionPreset.high);
    await controller.initialize();
    
    // Capture photo
    final image = await controller.takePicture();
    await controller.dispose();
    
    return File(image.path);
  }
}

// Submit visit with captured photo
Future<Map<String, dynamic>> submitVisit({
  required int sponsorId,
  required String notes,
  required File photo,
  DateTime? visitDate,
}) async {
  var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/visits'));
  
  // Add headers
  request.headers.addAll({
    'Authorization': 'Bearer $_token',
    'Accept': 'application/json',
  });
  
  // Add form fields
  request.fields['sponsor_id'] = sponsorId.toString();
  request.fields['notes'] = notes;
  if (visitDate != null) {
    request.fields['visit_date'] = visitDate.toIso8601String();
  }
  
  // Add photo file with proper mime type
  request.files.add(
    await http.MultipartFile.fromPath(
      'photo', 
      photo.path,
      contentType: MediaType('image', 'jpeg'), // Important!
    ),
  );

  final streamedResponse = await request.send();
  final response = await http.Response.fromStream(streamedResponse);
  final data = json.decode(response.body);

  if (response.statusCode == 200 && data['success']) {
    return data['data']['visit'];
  } else {
    throw Exception(data['message']);
  }
}
```

#### Option B: Using Image Picker Package
```dart
import 'package:image_picker/image_picker.dart';

class PhotoPickerService {
  static final ImagePicker _picker = ImagePicker();
  
  static Future<File?> pickFromCamera() async {
    final XFile? photo = await _picker.pickImage(
      source: ImageSource.camera,
      imageQuality: 85, // Compress to reduce file size
      maxWidth: 1920,   // Limit dimensions
      maxHeight: 1080,
    );
    
    return photo != null ? File(photo.path) : null;
  }
  
  static Future<File?> pickFromGallery() async {
    final XFile? photo = await _picker.pickImage(
      source: ImageSource.gallery,
      imageQuality: 85,
      maxWidth: 1920,
      maxHeight: 1080,
    );
    
    return photo != null ? File(photo.path) : null;
  }
}
```

#### Option C: Using Bytes (for in-memory images)
```dart
import 'dart:typed_data';

Future<Map<String, dynamic>> submitVisitWithBytes({
  required int sponsorId,
  required String notes,
  required Uint8List photoBytes,
  required String fileName,
  DateTime? visitDate,
}) async {
  var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/visits'));
  
  request.headers.addAll({
    'Authorization': 'Bearer $_token',
    'Accept': 'application/json',
  });
  
  request.fields['sponsor_id'] = sponsorId.toString();
  request.fields['notes'] = notes;
  if (visitDate != null) {
    request.fields['visit_date'] = visitDate.toIso8601String();
  }
  
  // Add photo from bytes
  request.files.add(
    http.MultipartFile.fromBytes(
      'photo',
      photoBytes,
      filename: fileName,
      contentType: MediaType('image', 'jpeg'),
    ),
  );

  final streamedResponse = await request.send();
  final response = await http.Response.fromStream(streamedResponse);
  final data = json.decode(response.body);

  if (response.statusCode == 200 && data['success']) {
    return data['data']['visit'];
  } else {
    throw Exception(data['message']);
  }
}
```

#### Complete Flutter Implementation Example
```dart
import 'dart:io';
import 'dart:typed_data';
import 'package:flutter_image_compress/flutter_image_compress.dart';
import 'package:path/path.dart' as path;

class VisitService extends ApiService {
  // Compress image before upload to ensure it's under 10MB
  static Future<File?> compressImage(File file) async {
    final String targetPath = path.join(
      path.dirname(file.path),
      '${path.basenameWithoutExtension(file.path)}_compressed.jpg',
    );
    
    final XFile? compressedFile = await FlutterImageCompress.compressAndGetFile(
      file.absolute.path,
      targetPath,
      quality: 85,
      minWidth: 1920,
      minHeight: 1080,
    );
    
    return compressedFile != null ? File(compressedFile.path) : null;
  }
  
  // Check file size (max 10MB = 10,485,760 bytes)
  static Future<bool> isFileSizeValid(File file) async {
    final int fileSize = await file.length();
    const int maxSize = 10 * 1024 * 1024; // 10MB
    return fileSize <= maxSize;
  }

  Future<Map<String, dynamic>> submitVisitWithPhoto({
    required int sponsorId,
    required String notes,
    DateTime? visitDate,
  }) async {
    try {
      // 1. Capture photo using camera
      File photo = await PhotoService.capturePhoto();
      
      // 2. Check and compress if needed
      if (!await isFileSizeValid(photo)) {
        final File? compressedPhoto = await compressImage(photo);
        if (compressedPhoto == null) {
          throw ApiException('Failed to compress image');
        }
        photo = compressedPhoto;
        
        // Double-check after compression
        if (!await isFileSizeValid(photo)) {
          throw ApiException('Image too large even after compression');
        }
      }
      
      // 3. Create multipart request
      var request = http.MultipartRequest(
        'POST', 
        Uri.parse('$baseUrl/visits')
      );
      
      // 4. Add authentication header
      request.headers.addAll({
        'Authorization': 'Bearer $_token',
        'Accept': 'application/json',
      });
      
      // 5. Add form data
      request.fields.addAll({
        'sponsor_id': sponsorId.toString(),
        'notes': notes,
        if (visitDate != null) 
          'visit_date': visitDate.toIso8601String(),
      });
      
      // 6. Add photo file with proper content type
      request.files.add(
        await http.MultipartFile.fromPath(
          'photo',
          photo.path,
          contentType: MediaType('image', 'jpeg'),
        ),
      );
      
      // 7. Send request with timeout
      final streamedResponse = await request.send().timeout(
        Duration(minutes: 2), // 2 minute timeout for upload
      );
      final response = await http.Response.fromStream(streamedResponse);
      final data = json.decode(response.body);
      
      // 8. Handle response
      if (response.statusCode == 200 && data['success']) {
        return data['data']['visit'];
      } else {
        throw ApiException(
          data['message'] ?? 'Failed to submit visit',
          statusCode: response.statusCode,
          errors: data['errors'],
        );
      }
      
    } on TimeoutException {
      throw ApiException('Upload timeout - please try again');
    } catch (e) {
      throw ApiException('Failed to upload photo: $e');
    }
  }
}

// Usage example in your Flutter UI
class SubmitVisitScreen extends StatefulWidget {
  final int sponsorId;
  
  const SubmitVisitScreen({Key? key, required this.sponsorId}) : super(key: key);
  
  @override
  State<SubmitVisitScreen> createState() => _SubmitVisitScreenState();
}

class _SubmitVisitScreenState extends State<SubmitVisitScreen> {
  final TextEditingController _notesController = TextEditingController();
  final VisitService _visitService = VisitService();
  bool _isSubmitting = false;

  Future<void> _submitVisit() async {
    if (_notesController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Please enter visit notes')),
      );
      return;
    }

    setState(() => _isSubmitting = true);

    try {
      final visit = await _visitService.submitVisitWithPhoto(
        sponsorId: widget.sponsorId,
        notes: _notesController.text,
      );
      
      // Success - navigate back or show success message
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Visit logged successfully!')),
      );
      Navigator.pop(context, visit);
      
    } on ApiException catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error: ${e.message}')),
      );
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Unexpected error occurred')),
      );
    } finally {
      setState(() => _isSubmitting = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Log Visit')),
      body: Padding(
        padding: EdgeInsets.all(16),
        child: Column(
          children: [
            TextField(
              controller: _notesController,
              decoration: InputDecoration(
                labelText: 'Visit Notes',
                hintText: 'Describe your visit...',
              ),
              maxLines: 4,
            ),
            SizedBox(height: 20),
            ElevatedButton(
              onPressed: _isSubmitting ? null : _submitVisit,
              child: _isSubmitting 
                ? CircularProgressIndicator()
                : Text('Capture Photo & Submit Visit'),
            ),
          ],
        ),
      ),
    );
  }
}
```

### 5. Get Visit History

```dart
Future<Map<String, dynamic>> getVisits({int page = 1, int limit = 20}) async {
  final response = await http.get(
    Uri.parse('$baseUrl/visits?page=$page&limit=$limit'),
    headers: _headers,
  );

  final data = json.decode(response.body);
  
  if (response.statusCode == 200 && data['success']) {
    return data['data'];
  } else {
    throw Exception(data['message']);
  }
}
```

### 6. Error Handling

```dart
class ApiException implements Exception {
  final String message;
  final int? statusCode;
  final Map<String, dynamic>? errors;

  ApiException(this.message, {this.statusCode, this.errors});

  @override
  String toString() => 'ApiException: $message';
}

Future<T> handleApiResponse<T>(http.Response response) async {
  final data = json.decode(response.body);
  
  if (response.statusCode >= 200 && response.statusCode < 300) {
    if (data['success']) {
      return data['data'];
    } else {
      throw ApiException(data['message']);
    }
  } else {
    throw ApiException(
      data['message'] ?? 'Unknown error occurred',
      statusCode: response.statusCode,
      errors: data['errors'],
    );
  }
}
```

---

## 🧪 Testing

### Manual Testing with cURL

#### 1. Login
```bash
curl -X POST http://127.0.0.1:8000/api/designer/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'
```

#### 2. Get Profile
```bash
curl -X GET http://127.0.0.1:8000/api/designer/profile \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

#### 3. Verify QR Code
```bash
curl -X POST http://127.0.0.1:8000/api/designer/verify-qr \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{"qr_data":"SPONSOR-1"}'
```

#### 4. Submit Visit
```bash
curl -X POST http://127.0.0.1:8000/api/designer/visits \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -F "sponsor_id=1" \
  -F "notes=Test visit from API" \
  -F "photo=@/path/to/image.jpg"
```

#### 5. Get Visit History
```bash
curl -X GET "http://127.0.0.1:8000/api/designer/visits?page=1&limit=10" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Flutter Testing

```dart
void main() {
  group('API Tests', () {
    late ApiService apiService;

    setUp(() {
      apiService = ApiService();
    });

    test('login should return user data and token', () async {
      final result = await apiService.login('test@example.com', 'password');
      expect(result['user']['email'], equals('test@example.com'));
      expect(result['token'], isNotNull);
    });

    test('verify QR should return sponsor data', () async {
      await apiService.login('test@example.com', 'password');
      final sponsor = await apiService.verifyQR('SPONSOR-123');
      expect(sponsor['id'], equals(123));
      expect(sponsor['name'], isNotNull);
    });
  });
}
```

---

## 🛠️ Configuration

### Environment Setup

Make sure your Laravel `.env` file includes:

```env
# API Configuration
APP_URL=https://your-domain.com
SANCTUM_STATEFUL_DOMAINS=your-domain.com
SPA_URL=https://your-domain.com

# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=525600
SESSION_DOMAIN=your-domain.com

# File Storage
FILESYSTEM_DISK=public
```

### CORS Configuration

In `config/cors.php`:

```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_methods' => ['*'],
'allowed_origins' => ['*'], // Configure for production
'allowed_origins_patterns' => [],
'allowed_headers' => ['*'],
'exposed_headers' => [],
'max_age' => 0,
'supports_credentials' => true,
```

---

## 📚 Additional Resources

- [Laravel Sanctum Documentation](https://laravel.com/docs/sanctum)
- [Flutter HTTP Package](https://pub.dev/packages/http)
- [QR Code Scanner Package](https://pub.dev/packages/qr_code_scanner)
- [Camera Package](https://pub.dev/packages/camera)

---

## 🤝 Support

For API support:
1. Check the error response format
2. Verify authentication token
3. Ensure proper headers are set
4. Review Laravel logs for server errors
5. Test with cURL before Flutter integration

**API Status:** ✅ **Fully Implemented and Ready for Production**