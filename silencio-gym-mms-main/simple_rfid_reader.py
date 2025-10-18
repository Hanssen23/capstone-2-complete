import time
import requests
from smartcard.System import readers
from smartcard.util import toHexString
from smartcard.Exceptions import NoCardException, CardConnectionException
from smartcard.CardConnection import CardConnection

# 🌐 VPS server URL for Silencio Gym
API_URL = "http://156.67.221.184/api/rfid/tap"

class ACR122UReader:
    def __init__(self):
        self.reader = None
        self.connection = None
        self.last_uid = None
        self.last_time = 0

    def connect(self):
        """Initialize connection to ACR122U reader"""
        try:
            available_readers = readers()
            if not available_readers:
                print("❌ No RFID readers found.")
                return False

            print(f"✅ Found {len(available_readers)} reader(s):")
            for i, reader in enumerate(available_readers):
                print(f"   {i+1}. {reader}")

            # Use the first available reader
            self.reader = available_readers[0]
            print(f"🔗 Using reader: {self.reader}")
            return True

        except Exception as e:
            print(f"❌ Error initializing reader: {e}")
            return False

    def read_card_uid(self):
        """Attempt to read a card UID from the reader using T1 protocol"""
        try:
            # Create a new connection for each read attempt
            self.connection = self.reader.createConnection()

            # Use T1 protocol (which works with this reader)
            self.connection.connect(CardConnection.T1_protocol)

            # Command to get UID
            apdu = [0xFF, 0xCA, 0x00, 0x00, 0x00]
            data, sw1, sw2 = self.connection.transmit(apdu)

            # Disconnect after reading
            self.connection.disconnect()
            self.connection = None

            if sw1 == 0x90 and sw2 == 0x00:
                uid = ''.join(format(x, '02X') for x in data)
                return uid
            else:
                # Try alternative command if first one fails
                self.connection = self.reader.createConnection()
                self.connection.connect(CardConnection.T1_protocol)

                apdu_alt = [0xFF, 0xCA, 0x00, 0x00, 0x04]
                data, sw1, sw2 = self.connection.transmit(apdu_alt)

                self.connection.disconnect()
                self.connection = None

                if sw1 == 0x90 and sw2 == 0x00:
                    uid = ''.join(format(x, '02X') for x in data)
                    return uid

                return None

        except NoCardException:
            # No card present - this is normal, just return None
            if self.connection:
                try:
                    self.connection.disconnect()
                except:
                    pass
                self.connection = None
            return None
        except CardConnectionException:
            # Card connection issue - disconnect and return None
            if self.connection:
                try:
                    self.connection.disconnect()
                except:
                    pass
                self.connection = None
            return None
        except Exception as e:
            # Other errors - log and return None
            if self.connection:
                try:
                    self.connection.disconnect()
                except:
                    pass
                self.connection = None
            return None

    def send_to_api(self, uid):
        """Send UID to Laravel API"""
        try:
            response = requests.post(API_URL, json={"uid": uid}, timeout=5)
            print(f"📡 Server response: {response.status_code}")

            if response.status_code == 200:
                result = response.json()
                print(f"✅ {result.get('message', 'Success')}")
            else:
                result = response.json()
                print(f"⚠️  {result.get('message', 'Unknown response')}")

        except Exception as e:
            print(f"❌ Error sending to server: {e}")

    def listen_for_cards(self):
        """Main loop for reading cards"""
        print("\n🎯 Listening for RFID cards...")
        print("💡 Place a card on the reader to test")
        print("🛑 Press Ctrl+C to stop\n")

        card_on_reader = None

        while True:
            try:
                uid = self.read_card_uid()

                if uid:
                    current_time = time.time()

                    # Check if this is a new card or the same card after cooldown
                    if self.last_uid == uid and (current_time - self.last_time) < 5:
                        # Same card within cooldown period - ignore
                        if card_on_reader != uid:
                            remaining = 5 - (current_time - self.last_time)
                            print(f"⏰ Card {uid} on cooldown - {remaining:.1f}s remaining")
                            card_on_reader = uid
                        continue

                    # New card or cooldown expired
                    if card_on_reader != uid:
                        card_on_reader = uid
                        self.last_uid = uid
                        self.last_time = current_time

                        print(f"🔍 Card detected: {uid}")
                        self.send_to_api(uid)

                        print("⏳ Card processed! Cooldown: 5 seconds")
                        print("💡 Remove card and wait, or try a different card")
                        print("-" * 40)

                        # Brief pause to let user see the message
                        time.sleep(0.5)
                else:
                    # No card detected
                    if card_on_reader:
                        print(f"📤 Card {card_on_reader} removed from reader")
                        card_on_reader = None

                time.sleep(0.3)  # Reasonable delay between reads

            except KeyboardInterrupt:
                print("\n👋 RFID reader stopped by user")
                break
            except Exception as e:
                print(f"❌ Unexpected error: {e}")
                time.sleep(1)

if __name__ == "__main__":
    print("=" * 50)
    print("    Simple RFID Reader for Silencio Gym")
    print("=" * 50)
    print(f"🌐 API URL: {API_URL}")
    print()

    reader = ACR122UReader()

    # Try to connect to reader
    while not reader.connect():
        print("⏳ Retrying in 5 seconds...")
        time.sleep(5)

    try:
        reader.listen_for_cards()
    except KeyboardInterrupt:
        print("\n👋 RFID reader stopped by user")
    except Exception as e:
        print(f"\n❌ Fatal error: {e}")
    finally:
        if reader.connection:
            try:
                reader.connection.disconnect()
            except:
                pass
        print("🔌 Disconnected from reader")
