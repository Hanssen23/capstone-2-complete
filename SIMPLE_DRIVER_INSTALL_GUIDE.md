# 🔧 Simple ACR122U Driver Installation Guide

## **Method 1: Using Search (Easiest)**

### **Step 1: Open Device Manager**
1. Click the **Windows Start button** (bottom-left corner)
2. Type: **device manager**
3. Click on **"Device Manager"** when it appears

### **Step 2: Find the ACR122 Device**
In Device Manager, look for one of these:
- **"Smart card readers"** section → **"ACR122 Smart Card Reader"**
- **"Other devices"** section → **"ACR122 Smart Card Reader"** (with yellow warning)
- **"Universal Serial Bus controllers"** → **"ACR122 Smart Card Reader"**

### **Step 3: Update the Driver**
1. **Right-click** on **"ACR122 Smart Card Reader"**
2. Click **"Update driver"**
3. Click **"Browse my computer for drivers"**
4. Click the **"Browse..."** button
5. Navigate to this folder:
   ```
   C:\Users\hanss\Documents\ACS-Unified-Driver-Win-4280
   ```
6. Click **"OK"**
7. Click **"Next"**
8. Wait for Windows to install the driver
9. Click **"Close"** when done

### **Step 4: Verify Installation**
1. In Device Manager, find **"ACR122 Smart Card Reader"** again
2. It should now show under **"Smart card readers"** with **no yellow warning**
3. **Unplug** the ACR122U reader from USB
4. **Plug it back in**
5. Wait 5 seconds for Windows to recognize it

---

## **Method 2: Using Run Command (Alternative)**

### **Step 1: Open Device Manager**
1. Press **Windows Key + R** (this opens the "Run" dialog)
2. Type: **devmgmt.msc**
3. Press **Enter**

### **Step 2-4: Same as Method 1**
Follow steps 2-4 from Method 1 above.

---

## **Method 3: Automatic Installation (Requires Admin)**

### **Step 1: Run as Administrator**
1. Find the file: **`install_acr122_driver_auto.ps1`**
2. **Right-click** on it
3. Select **"Run with PowerShell"** or **"Run as Administrator"**
4. If you see a security warning, click **"Yes"** or **"Allow"**
5. Follow the on-screen instructions

---

## **What You're Looking For**

### **Before Driver Installation:**
```
Device Manager
└── Smart card readers
    └── ACR122 Smart Card Reader (⚠️ with yellow warning)
        Status: Unknown
```

### **After Driver Installation:**
```
Device Manager
└── Smart card readers
    └── ACS ACR122U PICC Reader (✅ no warning)
        Status: OK
```

---

## **Troubleshooting**

### **Can't Find Device Manager?**
Try these alternatives:
1. **Search method**: Click Start → type "device" → click "Device Manager"
2. **Control Panel**: Start → Control Panel → Hardware and Sound → Device Manager
3. **Right-click Start**: Right-click the Windows Start button → Device Manager

### **Can't Find ACR122 Device?**
1. Make sure the ACR122U reader is **plugged into USB**
2. Try a **different USB port**
3. Look in these sections:
   - Smart card readers
   - Other devices
   - Universal Serial Bus controllers
   - Unknown devices

### **Can't Find Driver Folder?**
The driver folder should be at:
```
C:\Users\hanss\Documents\ACS-Unified-Driver-Win-4280
```

If it's not there, check:
```
C:\Users\hanss\Downloads\ACS-Unified-Driver-Win-4280
```

Or search your computer for: **ACS-Unified-Driver**

### **Driver Installation Fails?**
1. **Unplug** the ACR122U reader
2. **Restart** your computer
3. **Plug in** the ACR122U reader
4. Try the installation again

---

## **After Installation - Test It!**

### **Test 1: Check Device Manager**
1. Open Device Manager
2. Look under **"Smart card readers"**
3. You should see: **"ACS ACR122U PICC Reader"** (no yellow warning)

### **Test 2: Run Diagnostic Script**
Open PowerShell or Command Prompt and run:
```bash
python silencio-gym-mms-main/debug_rfid_reader.py
```

**Expected output:**
```
=== Testing Smart Card Readers ===
Found 1 reader(s)
Reader 0: ACS ACR122U PICC Reader
[OK] Connection created successfully
[OK] Connected with RAW protocol
```

### **Test 3: Test with RFID Card**
1. Run the diagnostic script (above)
2. When it says "Place an RFID card on the reader..."
3. **Place your RFID card** on the ACR122U reader
4. You should see: **"[OK] Card detected! UID: XXXXXXXX"**

---

## **Quick Reference**

| What | Where |
|------|-------|
| **Device Manager** | Start → type "device manager" |
| **ACR122 Device** | Smart card readers → ACR122 Smart Card Reader |
| **Driver Folder** | C:\Users\hanss\Documents\ACS-Unified-Driver-Win-4280 |
| **Test Script** | python silencio-gym-mms-main/debug_rfid_reader.py |

---

## **Still Having Issues?**

If you still can't find Device Manager or the ACR122 device, let me know and I can:
1. Create a script that opens Device Manager for you
2. Help you locate the exact device
3. Try alternative installation methods
4. Check if the driver files are in the right location

---

**Next Step After Installation:**
Once the driver is installed, run the test script to verify everything works:
```bash
python silencio-gym-mms-main/debug_rfid_reader.py
```

