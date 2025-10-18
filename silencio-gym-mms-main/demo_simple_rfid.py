#!/usr/bin/env python3
"""
Demo script to show the simple RFID reader behavior
This runs for 15 seconds to demonstrate the cooldown and card handling
"""

import subprocess
import sys
import time

def run_demo():
    print("=" * 60)
    print("    SIMPLE RFID READER DEMO")
    print("=" * 60)
    print("🎯 This demo will run for 15 seconds")
    print("💡 Try placing and removing cards to see the behavior")
    print("⏰ Notice the 5-second cooldown between same card reads")
    print("🔄 You can try different cards immediately")
    print()
    print("Starting in 3 seconds...")
    
    for i in range(3, 0, -1):
        print(f"⏳ {i}...")
        time.sleep(1)
    
    print("\n🚀 Starting RFID reader demo!\n")
    
    try:
        # Run the simple RFID reader for 15 seconds
        result = subprocess.run([
            sys.executable, "simple_rfid_reader.py"
        ], timeout=15, capture_output=False)
        
    except subprocess.TimeoutExpired:
        print("\n⏰ Demo completed (15 seconds)")
    except KeyboardInterrupt:
        print("\n👋 Demo stopped by user")
    except Exception as e:
        print(f"\n❌ Demo error: {e}")
    
    print("\n" + "=" * 60)
    print("    DEMO SUMMARY")
    print("=" * 60)
    print("✅ The simple RFID reader now has:")
    print("   • 5-second cooldown between same card reads")
    print("   • Clear feedback about card state")
    print("   • Detection of card removal")
    print("   • Immediate processing of different cards")
    print()
    print("🎯 Ready for production use!")
    print("💡 Run: python simple_rfid_reader.py")

if __name__ == "__main__":
    run_demo()
