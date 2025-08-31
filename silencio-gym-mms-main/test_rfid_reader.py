#!/usr/bin/env python3
"""
Simple RFID Reader Test Script
Tests if the ACR122U reader is properly detected and can read cards
"""

import sys
import time
from smartcard.System import readers
from smartcard.util import toHexString

def test_reader_detection():
    """Test if any smart card readers are detected"""
    print("🔍 Testing RFID Reader Detection...")
    print("=" * 50)
    
    try:
        # Get list of available readers
        r = readers()
        
        if len(r) == 0:
            print("❌ No smart card readers found!")
            print("\nPossible solutions:")
            print("1. Make sure ACR122U is connected via USB")
            print("2. Install ACS Unified Driver")
            print("3. Check Device Manager for driver issues")
            return False
        
        print(f"✅ Found {len(r)} smart card reader(s):")
        for i, reader in enumerate(r):
            print(f"   {i+1}. {reader}")
        
        return True
        
    except Exception as e:
        print(f"❌ Error detecting readers: {e}")
        return False

def test_card_reading(reader_index=0):
    """Test reading a card from the specified reader"""
    print(f"\n📱 Testing Card Reading (Reader {reader_index + 1})...")
    print("=" * 50)
    
    try:
        r = readers()
        if len(r) == 0:
            print("❌ No readers available for testing")
            return False
        
        reader = r[reader_index]
        print(f"📋 Using reader: {reader}")
        
        # Connect to the reader
        connection = reader.createConnection()
        connection.connect()
        
        print("✅ Connected to reader successfully!")
        print("📱 Please place an NFC card on the reader...")
        print("   (Press Ctrl+C to stop)")
        
        # Try to read card UID
        while True:
            try:
                # Send APDU command to get UID
                apdu = [0xFF, 0xCA, 0x00, 0x00, 0x04]
                response, sw1, sw2 = connection.transmit(apdu)
                
                if sw1 == 0x90 and sw2 == 0x00:
                    uid = toHexString(response)
                    print(f"✅ Card detected! UID: {uid}")
                    print("   Card data:", response)
                else:
                    print("📱 Waiting for card...")
                
                time.sleep(1)
                
            except KeyboardInterrupt:
                print("\n⏹️  Stopped by user")
                break
            except Exception as e:
                print(f"❌ Error reading card: {e}")
                break
        
        connection.disconnect()
        return True
        
    except Exception as e:
        print(f"❌ Error testing card reading: {e}")
        return False

def main():
    """Main test function"""
    print("🚀 ACR122U RFID Reader Test")
    print("=" * 50)
    
    # Test reader detection
    if not test_reader_detection():
        return
    
    # Test card reading
    test_card_reading()
    
    print("\n✅ Test completed!")

if __name__ == "__main__":
    main()
