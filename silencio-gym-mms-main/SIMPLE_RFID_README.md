# Simple RFID Reader for Silencio Gym

A lightweight, easy-to-use RFID reader implementation for the Silencio Gym Management System.

## 🚀 Quick Start

### 1. Prerequisites
- **ACR122U RFID Reader** connected via USB
- **Python 3.x** installed
- **Required Python packages** (install with: `pip install -r requirements.txt`)

### 2. Run the Simple RFID Reader

**Option A: Use the batch file (Windows)**
```bash
START_SIMPLE_RFID.bat
```

**Option B: Run directly with Python**
```bash
python simple_rfid_reader.py
```

### 3. Test the System
```bash
python test_simple_rfid.py
```

## 📋 What You'll See

When you run the simple RFID reader, you'll see:

```
==================================================
    Simple RFID Reader for Silencio Gym
==================================================
🌐 API URL: http://156.67.221.184/api/rfid/tap

✅ Found 1 reader(s):
   1. ACS ACR122 0
🔗 Using reader: ACS ACR122 0

🎯 Listening for RFID cards...
💡 Place a card on the reader to test
🛑 Press Ctrl+C to stop
```

When you place a card on the reader:
```
🔍 Card detected: E6258C40
📡 Server response: 200
✅ Welcome back, pat farala!
```

## 🔧 Configuration

The simple RFID reader is pre-configured to work with your VPS server:

- **API URL**: `http://156.67.221.184/api/rfid/tap`
- **Timeout**: 5 seconds
- **Duplicate Prevention**: 2 seconds

## 📁 Files Included

- `simple_rfid_reader.py` - Main RFID reader script
- `test_simple_rfid.py` - Test script to verify everything works
- `START_SIMPLE_RFID.bat` - Windows batch file for easy startup
- `requirements.txt` - Python dependencies

## 🆚 Simple vs Advanced Reader

| Feature | Simple Reader | Advanced Reader |
|---------|---------------|-----------------|
| **Setup** | ✅ Plug & Play | ⚙️ Configuration needed |
| **Dependencies** | ✅ Minimal | 📦 Many packages |
| **Error Handling** | ✅ Basic | 🛡️ Comprehensive |
| **Logging** | ✅ Console only | 📝 File + Console |
| **Performance** | ✅ Fast startup | ⚡ Optimized |
| **Features** | 🎯 Core functionality | 🚀 Full-featured |

## 🔍 Troubleshooting

### Reader Not Found
```
❌ No RFID readers found.
```
**Solution**: Check that your ACR122U is connected and drivers are installed.

### API Connection Issues
```
❌ Error sending to server: [connection error]
```
**Solution**: Check your internet connection and VPS server status.

### Card Not Recognized
```
⚠️ Unknown card. Please contact staff.
```
**Solution**: The card needs to be registered in the system first.

## 🎯 Usage Tips

1. **Place card firmly** on the reader surface
2. **Wait for confirmation** before removing the card
3. **Don't tap too quickly** - the system prevents duplicates for 2 seconds
4. **Check the console** for real-time feedback

## 🔄 How It Works

1. **Initialize**: Connects to the first available ACR122U reader
2. **Listen**: Continuously polls for RFID cards
3. **Read**: Extracts the unique UID from detected cards
4. **Send**: Posts the UID to the VPS server API
5. **Respond**: Displays the server response (check-in/out status)

## 🛠️ Technical Details

- **Protocol**: Uses standard APDU commands (`0xFF, 0xCA, 0x00, 0x00, 0x00`)
- **Connection**: Creates new connection for each read (more stable)
- **Error Handling**: Gracefully handles card removal and connection issues
- **Duplicate Prevention**: Ignores same card within 2-second window

## 📞 Support

If you encounter any issues:

1. Run `python test_simple_rfid.py` to diagnose problems
2. Check that your ACR122U drivers are properly installed
3. Verify your internet connection to the VPS server
4. Ensure the card is registered in the gym management system

---

**Ready to start?** Run `START_SIMPLE_RFID.bat` and place a card on your reader! 🎉
