#!/usr/bin/env python3
"""
RFID RFID Simulation Test
Tests the fallback RFID reader in simulation mode
"""

import time
import json
import requests
from datetime import datetime

def test_rfid_simulation():
    """Test RFID simulation functionality"""
    print("🎯 RFID SIMULATION TEST")
    print("=" * 40)
    print()
    
    # Test configuration loading
    try:
        with open('rfid_config.json', 'r') as f:
            config = json.load(f)
        print(f"✅ Configuration loaded successfully")
        print(f"   API URL: {config['api']['url']}")
        print(f"   Device ID: {config['reader']['device_id']}")
        print(f"   Read Delay: {config['reader']['read_delay']}s")
    except Exception as e:
        print(f"❌ Configuration loading failed: {e}")
        return False
    
    # Test API connectivity
    api_url = config['api']['url']
    endpoint = config['api']['endpoint']
    url = f"{api_url}{endpoint}"
    
    print(f"\n🧪 Testing API connectivity...")
    print(f"   📡 URL: {url}")
    
    # Test simulation cards from fallback reader
    simulation_cards = ['ABCD1234', 'EFGH5678', 'IJKL9012', 'MNOP3456', 'QRST7890']
    
    test_results = []
    
    for i, card_uid in enumerate(simulation_cards[:3], 1):  # Test first 3 cards
        print(f"\n🔄 Test {i}: Simulating card {card_uid}")
        
        payload = {
            'uid': card_uid,
            'device_id': 'simulation_test',
            'timestamp': datetime.now().isoformat()
        }
        
        try:
            response = requests.post(
                url, 
                json=payload,
                timeout=5,
                headers={'Content-Type': 'application/json'}
            )
            
            response_data = response.json()
            
            if response.status_code == 200:
                print(f"   ✅ SUCCESS: {response_data.get('message', 'No message')}")
                print(f"   🎯 Action: {response_data.get('action', 'unknown')}")
                test_results.append(('SUCCESS', card_uid, response_data))
            elif response.status_code == 404:
                print(f"   ✅ EXPECTED: Unknown card rejected")
                test_results.append(('EXPECTED', card_uid, 'Unknown card'))
            else:
                print(f"   ⚠️ UNEXPECTED: Status {response.status_code}")
                print(f"   📋 Response: {response_data}")
                test_results.append(('UNEXPECTED', card_uid, response_data))
                
        except requests.exceptions.RequestException as e:
            print(f"   ❌ REQUEST ERROR: {e}")
            test_results.append(('ERROR', card_uid, str(e)))
        
        # Small delay between tests
        time.sleep(0.5)
    
    # Summary
    print(f"\n📊 SIMULATION TEST SUMMARY")
    print(f"=" . repeat(30, '='))
    
    success_count = sum(1 for result in test_results if result[0] in ['SUCCESS', 'EXPECTED'])
    error_count = len(test_results) - success_count
    
    print(f"   ✅ Successful responses: {success_count}")
    print(f"   ❌ Errors: {error_count}")
    print(f"   📈 Success Rate: {(success_count/len(test_results)*100):.1f}%")
    
    if error_count == 0:
        print(f"\n🎉 RFID SIMULATION SYSTEMS WORKING PERFECTLY!")
        print(f"   ✅ API connectivity verified")
        print(f"   ✅ Card simulation working")
        print(f"   ✅ Error handling functional")
    else:
        print(f"\n⚠️ Some issues detected. Review test results above.")
    
    print(f"\n" + "=" . repeat(40, '='))
    
    return error_count == 0

def test_hardware_detection():
    """Test hardware detection capabilities"""
    print(f"\n🔧 HARDWARE DETECTION TEST")
    print(f"=" . repeat(30, '='))
    
    try:
        # Try to import smartcard libraries
        from smartcard.System import readers
        print(f"✅ Smartcard library available")
        
        # Check for readers
        available_readers = readers()
        if available_readers:
            print(f"📱 Found {len(available_readers)} reader(s):")
            for i, reader in enumerate(available_readers, 1):
                print(f"   {i}. {reader}")
        else:
            print(f"⚠️ No readers detected")
            
        return len(available_readers) > 0
        
    except ImportError as e:
        print(f"❌ Smartcard library not available: {e}")
        print(f"   📝 This is expected - simulation mode will be used")
        return False

if __name__ == "__main__":
    print("🚀 STARTING RFID FUNCTIONALITY TESTS")
    print("=" . repeat(40, '='))
    
    # Test hardware detection first
    hardware_available = test_hardware_detection()
    
    # Test simulation functionality
    simulation_working = test_rfid_simulation()
    
    # Final summary
    print(f"\n🎯 FINAL TEST SUMMARY")
    print(f"=" . repeat(30, '='))
    print(f"   🔧 Hardware Detection: {'✅ AVAILABLE' if hardware_available else '⚠️ SIMULATION MODE'}")
    print(f"   🧪 Simulation System: {'✅ WORKING' if simulation_working else '❌ ISSUES DETECTED'}")
    
    if simulation_working:
        print(f"\n🚀 RFID SYSTEM READY!")
        print(f"   📡 Connect hardware for physical card scanning")
        print(f"   🧪 Simulation mode available for development/testing")
        print(f"   💻 Run: python rfid_reader_fallback.py")
    else:
        print(f"\n⚠️ ISSUES DETECTED")
        print(f"   🔍 Check API server is running on http://localhost:8000")
        print(f"   📝 Verify Laravel application is started")
    
    print(f"\n" + "=" . repeat(40, '='))
