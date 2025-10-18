#!/usr/bin/env python3
"""
Comprehensive RFID Reader Diagnostic Script
This script will help identify why the UID reading is failing
"""

import time
import sys
from smartcard.System import readers
from smartcard.util import toHexString
from smartcard.Exceptions import NoCardException, CardConnectionException
from smartcard.CardConnection import CardConnection

def test_reader_detection():
    """Test if ACR122U reader is detected"""
    print("=== STEP 1: READER DETECTION ===")
    try:
        available_readers = readers()
        if not available_readers:
            print("❌ CRITICAL: No RFID readers found!")
            print("   - Check USB connection")
            print("   - Check driver installation")
            return False
        
        print(f"✅ Found {len(available_readers)} reader(s):")
        for i, reader in enumerate(available_readers):
            print(f"   {i+1}. {reader}")
        
        return available_readers[0]
    except Exception as e:
        print(f"❌ CRITICAL: Reader detection failed: {e}")
        return False

def test_reader_connection(reader):
    """Test basic connection to reader"""
    print("\n=== STEP 2: READER CONNECTION ===")
    try:
        connection = reader.createConnection()
        print("✅ Connection object created")
        
        # Try different protocols
        protocols = [
            (CardConnection.T0_protocol, "T0"),
            (CardConnection.T1_protocol, "T1"),
            (CardConnection.RAW_protocol, "RAW")
        ]
        
        for protocol, name in protocols:
            try:
                connection.connect(protocol)
                print(f"✅ Connected with {name} protocol")
                connection.disconnect()
                return True
            except Exception as e:
                print(f"❌ {name} protocol failed: {e}")
        
        print("❌ CRITICAL: All protocols failed")
        return False
        
    except Exception as e:
        print(f"❌ CRITICAL: Connection creation failed: {e}")
        return False

def test_card_detection(reader):
    """Test card detection with different methods"""
    print("\n=== STEP 3: CARD DETECTION ===")
    print("💡 Please place a card on the reader now...")
    
    methods = [
        {
            'name': 'Method 1: Standard UID Command',
            'command': [0xFF, 0xCA, 0x00, 0x00, 0x00],
            'protocol': CardConnection.T0_protocol
        },
        {
            'name': 'Method 2: Alternative UID Command',
            'command': [0xFF, 0xCA, 0x00, 0x00, 0x04],
            'protocol': CardConnection.T0_protocol
        },
        {
            'name': 'Method 3: RAW Protocol',
            'command': [0xFF, 0xCA, 0x00, 0x00, 0x00],
            'protocol': CardConnection.RAW_protocol
        }
    ]
    
    for method in methods:
        print(f"\n🔍 Testing {method['name']}...")
        
        for attempt in range(5):
            try:
                connection = reader.createConnection()
                connection.connect(method['protocol'])
                
                data, sw1, sw2 = connection.transmit(method['command'])
                
                if sw1 == 0x90 and sw2 == 0x00:
                    uid = toHexString(data).replace(' ', '')
                    print(f"✅ SUCCESS! Card UID: {uid}")
                    connection.disconnect()
                    return uid
                else:
                    print(f"   Attempt {attempt+1}: Status {sw1:02X} {sw2:02X}")
                
                connection.disconnect()
                
            except NoCardException:
                print(f"   Attempt {attempt+1}: No card detected")
            except CardConnectionException as e:
                print(f"   Attempt {attempt+1}: Connection error: {e}")
            except Exception as e:
                print(f"   Attempt {attempt+1}: Error: {e}")
            
            time.sleep(0.5)
    
    print("❌ No card detected with any method")
    return None

def test_continuous_detection(reader):
    """Test continuous card detection"""
    print("\n=== STEP 4: CONTINUOUS DETECTION TEST ===")
    print("🔄 Testing continuous detection for 10 seconds...")
    print("💡 Try placing and removing cards during this test")
    
    start_time = time.time()
    detections = 0
    
    while time.time() - start_time < 10:
        try:
            connection = reader.createConnection()
            connection.connect(CardConnection.T0_protocol)
            
            data, sw1, sw2 = connection.transmit([0xFF, 0xCA, 0x00, 0x00, 0x00])
            
            if sw1 == 0x90 and sw2 == 0x00:
                uid = toHexString(data).replace(' ', '')
                detections += 1
                print(f"🔍 Detection #{detections}: {uid}")
            
            connection.disconnect()
            
        except (NoCardException, CardConnectionException):
            # Normal when no card present
            pass
        except Exception as e:
            print(f"❌ Unexpected error: {e}")
        
        time.sleep(0.1)
    
    print(f"✅ Test completed. Total detections: {detections}")
    return detections > 0

def main():
    """Main diagnostic function"""
    print("=" * 60)
    print("    RFID READER DIAGNOSTIC TOOL")
    print("=" * 60)
    print("🎯 This tool will help identify UID reading issues")
    print()
    
    # Step 1: Reader detection
    reader = test_reader_detection()
    if not reader:
        print("\n❌ DIAGNOSIS: Reader hardware issue")
        print("🔧 SOLUTION: Check USB connection and drivers")
        return
    
    # Step 2: Connection test
    if not test_reader_connection(reader):
        print("\n❌ DIAGNOSIS: Reader communication issue")
        print("🔧 SOLUTION: Try different USB port or reinstall drivers")
        return
    
    # Step 3: Card detection
    uid = test_card_detection(reader)
    if not uid:
        print("\n❌ DIAGNOSIS: Card detection issue")
        print("🔧 SOLUTIONS:")
        print("   - Make sure card is placed flat on reader")
        print("   - Try different cards")
        print("   - Check if card is RFID/NFC compatible")
        return
    
    # Step 4: Continuous detection
    if test_continuous_detection(reader):
        print("\n✅ DIAGNOSIS: Reader is working correctly!")
        print("🎉 The issue might be in the main script logic")
    else:
        print("\n⚠️ DIAGNOSIS: Intermittent detection issues")
        print("🔧 SOLUTIONS:")
        print("   - Check reader positioning")
        print("   - Try slower polling rate")
        print("   - Check power supply")
    
    print("\n" + "=" * 60)
    print("    DIAGNOSTIC COMPLETE")
    print("=" * 60)

if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("\n👋 Diagnostic stopped by user")
    except Exception as e:
        print(f"\n❌ CRITICAL ERROR: {e}")
        import traceback
        traceback.print_exc()
