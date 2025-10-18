#!/usr/bin/env python3
"""
Test T1 Protocol for RFID Card Reading
This script specifically tests the T1 protocol that works with your reader
"""

import time
from smartcard.System import readers
from smartcard.util import toHexString
from smartcard.Exceptions import NoCardException, CardConnectionException
from smartcard.CardConnection import CardConnection

def test_t1_card_reading():
    """Test card reading with T1 protocol"""
    print("=== T1 PROTOCOL CARD READING TEST ===\n")
    
    # Get reader
    available_readers = readers()
    if not available_readers:
        print("❌ No readers found!")
        return False
    
    reader = available_readers[0]
    print(f"✅ Using reader: {reader}")
    
    print("\n💡 Please place a card on the reader...")
    print("🔄 Testing for 10 seconds...\n")
    
    start_time = time.time()
    attempts = 0
    successes = 0
    
    while time.time() - start_time < 10:
        attempts += 1
        
        try:
            # Create connection with T1 protocol
            connection = reader.createConnection()
            connection.connect(CardConnection.T1_protocol)
            
            # Try standard UID command
            apdu = [0xFF, 0xCA, 0x00, 0x00, 0x00]
            data, sw1, sw2 = connection.transmit(apdu)
            
            if sw1 == 0x90 and sw2 == 0x00:
                uid = ''.join(format(x, '02X') for x in data)
                successes += 1
                print(f"✅ SUCCESS #{successes}: Card UID = {uid}")
            else:
                # Try alternative command
                apdu_alt = [0xFF, 0xCA, 0x00, 0x00, 0x04]
                data, sw1, sw2 = connection.transmit(apdu_alt)
                
                if sw1 == 0x90 and sw2 == 0x00:
                    uid = ''.join(format(x, '02X') for x in data)
                    successes += 1
                    print(f"✅ SUCCESS #{successes} (alt): Card UID = {uid}")
                else:
                    print(f"⚠️  Attempt {attempts}: Status {sw1:02X} {sw2:02X}")
            
            connection.disconnect()
            
        except NoCardException:
            print(f"🔍 Attempt {attempts}: No card detected")
        except CardConnectionException as e:
            print(f"❌ Attempt {attempts}: Connection error: {e}")
        except Exception as e:
            print(f"❌ Attempt {attempts}: Unexpected error: {e}")
        
        time.sleep(0.5)
    
    print(f"\n=== RESULTS ===")
    print(f"Total attempts: {attempts}")
    print(f"Successful reads: {successes}")
    print(f"Success rate: {(successes/attempts*100):.1f}%" if attempts > 0 else "0%")
    
    return successes > 0

if __name__ == "__main__":
    try:
        success = test_t1_card_reading()
        if success:
            print("\n🎉 T1 protocol works! The simple RFID reader should work now.")
        else:
            print("\n❌ T1 protocol test failed. Check card placement and try again.")
    except KeyboardInterrupt:
        print("\n👋 Test stopped by user")
    except Exception as e:
        print(f"\n❌ Critical error: {e}")
        import traceback
        traceback.print_exc()
