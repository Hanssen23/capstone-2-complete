#!/usr/bin/env python3
"""
ACR122U NFC Reader Integration Script
Connects to ACR122U reader and sends card data to Laravel API
"""

import requests
import json
import time
import sys
import os
from pathlib import Path
from smartcard.System import readers
from smartcard.util import toHexString, toBytes

class ACR122UReader:
    def __init__(self, api_url="http://localhost:8000"):
        # Normalize base URL (no trailing slash)
        self.api_url = api_url.rstrip("/") if api_url else "http://localhost:8000"
        self.device_id = "acr122u_main"
        self.connection = None
        self.reader = None
        
    def connect(self):
        """Connect to ACR122U reader"""
        try:
            # Get available readers
            r = readers()
            
            if not r:
                print("❌ No smart card readers found!")
                print("🔧 Please check:")
                print("   - USB connection is secure")
                print("   - Reader drivers are installed") 
                print("   - Device is recognized by Windows")
                print("⏳ Will retry continuously...")
                return False
            
            print(f"📱 Found {len(r)} reader(s):")
            for i, reader in enumerate(r):
                print(f"   {i+1}. {reader}")
            
            # Use the first reader (usually ACR122U)
            self.reader = r[0]
            print(f"🔗 Reader selected: {self.reader}")
            
            # Don't connect immediately - we'll connect when needed
            self.connection = None
            
            print("✅ Reader initialized successfully!")
            return True
            
        except Exception as e:
            print(f"❌ Connection failed: {e}")
            print("🔧 Troubleshooting:")
            print("   - Ensure RFID reader is connected via USB")
            print("   - Try reconnecting the reader")
            print("   - Check if reader drivers are installed")
            print("⏳ Will retry continuously...")
            return False
    
    def get_card_uid(self):
        """Read card UID from ACR122U"""
        try:
            # Create connection if not exists
            if not self.connection:
                self.connection = self.reader.createConnection()
                self.connection.connect()
            
            # APDU command to get UID
            get_uid = [0xFF, 0xCA, 0x00, 0x00, 0x04]
            
            # Send command
            response, sw1, sw2 = self.connection.transmit(get_uid)
            
            if sw1 == 0x90 and sw2 == 0x00:
                # Convert response to hex string
                uid = toHexString(response).replace(' ', '').upper()
                return uid
            else:
                # Card not present or error
                return None
                
        except Exception as e:
            # Card not present or communication error
            # Reset connection for next attempt
            self.connection = None
            return None
    
    def check_card_presence(self):
        """Check if a card is present without trying to read it"""
        try:
            # Create connection if not exists
            if not self.connection:
                self.connection = self.reader.createConnection()
                self.connection.connect()
            
            # Simple command to check card presence
            get_uid = [0xFF, 0xCA, 0x00, 0x00, 0x04]
            
            # Send command
            response, sw1, sw2 = self.connection.transmit(get_uid)
            
            # If we get a response, card is present
            return True
                
        except Exception as e:
            # Card not present or communication error
            # Reset connection for next attempt
            self.connection = None
            return False
    
    def send_to_api(self, card_uid):
        """Send card data to Laravel API"""
        try:
            data = {
                'card_uid': card_uid,
                'device_id': self.device_id
            }
            
            headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
            
            response = requests.post(
                f"{self.api_url}/rfid/tap",
                json=data,
                headers=headers,
                timeout=5
            )
            
            if response.status_code == 200:
                result = response.json()
                print(f"✅ {result.get('message', 'Success')}")
                return True
            else:
                print(f"❌ API Error: {response.status_code}")
                if response.text:
                    try:
                        error = response.json()
                        print(f"   {error.get('message', 'Unknown error')}")
                    except:
                        print(f"   {response.text}")
                return False
                
        except requests.exceptions.RequestException as e:
            print(f"❌ Network error: {e}")
            return False
    
    def run(self):
        """Main loop to continuously read cards"""
        print("\n🚀 Starting RFID Reader System...")
        print("🔧 Detecting hardware...")
        
        connection_retry_count = 0
        max_retries = 10
        
        while connection_retry_count < max_retries:
            if self.connect():
                break
            connection_retry_count += 1
            print(f"🔄 Connection attempt {connection_retry_count}/{max_retries}")
            time.sleep(5)
        
        if connection_retry_count >= max_retries:
            print("\n⚠️ Unable to connect to RFID hardware after multiple attempts")
            print("🛑 Starting anyway - hardware detection will continue in background")
            print("💡 Try reconnecting the RFID reader and restarting the system\n")
        
        print("🎯 RFID reader ready to detect cards!")
        print("📋 Place a card on the reader to check in/out")
        print("🛑 Press Ctrl+C to exit")
        print("\n")
        
        card_presentation = False
        last_uid = None
        reader_connected = True
        
        try:
            while True:
                try:
                    # Check if card is present
                    card_detected = self.check_card_presence()
                    
                    if card_detected and not card_present:
                        # New card detected, try to get UID
                        uid = self.get_card_uid()
                        
                        if uid:
                            print(f"\n📱 Card detected: {uid}")
                            
                            # Send to API
                            success = self.send_to_api(uid)
                            
                            if success:
                                card_present = True
                                last_uid = uid
                            
                            print("📋 Waiting for card removal...\n")
                    
                    elif not card_detected and card_present:
                        # Card was removed
                        print("📤 Card removed - ready for next card\n")
                        card_present = False
                        last_uid = None
                    
                    time.sleep(0.05)  # Ultra-fast response for immediate detection
                    
                except Exception as e:
                    # Silent error handling - hardware might have disconnected
                    time.sleep(0.5)
                    
        except KeyboardInterrupt:
            print("\n\n👋 Shutting down RFID reader...")
            if self.connection:
                self.connection.disconnect()
            print("✅ Disconnected from ACR122U")

def main():
    """Main function"""
    print("🚀 ACR122U NFC Reader Integration")
    print("=" * 40)

    # Check if pyscard is installed
    try:
        import smartcard  # noqa: F401
    except ImportError:
        print("❌ Error: pyscard library not found!")
        print("📦 Install it with: pip install pyscard")
        return

    # Determine script directory (so we can resolve paths even if CWD is different)
    script_dir: Path = Path(__file__).resolve().parent

    # CLI override: --api <url>
    api_override = None
    if len(sys.argv) >= 3 and sys.argv[1] in ("--api", "-a"):
        api_override = sys.argv[2]

    # ENV override
    env_api = os.environ.get("RFID_API_URL")

    # Load configuration from file next to the script, falling back to CWD
    api_url = None
    config_paths = [
        script_dir / "rfid_config.json",
        Path.cwd() / "rfid_config.json",
    ]
    for cfg_path in config_paths:
        try:
            if cfg_path.exists():
                with cfg_path.open("r", encoding="utf-8") as f:
                    config = json.load(f)
                api_url = (config.get("api", {}) or {}).get("url")
                if api_url:
                    print(f"📋 Loaded config from {cfg_path}: API URL = {api_url}")
                    break
        except (OSError, json.JSONDecodeError) as e:
            print(f"⚠️  Config read error at {cfg_path}: {e}")

    # Apply overrides and defaults (prefer CLI > ENV > config > localhost default)
    api_url = (api_override or env_api or api_url or "http://localhost:8000").rstrip("/")
    print(f"🌐 Using API URL: {api_url}")

    # Create and run reader
    reader = ACR122UReader(api_url)
    reader.run()

if __name__ == "__main__":
    main()
