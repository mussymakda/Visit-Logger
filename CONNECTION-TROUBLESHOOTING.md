# 🔧 Connection Error Troubleshooting Guide

## ❌ **Common Connection Errors & Solutions**

### **Error: "Connection refused" or "Network unreachable"**

## 🛠️ **Step-by-Step Fix**

### **Step 1: Fix Windows Firewall (Most Important)**

**Run PowerShell as Administrator** and execute:
```powershell
netsh advfirewall firewall add rule name="Laravel Dev Server" dir=in action=allow protocol=TCP localport=8000
```

**Alternative: Manual Firewall Setup**
1. Open Windows Defender Firewall
2. Click "Advanced settings"
3. Click "Inbound Rules" → "New Rule"
4. Select "Port" → Next
5. Select "TCP" → Specific local ports: `8000` → Next
6. Select "Allow the connection" → Next
7. Check all profiles → Next
8. Name: "Laravel Dev Server" → Finish

### **Step 2: Verify Laravel Server Configuration**

**Correct command:**
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

**You should see:**
```
INFO Server running on [http://0.0.0.0:8000]
```

**❌ Wrong (won't work with emulator):**
```bash
php artisan serve --host=127.0.0.1 --port=8000
```

### **Step 3: Test Network Connectivity**

#### **Test 1: From PC Browser**
Open browser and visit:
```
http://localhost:8000/api/designer/login
```

**Expected result:** You should see Laravel routing page or API response

#### **Test 2: From Command Line**
```powershell
curl http://localhost:8000/api/designer/login
```

#### **Test 3: Check Port is Open**
```powershell
netstat -an | findstr :8000
```

**Expected result:**
```
TCP    0.0.0.0:8000           0.0.0.0:0              LISTENING
```

### **Step 4: Test from Android Emulator**

1. **Open emulator browser**
2. **Navigate to:** `http://10.0.2.2:8000/api/designer/login`
3. **Expected:** API response or Laravel page

## 📱 **Flutter App Configuration**

### **Correct Flutter Configuration:**

```dart
class ApiService {
  // For Android Emulator
  static const String baseUrl = 'http://10.0.2.2:8000/api/designer';
  
  // Alternative method - detect platform
  static String get baseUrl {
    if (Platform.isAndroid && kDebugMode) {
      return 'http://10.0.2.2:8000/api/designer';  // Android Emulator
    } else {
      return 'http://192.168.0.105:8000/api/designer';  // iOS/Physical
    }
  }
}
```

### **Test API Connection in Flutter:**

```dart
void testConnection() async {
  try {
    final response = await http.get(
      Uri.parse('${ApiService.baseUrl}/login'),
      headers: {'Accept': 'application/json'},
    );
    print('Connection Status: ${response.statusCode}');
    print('Response: ${response.body}');
  } catch (e) {
    print('Connection Error: $e');
  }
}
```

## 🌐 **Network Diagnosis**

### **Check Your Network Settings:**

#### **1. Verify PC IP Address**
```powershell
ipconfig | findstr "IPv4"
```
**Your current IP:** `192.168.0.105`

#### **2. Test Network Reachability**
```powershell
# Test if port 8000 is accessible
telnet localhost 8000

# Test from another device on same network
telnet 192.168.0.105 8000
```

#### **3. Check Laravel Logs**
```bash
tail -f storage/logs/laravel.log
```

## 🚨 **Common Issues & Quick Fixes**

### **Issue 1: Windows Firewall Blocking**
**Symptoms:** Connection refused, timeout
**Solution:** Add firewall rule (Step 1 above)

### **Issue 2: Wrong Server Host**
**Symptoms:** Works locally but not from emulator
**Solution:** Use `--host=0.0.0.0` instead of `127.0.0.1`

### **Issue 3: Wrong Emulator IP**
**Symptoms:** Network unreachable from Flutter app
**Solution:** Use `10.0.2.2` not `localhost` or `127.0.0.1`

### **Issue 4: Antivirus Blocking**
**Symptoms:** Random connection drops
**Solution:** Add Laravel folder to antivirus exclusions

### **Issue 5: Port Already in Use**
**Symptoms:** "Address already in use" error
**Solution:** 
```powershell
netstat -ano | findstr :8000
taskkill /PID [PID_NUMBER] /F
```

## 🧪 **Testing Workflow**

### **Quick Test Sequence:**

1. **Start Server:**
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```

2. **Test from PC:**
   ```bash
   curl http://localhost:8000/api/designer/login
   ```

3. **Test from Emulator Browser:**
   ```
   http://10.0.2.2:8000/api/designer/login
   ```

4. **Test Flutter HTTP Call:**
   ```dart
   http.get(Uri.parse('http://10.0.2.2:8000/api/designer/login'))
   ```

### **Expected Responses:**

✅ **Success Response:**
```json
{
  "message": "The GET method is not supported for route api/designer/login. Supported methods: POST."
}
```

❌ **Connection Error:**
- "Connection refused"
- "Network unreachable" 
- "Timeout"

## 📋 **Current Configuration Status**

- ✅ **Laravel Server:** `php artisan serve --host=0.0.0.0 --port=8000`
- ✅ **PC IP Address:** `192.168.0.105`
- ✅ **Android Emulator URL:** `http://10.0.2.2:8000/api/designer`
- ✅ **CORS Configured:** Multiple origins allowed
- ⚠️ **Windows Firewall:** May need manual configuration (requires admin)

## 🆘 **If Nothing Works**

### **Alternative Port:**
```bash
php artisan serve --host=0.0.0.0 --port=8080
```

Update Flutter to use:
```dart
static const String baseUrl = 'http://10.0.2.2:8080/api/designer';
```

### **Alternative: Use ngrok (Tunnel)**
```bash
# Install ngrok, then:
ngrok http 8000
```

Use the ngrok URL in Flutter:
```dart
static const String baseUrl = 'https://xyz123.ngrok.io/api/designer';
```

---

**Most Common Solution:** Run PowerShell as Administrator and execute the firewall command, then restart your Flutter app.