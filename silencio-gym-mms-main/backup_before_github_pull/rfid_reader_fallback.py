#!/usr/bin/env python3
"""
Fallback RFID Reader for Silencio Gym Management System
Handles hardware detection and falls back to simulation mode if smartcard library unavailable
"""

import time
import json
import requests
import logging
import sys
import random
import string
from datetime import datetime

# Try to import smartcard library, gracefully handle if unavailable
try:
    from smartcard.System import readers
    from smartcard.util import toHexString
    from smartcard.Exceptions import CardConnectionException, NoCardException
    SMARTCARD_AVAILABLE = True
    print("✓ Smartcard library available - hardware mode enabled")
except ImportError as e:
    SMARTCARD_AVAILABLE = False
    print(f"⚠ Smartcard library not available ({e}) - using simulation mode")

class FallbackRFIDReader:
    def __init__(self, config_file="rfid_config.json"):
        """Initialize the RFID reader with fallback capabilities"""
        self.config = self.load_config(config_file)
        self.api_url = self.config.get('api', {}).get('url', 'http://localhost:8000')
        self.device_id = self.config.get('reader', {}).get('device_id', 'fallback_reader')
        self.read_delay = self.config.get('reader', {}).get('read_delay', 0.5)
        self.duplicate_prevention = self.config.get('reader', {}).get('duplicate_prevention_seconds', 2)
        
        self.connection = None
        self.reader = None
        self.last_card_uid = None
        self.last_card_time = 0
        
        # Two-tap system state tracking
        self.card_states = {}  # Track state for each card UID
        self.current_card_on_reader = None  # Track which card is currently on reader
        
        # Setup logging
        self.setup_logging()
        
        # Initialize reader
        self.initialize_reader()
        
        # Simulation mode properties
        self.simulation_mode = not SMARTCARD_AVAILABLE
        self.simulation_cards = [
            "ABCD1234",  # Test card 1
            "EFGH5678",  # Test card 2
            "IJKL9012",  # Test card 3
            "MNOP3456",  # Test card 4
            "QRST7890"   # Test card 5
        ]
        self.simulation_index = 0
    
    def load_config(self, config_file):
        """Load configuration from JSON file"""
        try:
            with open(config_file, 'r') as f:
                return json.load(f)
        except FileNotFoundError:
            # Default configuration
            return {
                "api": {
                    "url": "http://localhost:8000",
                    "endpoint": "/api/rfid/tap",
                    "timeout": 5
                },
                "reader": {
                    "device_id": "fallback_reader",
                    "read_delay": 0.5,
                    "duplicate_prevention_seconds": 2
                },
                "feedback": {
                    "success_sound": True,
                    "error_sound": True,
                    "led_indicator": True
                },
                "logging": {
                    "enabled": True,
                    "log_file": "rfid_activity.log",
                    "log_level": "INFO"
                }
            }
    
    def setup_logging(self):
        """Setup logging configuration"""
        if self.config.get('logging', {}).get('enabled', True):
            log_file = self.config.get('logging', {}).get('log_file', 'rfid_activity.log')
            log_level = self.config.get('logging', {}).get('log_level', 'INFO')
            
            logging.basicConfig(
                level=getattr(logging, log_level.upper()),
                format='%(asctime)s - %(levelname)s - %(message)s',
                handlers=[
                    logging.FileHandler(log_file),
                    logging.StreamHandler()
                ]
            )
            self.logger = logging.getLogger(__name__)
        else:
            self.logger = logging.getLogger(__name__)
            self.logger.disabled = True
    
    def initialize_reader(self):
        """Initialize the RFID reader (hardware or simulation)"""
        try:
            if SMARTCARD_AVAILABLE:
                self.initialize_hardware()
            else:
                self.initialize_simulation()
        except Exception as e:
            self.logger.error(f"Reader initialization failed: {e}")
            self.initialize_simulation()
    
    def initialize_hardware(self):
        """Initialize hardware RFID reader"""
        try:
            available_readers = readers()
            if not available_readers:
                raise Exception("No smart card readers found")
            
            # Find ACR122U reader
            acr122u_reader = None
            for reader in available_readers:
                if 'ACR122' in str(reader):
                    acr122u_reader = reader
                    break
            
            if not acr122u_reader:
                raise Exception("ACR122U reader not found")
            
            self.reader = acr122u_reader
            self.logger.info(f"Hardware RFID reader initialized: {self.reader}")
            
        except Exception as e:
            self.logger.warning(f"Hardware initialization failed: {e}. Switching to simulation mode.")
            self.initialize_simulation()
    
    def initialize_simulation(self):
        """Initialize simulation mode"""
        self.simulation_mode = True
        self.reader = "SIMULATION_MODE"
        self.logger.info("✓ Simulation mode activated - perfect for testing and development")
        self.logger.info("Available simulation cards: " + ", ".join(self.simulation_cards))
    
    def read_card(self):
        """Read card UID from reader (hardware or simulation)"""
        try:
            if self.simulation_mode:
                return self.simulate_card_read()
            else:
                return self.read_hardware_card()
        except Exception as e:
            self.logger.error(f"Card read error: {e}")
            return None
    
    def simulate_card_read(self):
        """Simulate card reading for testing/demo purposes"""
        # Simulate random card reads for testing
        simulation_card = self.simulation_cards[self.simulation_index % len(self.simulation_cards)]
        self.simulation_index += 1
        
        self.logger.info(f"[SIMULATION] Card detected: {simulation_card}")
        return simulation_card
    
    def read_hardware_card(self):
        """Read card from actual hardware"""
        if not SMARTCARD_AVAILABLE or not self.reader:
            return None
            
        try:
            # Connect to reader
            connection = self.reader.createConnection()
            connection.connect()
            
            # Try to get card UID using standard APDU commands
            data, sw1, sw2 = connection.transmit([0xFF, 0xCA, 0x00, 0x00, 0x00])
            
            if sw1 == 0x90 and sw2 == 0x00 and len(data) > 0:
                # Convert bytes to hex string
                card_uid = ''.join([f'{byte:02X}' for byte in data])
                self.logger.info(f"Card detected: {card_uid}")
                return card_uid
            
            connection.disconnect()
            return None
            
        except NoCardException:
            # No card present - this is normal
            return None
        except CardConnectionException as e:
            self.logger.error(f"Card connection error: {e}")
            return None
        except Exception as e:
            self.logger.error(f"Hardware read error: {e}")
            return None
    
    def send_to_api(self, card_uid):
        """Send card UID to Laravel API"""
        try:
            endpoint = self.config.get('api', {}).get('endpoint', '/api/rfid/tap')
            url = f"{self.api_url}{endpoint}"
            
            payload = {
                'uid': card_uid,
                'device_id': self.device_id,
                'timestamp': datetime.now().isoformat()
            }
            
            response = requests.post(
                url, 
                json=payload, 
                timeout=self.config.get('api', {}).get('timeout', 5),
                headers={'Content-Type': 'application/json'}
            )
            
            if response.status_code == 200:
                self.logger.info(f"✓ API response: {response.json()}")
                return True
            else:
                self.logger.error(f"✗ API error: {response.status_code} - {response.text}")
                return False
                
        except Exception as e:
            self.logger.error(f"API communication error: {e}")
            return False
    
    def run(self):
        """Main loop for processing RFID cards"""
        mode_str = "SIMULATION" if self.simulation_mode else "HARDWARE"
        self.logger.info(f"🚀 RFID Reader started in {mode_str} mode")
        self.logger.info(f"📡 API URL: {self.api_url}")
        
        try:
            while True:
                current_time = time.time()
                
                # Check for duplicate prevention
                card_uid = self.read_card()
                if card_uid:
                    if (self.last_card_uid == card_uid and 
                        current_time - self.last_card_time < self.duplicate_prevention):
                        self.logger.debug(f"Skipping duplicate card: {card_uid}")
                        continue
                    
                    self.last_card_uid = card_uid
                    self.last_card_time = current_time
                    
                    # Send to API
                    if self.simulation_mode:
                        self.logger.info(f"🔄 [SIMULATION] Sending {card_uid} to API...")
                    else:
                        self.logger.info(f"🔄 [HARDWARE] Sending {card_uid} to API...")
                    
                    self.send_to_api(card_uid)
                
                time.sleep(self.read_delay)
                
        except KeyboardInterrupt:
            self.logger.info("👋 RFID Reader stopped by user")
        except Exception as e:
            self.logger.error(f"❌ Unexpected error: {e}")

def main():
    """Main entry point"""
    print("=" * 50)
    print("Silencio Gym RFID Reader")
    print("Fallback Version with Hardware/Simulation Support")
    print("=" * 50)
    
    reader = FallbackRFIDReader()
    
    print(f"Mode: {'SIMULATION' if reader.simulation_mode else 'HARDWARE'}")
    print(f"API URL: {reader.api_url}")
    print("Press Ctrl+C to stop")
    print("-" * 50)
    
    reader.run()

if __name__ == "__main__":
    main()
