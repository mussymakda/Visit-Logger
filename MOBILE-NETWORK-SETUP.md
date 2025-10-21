# Mobile App Network Configuration Guide

## 🌐 **Network Setup for Emulator/Device Testing**

### **Problem**
Flutter apps running on emulators/devices cannot reach `localhost` or `127.0.0.1` because these addresses refer to the emulator/device's own localhost, not your PC's localhost.

### **Solutions by Platform**

## 📱 **Android Emulator**

### **API Base URL**
```dart
static const String baseUrl = 'http://10.0.2.2:8000/api/designer';
```

### **Why 10.0.2.2?**
- Android emulator creates a virtual network
- `10.0.2.2` is the special IP that refers to the host machine (your PC)
- This is the standard Android emulator networking configuration

### **Laravel Server Command**
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## 📱 **iOS Simulator**

### **API Base URL**
```dart
static const String baseUrl = 'http://192.168.0.105:8000/api/designer';
```

### **Laravel Server Command**
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### **Your PC's IP Address**
Your current PC IP address is: **192.168.0.105**

## 📱 **Physical Device (Android/iOS)**

### **Requirements**
- Device and PC must be on the **same WiFi network**
- PC firewall must allow connections on port 8000

### **API Base URL**
```dart
static const String baseUrl = 'http://192.168.0.105:8000/api/designer';
```

### **Laravel Server Command**
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## 🔧 **Laravel Configuration Updates**

### **Environment Variables (.env)**
```env
APP_URL=http://192.168.0.105:8000
SANCTUM_STATEFUL_DOMAINS=192.168.0.105:8000,10.0.2.2:8000,localhost:8000
```

### **CORS Configuration (config/cors.php)**
```php
'allowed_origins' => [
    'http://10.0.2.2:8000',
    'http://192.168.0.105:8000', 
    'http://localhost:8000',
    'http://127.0.0.1:8000'
],
```

## 📋 **Flutter Environment Configuration**

### **Development Setup**
Create different build configurations for different environments:

### **android/app/build.gradle**
```gradle
android {
    buildTypes {
        debug {
            buildConfigField "String", "API_BASE_URL", '"http://10.0.2.2:8000/api/designer"'
        }
        release {
            buildConfigField "String", "API_BASE_URL", '"https://your-production-domain.com/api/designer"'
        }
    }
}
```

### **Flutter Code**
```dart
class ApiConfig {
  static String get baseUrl {
    // Android Emulator
    if (Platform.isAndroid && kDebugMode) {
      return 'http://10.0.2.2:8000/api/designer';
    }
    // iOS Simulator or Physical Device
    else if (kDebugMode) {
      return 'http://192.168.0.105:8000/api/designer';
    }
    // Production
    else {
      return 'https://your-domain.com/api/designer';
    }
  }
}
```

## 🧪 **Testing Different Configurations**

### **Test API Connectivity**

#### **1. From Android Emulator**
```bash
# In emulator terminal or ADB shell
curl -X GET http://10.0.2.2:8000/api/designer/login
```

#### **2. From iOS Simulator/Physical Device**
```bash
curl -X GET http://192.168.0.105:8000/api/designer/login
```

#### **3. Test with Flutter HTTP Client**
```dart
void testApiConnection() async {
  try {
    final response = await http.get(
      Uri.parse('${ApiConfig.baseUrl}/login'),
      headers: {'Accept': 'application/json'},
    );
    print('API Connection: ${response.statusCode}');
  } catch (e) {
    print('API Connection Failed: $e');
  }
}
```

## 🛠️ **Troubleshooting**

### **Common Issues & Solutions**

#### **1. Connection Refused**
**Problem**: `Connection refused` or `Network unreachable`
**Solution**: 
- Ensure Laravel server runs with `--host=0.0.0.0`
- Check Windows Firewall settings
- Verify IP address is correct

#### **2. CORS Errors**
**Problem**: Browser/app blocks requests due to CORS policy
**Solution**:
- Update `config/cors.php` with correct origins
- Ensure `'supports_credentials' => true`

#### **3. Android Emulator Network Issues**
**Problem**: `10.0.2.2` not working
**Solution**:
- Try wiping emulator data
- Use different emulator image
- Check emulator network settings

#### **4. Physical Device Can't Connect**
**Problem**: Device can't reach PC
**Solution**:
- Ensure both on same WiFi
- Check PC's firewall rules
- Try pinging PC from device

### **Windows Firewall Configuration**
```bash
# Allow incoming connections on port 8000
netsh advfirewall firewall add rule name="Laravel Dev Server" dir=in action=allow protocol=TCP localport=8000
```

### **Verify Network Connectivity**
```bash
# Check if port 8000 is open
netstat -an | findstr :8000

# Test from another device on same network
curl -X GET http://192.168.0.105:8000
```

## 🎯 **Quick Setup Checklist**

### **For Android Emulator Testing:**
- [ ] Laravel server: `php artisan serve --host=0.0.0.0 --port=8000`
- [ ] Flutter API URL: `http://10.0.2.2:8000/api/designer`
- [ ] Test: Can access from emulator browser

### **For iOS/Physical Device Testing:**
- [ ] Laravel server: `php artisan serve --host=0.0.0.0 --port=8000`  
- [ ] Flutter API URL: `http://192.168.0.105:8000/api/designer`
- [ ] Same WiFi network confirmed
- [ ] Test: Can ping PC IP from device

### **Laravel Configuration:**
- [ ] CORS allows your IP addresses
- [ ] Sanctum configured with correct domains
- [ ] .env APP_URL updated if needed

## 🚀 **Production Deployment**

When deploying to production:
1. Change API URLs to your production domain
2. Enable HTTPS/SSL certificates
3. Update CORS to allow only production domains
4. Configure proper Sanctum domains

---

**Current Status:** 
- ✅ Laravel Server: Running on `0.0.0.0:8000`
- ✅ PC IP Address: `192.168.0.105`
- ✅ Android Emulator URL: `http://10.0.2.2:8000/api/designer`
- ✅ iOS/Device URL: `http://192.168.0.105:8000/api/designer`