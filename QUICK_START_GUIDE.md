# 🚀 QUICK START GUIDE - Silencio Gym RFID System

## **Your System is Ready!** ✅

Everything is deployed and working. Here's how to use it:

---

## **1. Start the RFID Reader** (Most Common)

Open PowerShell and run:

```powershell
cd C:\Users\hanss\Documents\silencio-gym-mms-main
python silencio-gym-mms-main\rfid_reader.py
```

**That's it!** The reader will now:
- Monitor for card taps
- Send data to your VPS at http://156.67.221.184
- Log all activity

---

## **2. What You'll See**

When running, you'll see:
```
RFID Reader Started
Device ID: main_reader
API URL: http://156.67.221.184/rfid-test.php
Waiting for cards...
```

When you tap a card:
```
[Card detected: B696735F]
Sending to API...
✓ Success: Card tap recorded
Waiting for next card...
```

---

## **3. Alternative Start Methods**

### **Using Batch File** (Easier):
```batch
silencio-gym-mms-main\start_rfid_reader.bat
```

### **Optimized Mode** (Faster):
```batch
silencio-gym-mms-main\start_rfid_optimized.bat
```

### **Auto-start on Boot**:
```batch
silencio-gym-mms-main\install_rfid_autostart.bat
```

---

## **4. Stopping the Reader**

Press **Ctrl + C** in the PowerShell window

---

## **5. Check System Status**

### **Test Hardware**:
```powershell
python silencio-gym-mms-main\debug_rfid_reader.py
```

### **Check VPS**:
Open browser: http://156.67.221.184

### **View Logs**:
```powershell
# Local logs
type silencio-gym-mms-main\storage\logs\rfid_activity.log

# VPS logs
ssh root@156.67.221.184 "tail -20 /var/www/silencio-gym/storage/logs/rfid-test.log"
```

---

## **6. Troubleshooting**

### **Reader Not Detected?**
1. Unplug ACR122U from USB
2. Plug it back in
3. Wait 5 seconds
4. Try again

### **Card Not Reading?**
1. Make sure card is placed flat on reader
2. Hold for 1-2 seconds
3. Try different card position

### **API Error?**
1. Check internet connection
2. Verify VPS is accessible: `ping 156.67.221.184`
3. Test endpoint: 
   ```powershell
   Invoke-RestMethod -Uri "http://156.67.221.184/rfid-test.php" -Method POST -ContentType "application/json" -Body '{"card_uid":"test","device_id":"test"}'
   ```

---

## **7. Important Files**

| File | Purpose |
|------|---------|
| `rfid_reader.py` | Main RFID reader script |
| `rfid_config.json` | Configuration settings |
| `debug_rfid_reader.py` | Hardware diagnostic tool |
| `FINAL_SYSTEM_STATUS.md` | Complete system documentation |

---

## **8. Quick Commands Reference**

```powershell
# Start RFID reader
python silencio-gym-mms-main\rfid_reader.py

# Test hardware
python silencio-gym-mms-main\debug_rfid_reader.py

# Restart optimized
silencio-gym-mms-main\restart_rfid_optimized.bat

# Check VPS status
ssh root@156.67.221.184 "systemctl status nginx"

# View VPS logs
ssh root@156.67.221.184 "tail -f /var/www/silencio-gym/storage/logs/rfid-test.log"
```

---

## **9. System Information**

- **VPS URL**: http://156.67.221.184
- **RFID Endpoint**: /rfid-test.php
- **Reader**: ACS ACR122U
- **Card Detected**: B696735F ✅
- **Status**: Fully Operational ✅

---

## **10. Need Help?**

Check these files:
1. `FINAL_SYSTEM_STATUS.md` - Complete system status
2. `DEPLOYMENT_AND_RFID_STATUS.md` - Deployment details
3. `SIMPLE_DRIVER_INSTALL_GUIDE.md` - Driver help
4. `ACR122U_SETUP_GUIDE.md` - Hardware setup

---

**That's all you need to know to get started!** 🎉

Just run: `python silencio-gym-mms-main\rfid_reader.py` and start tapping cards!

