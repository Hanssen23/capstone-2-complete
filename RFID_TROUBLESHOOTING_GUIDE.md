# 🔧 RFID System Troubleshooting Guide

## **Problem: No Display When Tapping NFC Card**

### **Root Cause:**
The RFID reader Python script is **NOT running**. When you tap your NFC card, there's no process listening for the card, so nothing happens.

---

## **Quick Fix - Start the RFID System:**

### **Option 1: Use the Diagnostic Script (RECOMMENDED)**

1. **Navigate to the project folder:**
   ```
   cd C:\Users\hanss\Documents\silencio-gym-mms-main\silencio-gym-mms-main
   ```

2. **Run the diagnostic script:**
   ```
   diagnose_and_start_rfid.bat
   ```

3. **The script will:**
   - ✅ Check if Python is installed
   - ✅ Check if required libraries are installed
   - ✅ Check if NFC reader is detected
   - ✅ Check if Laravel server is running
   - ✅ Start the RFID reader automatically

---

### **Option 2: Manual Start**

1. **Open Command Prompt**

2. **Navigate to project directory:**
   ```cmd
   cd C:\Users\hanss\Documents\silencio-gym-mms-main\silencio-gym-mms-main
   ```

3. **Start Laravel server (if not running):**
   ```cmd
   php artisan serve
   ```
   - Leave this window open
   - Server will run on http://localhost:8000

4. **Open a NEW Command Prompt window**

5. **Navigate to project directory again:**
   ```cmd
   cd C:\Users\hanss\Documents\silencio-gym-mms-main\silencio-gym-mms-main
   ```

6. **Start RFID reader:**
   ```cmd
   python rfid_reader.py
   ```
   - Leave this window open
   - You should see: "Starting ACR122U RFID reader..."
   - You should see: "Waiting for cards..."

7. **Now tap your NFC card**
   - You should see the card UID in the console
   - The RFID Monitor page should update

---

## **Common Issues and Solutions:**

### **Issue 1: "Python is not recognized"**

**Problem:** Python is not installed or not in PATH

**Solution:**
1. Download Python from: https://www.python.org/downloads/
2. During installation, check "Add Python to PATH"
3. Restart Command Prompt
4. Test: `python --version`

---

### **Issue 2: "No module named 'smartcard'"**

**Problem:** pyscard library is not installed

**Solution:**
```cmd
pip install pyscard
```

---

### **Issue 3: "No module named 'requests'"**

**Problem:** requests library is not installed

**Solution:**
```cmd
pip install requests
```

---

### **Issue 4: "No readers detected"**

**Problem:** NFC reader is not connected or driver is not installed

**Solution:**
1. **Check USB connection:**
   - Unplug the ACR122U
   - Wait 5 seconds
   - Plug it back in
   - Wait for Windows to recognize it

2. **Check Device Manager:**
   - Press `Win + X` → Device Manager
   - Look for "Smart card readers" or "USB devices"
   - You should see "ACS ACR122U PICC Interface"
   - If you see a yellow exclamation mark, the driver is not installed

3. **Install ACS Driver:**
   - Download from: https://www.acs.com.hk/en/driver/3/acr122u-usb-nfc-reader/
   - Install the driver
   - Restart your computer
   - Plug in the ACR122U again

---

### **Issue 5: "Connection refused" or "API error"**

**Problem:** Laravel server is not running

**Solution:**
1. **Check if server is running:**
   ```cmd
   netstat -an | findstr :8000
   ```
   - If you see `:8000`, the server is running
   - If not, start it: `php artisan serve`

2. **Check the API URL in rfid_reader.py:**
   - Open `rfid_reader.py`
   - Look for `api_url`
   - Should be: `http://localhost:8000`

---

### **Issue 6: RFID reader starts but stops immediately**

**Problem:** Error in the Python script or configuration

**Solution:**
1. **Run with verbose output:**
   ```cmd
   python rfid_reader.py
   ```
   - Look for error messages
   - Common errors:
     - "No readers detected" → Check USB connection
     - "Connection refused" → Start Laravel server
     - "Module not found" → Install missing library

2. **Check the log file:**
   - Look for `rfid_activity.log` in the project folder
   - Open it to see detailed error messages

---

## **Step-by-Step Startup Procedure:**

### **Every time you want to use the RFID system:**

1. **Plug in the ACR122U NFC reader** (if not already plugged in)

2. **Start Laravel server:**
   ```cmd
   cd C:\Users\hanss\Documents\silencio-gym-mms-main\silencio-gym-mms-main
   php artisan serve
   ```
   - Keep this window open

