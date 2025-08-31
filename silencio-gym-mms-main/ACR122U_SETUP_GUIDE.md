# ACR122U NFC Reader Setup Guide

## 📋 **Prerequisites**

### Hardware Requirements
- **ACR122U NFC Reader** (USB connection)
- **NFC Cards/Tags** (13.56MHz ISO14443A compatible)
- **Computer** (Windows/Linux/Mac)

### Software Requirements
- **Python 3.7+** installed
- **ACS Driver** for ACR122U (downloaded from ACS website)
- **Laravel Application** running (your gym management system)

## 🔧 **Step-by-Step Setup**

### **Step 1: Hardware Installation**

1. **Connect ACR122U Reader**:
   ```
   📱 Connect ACR122U to USB port
   🔌 Install ACS driver from downloaded package
   ✅ Verify in Device Manager (Windows) or lsusb (Linux)
   ```

2. **Test Hardware**:
   - Use ACS test utility to verify card reading
   - Make sure you can read card UIDs
   - Note the UID format (usually 8-byte hex string)

### **Step 2: Software Installation**

1. **Install Python Dependencies**:
   ```bash
   pip install pyscard requests
   ```

2. **Verify Installation**:
   ```bash
   python -c "import smartcard; print('pyscard OK')"
   python -c "import requests; print('requests OK')"
   ```

### **Step 3: Configure Your System**

1. **Update Configuration**:
   - Edit `rfid_config.json`
   - Set your Laravel API URL
   - Adjust reader settings as needed

2. **Start Laravel Server**:
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```

### **Step 4: Test the Integration**

1. **Run the RFID Reader**:
   ```bash
   # Option 1: Direct Python
   python rfid_reader.py
   
   # Option 2: Windows Batch (easier)
   start_rfid_reader.bat
   ```

2. **Test with Sample Cards**:
   - Use the test cards provided in your database
   - UIDs: UID001, UID002, UID003, UID004
   - Place cards on reader one by one

## 🎯 **Expected Behavior**

### **Successful Check-in**:
```
📱 Card detected: UID001
✅ Welcome back, John Doe!
📋 Waiting for next card...
```

### **Successful Check-out**:
```
📱 Card detected: UID001
✅ Goodbye, John Doe! Session duration: 1h 23m
📋 Waiting for next card...
```

### **Error Scenarios**:
```
📱 Card detected: UNKNOWN_CARD
❌ Unknown card. Please contact staff.
📋 Waiting for next card...
```

## 🔍 **Troubleshooting**

### **Common Issues**

#### **1. "No card readers found"**
- **Solution**: Check USB connection and driver installation
- **Verify**: Device appears in Device Manager
- **Test**: Use ACS test utility

#### **2. "Connection failed"**
- **Solution**: Close other applications using the reader
- **Check**: No other RFID software running
- **Restart**: Disconnect and reconnect USB

#### **3. "API Error: Connection refused"**
- **Solution**: Start Laravel server
- **Check**: `php artisan serve` is running
- **Verify**: API URL in config is correct

#### **4. "Card read error"**
- **Solution**: Use compatible NFC cards
- **Check**: Card is 13.56MHz ISO14443A
- **Test**: Try different cards

### **Debug Mode**

Enable debug logging in `rfid_config.json`:
```json
{
    "logging": {
        "enabled": true,
        "log_level": "DEBUG"
    }
}
```

## 📊 **Monitoring & Logs**

### **Real-time Monitoring**
- **Dashboard**: Check your Laravel dashboard for active members
- **RFID Monitor**: Use the RFID monitoring panel
- **Logs**: Check `rfid_activity.log` for detailed activity

### **Database Verification**
```sql
-- Check RFID logs
SELECT * FROM rfid_logs ORDER BY timestamp DESC LIMIT 10;

-- Check active sessions
SELECT * FROM active_sessions WHERE status = 'active';

-- Check attendance records
SELECT * FROM attendances ORDER BY check_in_time DESC LIMIT 10;
```

## 🔄 **Production Deployment**

### **Windows Service**
1. Install `pywin32`: `pip install pywin32`
2. Create Windows service for automatic startup
3. Configure to run on system boot

### **Linux Service**
1. Create systemd service file
2. Enable automatic startup
3. Configure logging to syslog

### **Network Configuration**
- **Firewall**: Allow connections to Laravel API
- **Port**: Ensure port 8000 (or your port) is accessible
- **SSL**: Use HTTPS in production

## 🛡️ **Security Considerations**

### **Physical Security**
- **Reader Location**: Place in secure, monitored area
- **Cable Management**: Secure USB connections
- **Access Control**: Limit physical access to reader

### **Network Security**
- **API Authentication**: Implement proper authentication
- **HTTPS**: Use SSL/TLS encryption
- **Rate Limiting**: Prevent API abuse

### **Data Protection**
- **Card UIDs**: Store securely, consider encryption
- **Log Retention**: Implement log rotation
- **Access Logs**: Monitor for suspicious activity

## 📈 **Performance Optimization**

### **Reader Settings**
- **Read Delay**: Adjust `read_delay` in config
- **Duplicate Prevention**: Tune `duplicate_prevention_seconds`
- **Connection Pooling**: Reuse connections when possible

### **API Optimization**
- **Caching**: Implement Redis caching
- **Database Indexing**: Optimize database queries
- **Background Jobs**: Use queues for heavy operations

## 🔧 **Customization**

### **Adding LED Feedback**
```python
# Add to rfid_reader.py
def set_led(self, color):
    # ACR122U LED control commands
    if color == 'green':
        self.connection.transmit([0xFF, 0x00, 0x40, 0x04, 0x04, 0x00, 0x00, 0x00])
    elif color == 'red':
        self.connection.transmit([0xFF, 0x00, 0x40, 0x04, 0x04, 0x00, 0x00, 0x01])
```

### **Adding Sound Feedback**
```python
# Add to rfid_reader.py
import winsound  # Windows
# or
import os  # Linux/Mac

def play_sound(self, sound_type):
    if sound_type == 'success':
        winsound.Beep(1000, 200)  # High beep
    elif sound_type == 'error':
        winsound.Beep(500, 400)   # Low beep
```

### **Custom Card Types**
```python
# Add support for different card types
def get_card_type(self, uid):
    # Implement card type detection
    # Return: 'member', 'staff', 'guest', etc.
    pass
```

## 📞 **Support**

### **ACS Support**
- **Website**: https://www.acs.com.hk
- **Documentation**: ACR122U User Manual
- **Drivers**: Latest drivers from ACS website

### **System Logs**
- **Laravel Logs**: `storage/logs/laravel.log`
- **RFID Logs**: `rfid_activity.log`
- **System Logs**: Check OS-specific logs

### **Testing Tools**
- **ACS Test Utility**: For hardware testing
- **Postman**: For API testing
- **Browser**: For web interface testing

---

**Version**: 1.0.0  
**Last Updated**: January 2025  
**Compatibility**: ACR122U, Python 3.7+, Laravel 10+
