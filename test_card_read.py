#!/usr/bin/env python3
"""
Simple test script to check if the ACR122U can read cards
"""

from smartcard.System import readers
from smartcard.util import toHexString
from smartcard.CardConnection import CardConnection
from smartcard.Exceptions import CardConnectionException, NoCardException
import time

print("=" * 60)
print("ACR122U Card Reader Test")
print("=" * 60)

# Get available readers
available_readers = readers()
print(f"\nAvailable readers: {available_readers}")

if not available_readers:
    print("ERROR: No card readers found!")
    exit(1)

# Find ACR122U
reader = None
for r in available_readers:
    if 'ACR122' in str(r):
        reader = r
        break

if not reader:
    print("ERROR: ACR122U reader not found!")
    exit(1)

print(f"Using reader: {reader}")
print("\nWaiting for card... (Place your card on the reader)")
print("Press Ctrl+C to exit\n")

try:
    while True:
        try:
            # Try to connect to a card
            connection = reader.createConnection()
            connection.connect(CardConnection.T0_protocol)
            
            # Get UID
            get_uid_command = [0xFF, 0xCA, 0x00, 0x00, 0x00]
            data, sw1, sw2 = connection.transmit(get_uid_command, CardConnection.T0_protocol)
            
            if sw1 == 0x90 and sw2 == 0x00:
                uid = toHexString(data).replace(' ', '')
                print(f"✅ CARD DETECTED!")
                print(f"   UID: {uid}")
                print(f"   Status: {sw1:02X} {sw2:02X}")
                print(f"   Time: {time.strftime('%Y-%m-%d %H:%M:%S')}")
                print()
                
                # Wait for card to be removed
                print("   Remove card to read another...")
                while True:
                    try:
                        connection.transmit(get_uid_command, CardConnection.T0_protocol)
                        time.sleep(0.5)
                    except:
                        print("   Card removed.\n")
                        break
            
            connection.disconnect()
            
        except NoCardException:
            # No card present - this is normal
            pass
        except CardConnectionException:
            # Card was removed or connection lost
            pass
        except Exception as e:
            if "No card" not in str(e) and "removed" not in str(e).lower():
                print(f"Error: {e}")
        
        time.sleep(0.3)
        
except KeyboardInterrupt:
    print("\n\nTest stopped by user.")
    print("=" * 60)

