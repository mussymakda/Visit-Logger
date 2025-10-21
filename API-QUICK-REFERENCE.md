# Visit Logger API - Quick Reference

## 🚀 Base URL

**Android Emulator (Most Common):**
```
http://10.0.2.2:8000/api/designer
```

**iOS Simulator / Physical Device:**
```
http://192.168.0.105:8000/api/designer
```

## 🌐 Network Setup Required

**Laravel Server Command:**
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

**Windows Firewall Fix (Run as Administrator):**
```bash
netsh advfirewall firewall add rule name="Laravel Dev Server" dir=in action=allow protocol=TCP localport=8000
```

## ⚠️ Connection Error Troubleshooting

**If you get connection errors:**

1. **Check Windows Firewall** (Most Common Issue)
   - Windows may be blocking port 8000
   - Run command above as Administrator
   
2. **Verify Server is Running**
   - Server should show: `Server running on [http://0.0.0.0:8000]`
   - Should NOT be `127.0.0.1:8000`

3. **Test Network Connectivity**
   ```bash
   # Test from PC browser
   http://localhost:8000/api/designer/login
   
   # Test from emulator browser
   http://10.0.2.2:8000/api/designer/login
   ```

**Why `10.0.2.2`?**  
- Android emulator's special IP to reach host machine
- `127.0.0.1` only works inside the emulator itself
- `0.0.0.0` makes Laravel accept external connections

## 🧪 Test Credentials (Local)
- **Email**: `test@example.com`
- **Password**: `password`

## 🔐 Authentication
All endpoints (except login) require:
```
Authorization: Bearer {token}
```

## 📋 Key Endpoints

| Method | Endpoint | Purpose | Auth Required |
|--------|----------|---------|---------------|
| POST | `/login` | Get API token | ❌ |
| GET | `/profile` | Get user info | ✅ |
| POST | `/logout` | Revoke token | ✅ |
| POST | `/verify-qr` | Verify QR code | ✅ |
| POST | `/visits` | Submit visit with photo | ✅ |
| GET | `/visits` | Get visit history (paginated) | ✅ |
| GET | `/visits/search` | Search visits | ✅ |
| GET | `/visits/{id}` | Get visit details | ✅ |
| GET | `/stats` | Get visit statistics | ✅ |

## 📱 Flutter Quick Start

### 1. Install Dependencies
```yaml
dependencies:
  http: ^1.1.0
  camera: ^0.10.5
  image_picker: ^1.0.4
  path: ^1.8.3
  mime: ^1.0.4
  flutter_image_compress: ^2.1.0  # For image compression
```

### 2. Required Permissions
**Android:** Camera, Storage permissions in `AndroidManifest.xml`  
**iOS:** Camera, Photo Library usage descriptions in `Info.plist`

### 3. Photo Upload Process
1. **Capture/Select** → Use `camera` or `image_picker` package
2. **Compress** → Ensure under 10MB using `flutter_image_compress`
3. **Upload** → Use `MultipartRequest` with proper headers
4. **Handle Response** → Check success/error and display feedback

### 2. Login Example
```dart
final response = await http.post(
  Uri.parse('$baseUrl/login'),
  headers: {'Content-Type': 'application/json'},
  body: json.encode({
    'email': 'designer@example.com',
    'password': 'password123',
  }),
);
```

### 3. QR Verification
```dart
final response = await http.post(
  Uri.parse('$baseUrl/verify-qr'),
  headers: {
    'Authorization': 'Bearer $token',
    'Content-Type': 'application/json',
  },
  body: json.encode({'qr_data': qrCodeString}),
);
```

### 4. Photo Upload Complete Example
```dart
// Capture photo
final cameras = await availableCameras();
final controller = CameraController(cameras.first, ResolutionPreset.high);
await controller.initialize();
final image = await controller.takePicture();
File photo = File(image.path);

// Compress if needed (max 10MB)
if (await photo.length() > 10 * 1024 * 1024) {
  photo = await FlutterImageCompress.compressAndGetFile(
    photo.path, '${photo.path}_compressed.jpg', quality: 85,
  );
}

// Upload with multipart request
var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/visits'));
request.headers['Authorization'] = 'Bearer $token';
request.fields['sponsor_id'] = sponsorId.toString();
request.fields['notes'] = visitNotes;
request.files.add(
  await http.MultipartFile.fromPath(
    'photo', 
    photo.path,
    contentType: MediaType('image', 'jpeg'),
  ),
);

final response = await request.send();
```

## 🎯 Supported QR Formats
- `SPONSOR-123`
- `sponsor=123`
- `123` (plain number)
- URLs containing sponsor ID

## 📊 Response Format
```json
{
  "success": true/false,
  "message": "Description",
  "data": { /* response data */ },
  "errors": { /* validation errors if any */ }
}
```

## 🔧 Required Headers
```dart
Map<String, String> headers = {
  'Authorization': 'Bearer $token',
  'Content-Type': 'application/json',
  'Accept': 'application/json',
};
```

## ⚡ Status Codes
- **200**: Success
- **401**: Unauthorized (bad/missing token)
- **404**: Not found
- **422**: Validation error
- **500**: Server error

## 📷 Photo Upload Requirements
- **Formats**: JPEG, PNG
- **Max Size**: 10MB
- **Field Name**: `photo`
- **Content-Type**: `multipart/form-data`

## 🔄 Pagination (GET /visits)
```dart
final response = await http.get(
  Uri.parse('$baseUrl/visits?page=1&limit=20'),
  headers: headers,
);
```

## 🔍 Search (GET /visits/search)
```dart
final response = await http.get(
  Uri.parse('$baseUrl/visits/search?query=IKEA'),
  headers: headers,
);
```

## 📈 Statistics Response
```json
{
  "stats": {
    "visits_today": 3,
    "visits_this_week": 12,
    "visits_this_month": 45,
    "total_visits": 156,
    "unique_sponsors": 23
  },
  "recent_visits": [...]
}
```

---

**For complete documentation, see: [API-DOCUMENTATION.md](API-DOCUMENTATION.md)**