3. **Start RFID reader (in a NEW window):**
   ```cmd
   cd C:\Users\hanss\Documents\silencio-gym-mms-main\silencio-gym-mms-main
   python rfid_reader.py
   ```
   - Keep this window open
   - You should see: "Waiting for cards..."

4. **Open RFID Monitor in browser:**
   ```
   http://localhost:8000/rfid-monitor
   ```

5. **Tap your NFC card**
   - You should see activity in the Python console
   - You should see activity in the RFID Monitor page

---

## **Verification Checklist:**

Before tapping your card, verify:

- [ ] ACR122U is plugged into USB port
- [ ] Green LED on ACR122U is lit
- [ ] Python is installed (`python --version`)
- [ ] pyscard is installed (`pip show pyscard`)
- [ ] requests is installed (`pip show requests`)
- [ ] Laravel server is running (check http://localhost:8000)
- [ ] RFID reader script is running (you see "Waiting for cards...")
- [ ] RFID Monitor page is open in browser

---

## **Testing the System:**

### **Test 1: Check if NFC reader is detected**
```cmd
python -c "from smartcard.System import readers; print(readers())"
```
**Expected output:**
```
['ACS ACR122U PICC Interface 0']
```

### **Test 2: Check if Laravel server is running**
```cmd
curl http://localhost:8000
```
**Expected:** HTML response (Laravel welcome page)

### **Test 3: Check if RFID API endpoint works**
```cmd
curl -X POST http://localhost:8000/api/rfid/tap -H "Content-Type: application/json" -d "{\"uid\":\"TEST1234\",\"device_id\":\"test\"}"
```
**Expected:** JSON response with success or error message

---

## **Logs and Debugging:**

### **Where to find logs:**

1. **RFID Reader Log:**
   - File: `rfid_activity.log`
   - Location: Project root folder
   - Contains: Card reads, API calls, errors

2. **Laravel Log:**
   - File: `storage/logs/laravel.log`
   - Contains: API requests, database queries, errors

3. **Python Console:**
   - Real-time output when running `python rfid_reader.py`
   - Shows: Card UIDs, API responses, errors

### **How to read logs:**

**RFID Reader Log:**
```
2025-10-03 12:00:00 - INFO - Starting ACR122U RFID reader...
2025-10-03 12:00:01 - INFO - Waiting for cards...
2025-10-03 12:00:05 - INFO - Card UID read: A69D194E
2025-10-03 12:00:05 - INFO - Sending card data to API: http://localhost:8000/api/rfid/tap
2025-10-03 12:00:05 - INFO - API Success: Check-in successful
```

**Laravel Log:**
```
[2025-10-03 12:00:05] local.INFO: RFID tap request received {"uid":"A69D194E","device_id":"acr122u_main"}
[2025-10-03 12:00:05] local.INFO: Member found: John Doe
[2025-10-03 12:00:05] local.INFO: Action: check_in
```

---

## **Quick Reference Commands:**

### **Check if processes are running:**
```cmd
REM Check if Python RFID reader is running
tasklist | findstr python

REM Check if Laravel server is running
netstat -an | findstr :8000
```

### **Stop processes:**
```cmd
REM Stop Python RFID reader
taskkill /F /IM python.exe

REM Stop Laravel server (Ctrl+C in the server window)
```

### **Restart everything:**
```cmd
REM 1. Stop all processes
taskkill /F /IM python.exe
taskkill /F /IM php.exe

REM 2. Wait a moment
timeout /t 2

REM 3. Start Laravel server
start cmd /k "cd C:\Users\hanss\Documents\silencio-gym-mms-main\silencio-gym-mms-main && php artisan serve"

REM 4. Wait for server to start
timeout /t 3

REM 5. Start RFID reader
start cmd /k "cd C:\Users\hanss\Documents\silencio-gym-mms-main\silencio-gym-mms-main && python rfid_reader.py"
```

---

## **Summary:**

**The main issue is:** The RFID reader Python script is not running.

**The solution is:** Start the RFID reader by running:
```cmd
cd C:\Users\hanss\Documents\silencio-gym-mms-main\silencio-gym-mms-main
python rfid_reader.py
```

**Keep the window open** and you should see "Waiting for cards..." - then tap your NFC card!

---

## **Need More Help?**

If you're still having issues:

1. Run the diagnostic script: `diagnose_and_start_rfid.bat`
2. Check the logs: `rfid_activity.log` and `storage/logs/laravel.log`
3. Make sure the ACR122U green LED is lit
4. Try unplugging and replugging the ACR122U
5. Restart your computer and try again

---

**Date:** October 3, 2025  
**Issue:** No display when tapping NFC card  
**Root Cause:** RFID reader script not running  
**Solution:** Start the RFID reader with `python rfid_reader.py`

