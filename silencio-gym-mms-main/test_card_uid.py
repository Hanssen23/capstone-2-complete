#!/usr/bin/env python3
"""
Test script to identify the UID of your RFID card
"""

import requests
import json
import time
import sys
import os
from pathlib import Path
from smartcard.System import readers
from smartcard.util import toHexString, toBytes

class CardUIDTester:
    def __init__(self):
        self.connection = None
        self.reader = None
        
    def connect(self):
        """Connect to ACR122U reader"""
        try:
            # Get available readers
            r = readers()
            
            if not r:
                print("ERROR: No smart card readers found!")
                return False
            
            print(f"Found {len(r)} reader(s):")
            for i, reader in enumerate(r):
                print(f"   {i+1}. {reader}")
            
            # Use the first reader (usually ACR122U)
            self.reader = r[0]
            print(f"Reader selected: {self.reader}")
            
            # Don't connect immediately - we'll connect when needed
            self.connection = None
            
            print("Reader initialized successfully!")
            return True
            
        except Exception as e:
            print(f"Connection failed: {e}")
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
    
    def test_api_connection(self, card_uid):
        """Test API connection with the card UID"""
        try:
            # Load config
            config_path = Path(__file__).parent / "rfid_config.json"
            if config_path.exists():
                with config_path.open("r", encoding="utf-8") as f:
                    config = json.load(f)
                api_url = config.get("api", {}).get("url", "http://localhost:8080")
            else:
                api_url = "http://localhost:8080"
            
            data = {
                'card_uid': card_uid,
                'device_id': 'test_device'
            }
            
            headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
            
            print(f"Testing API connection to: {api_url}")
            response = requests.post(
                f"{api_url}/rfid/tap",
                json=data,
                headers=headers,
                timeout=5
            )
            
            print(f"API Response Status: {response.status_code}")
            print(f"API Response: {response.text}")
            
            return response.status_code == 200
            
        except requests.exceptions.RequestException as e:
            print(f"Network error: {e}")
            return False
    
    def run(self):
        """Main loop to continuously read cards"""
        if not self.connect():
            return
        
        print("\n=== RFID Card UID Tester ===")
        print("Place your RFID card on the reader to identify its UID")
        print("Press Ctrl+C to exit")
        print("=" * 40)
        
        card_present = False
        last_uid = None
        
        try:
            while True:
                try:
                    # Check if card is present
                    card_detected = self.check_card_presence()
                    
                    if card_detected and not card_present:
                        # New card detected, try to get UID
                        uid = self.get_card_uid()
                        
                        if uid:
                            print(f"\n🎯 CARD DETECTED!")
                            print(f"UID: {uid}")
                            print(f"Length: {len(uid)} characters")
                            
                            # Test API connection
                            print("\nTesting API connection...")
                            api_success = self.test_api_connection(uid)
                            
                            if api_success:
                                print("✅ API connection successful!")
                            else:
                                print("❌ API connection failed!")
                            
                            print("\n" + "="*50)
                            print("INSTRUCTIONS:")
                            print("1. Copy the UID above")
                            print("2. Update your member in the database with this UID")
                            print("3. Or create a new member with this UID")
                            print("="*50)
                            
                            card_present = True
                            last_uid = uid
                    
                    elif not card_detected and card_present:
                        # Card was removed
                        print("Card removed - ready for next card\n")
                        card_present = False
                        last_uid = None
                    
                    time.sleep(0.5)  # Small delay
                    
                except Exception as e:
                    # Silent error handling
                    time.sleep(0.5)
                    
        except KeyboardInterrupt:
            print("\n\nShutting down...")
            if self.connection:
                self.connection.disconnect()
            print("Disconnected from ACR122U")

def main():
    """Main function"""
    print("RFID Card UID Tester")
    print("=" * 40)

    # Check if pyscard is installed
    try:
        import smartcard  # noqa: F401
    except ImportError:
        print("ERROR: pyscard library not found!")
        print("Install it with: pip install pyscard")
        return

    # Create and run tester
    tester = CardUIDTester()
    tester.run()

if __name__ == "__main__":
    main()
