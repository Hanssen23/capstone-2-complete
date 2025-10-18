#!/usr/bin/env python3
"""
Test script for the simple RFID reader
This script tests the connection to the ACR122U reader and API without requiring a card
"""

import time
import requests
from smartcard.System import readers
from smartcard.util import toHexString

# API configuration
API_URL = "http://156.67.221.184/api/rfid/tap"

def test_reader_connection():
    """Test if ACR122U reader is connected and accessible"""
    print("🔍 Testing RFID reader connection...")
    
    try:
        available_readers = readers()
        if not available_readers:
            print("❌ No RFID readers found.")
            return False
        
        print(f"✅ Found {len(available_readers)} reader(s):")
        for i, reader in enumerate(available_readers):
            print(f"   {i+1}. {reader}")
        
        # Try to connect to the first reader
        reader = available_readers[0]
        connection = reader.createConnection()
        connection.connect()
        print(f"✅ Successfully connected to: {reader}")
        connection.disconnect()
        return True
        
    except Exception as e:
        print(f"❌ Error connecting to reader: {e}")
        return False

def test_api_connection():
    """Test API connection with a test UID"""
    print("\n🌐 Testing API connection...")
    
    try:
        test_data = {"uid": "TEST_SIMPLE_RFID"}
        response = requests.post(API_URL, json=test_data, timeout=5)
        
        print(f"✅ API responded with status: {response.status_code}")
        print(f"📝 Response: {response.text}")
        
        if response.status_code in [200, 404]:  # 404 is expected for unknown cards
            return True
        else:
            return False
            
    except Exception as e:
        print(f"❌ API connection error: {e}")
        return False

def main():
    """Main test function"""
    print("=" * 50)
    print("    Simple RFID Reader Test")
    print("=" * 50)
    
    # Test reader connection
    reader_ok = test_reader_connection()
    
    # Test API connection
    api_ok = test_api_connection()
    
    # Summary
    print("\n" + "=" * 50)
    print("    Test Results Summary")
    print("=" * 50)
    print(f"RFID Reader: {'✅ OK' if reader_ok else '❌ FAILED'}")
    print(f"API Connection: {'✅ OK' if api_ok else '❌ FAILED'}")
    
    if reader_ok and api_ok:
        print("\n🎉 All tests passed! Your simple RFID reader should work.")
        print("💡 Run 'python simple_rfid_reader.py' to start reading cards.")
    else:
        print("\n⚠️  Some tests failed. Please check the issues above.")
    
    print("\n" + "=" * 50)

if __name__ == "__main__":
    main()
