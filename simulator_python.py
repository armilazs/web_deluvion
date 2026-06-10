import os
import time
import random
import json
from google.cloud import firestore

# Path to the Firebase service account private key
cred_path = os.path.join("storage", "app", "firebase-auth.json")

if not os.path.exists(cred_path):
    print(f"Error: {cred_path} not found.")
    print("Please make sure you run this script from the root directory of the Laravel project.")
    exit(1)

# Set the environment variable for Google Application Credentials
os.environ["GOOGLE_APPLICATION_CREDENTIALS"] = cred_path

print("Initializing Cloud Firestore...")
db = firestore.Client()

print("Cloud Firestore connected successfully!")
print("Starting real-time simulation...")
print("Press Ctrl+C to stop.")

def send_data(penempatan):
    # Generates random but realistic values for simulation
    if penempatan == "hulu":
        water_level = random.randint(40, 160)
        water_flow = random.randint(10, 45)
        ombrometer = 0.0  # Must be numeric as per Firestore rules
        anemometer = 0.0  # Must be numeric as per Firestore rules
    else:
        water_level = random.randint(30, 140)
        water_flow = random.randint(5, 30)
        ombrometer = round(random.uniform(0.0, 50.0), 2)
        anemometer = round(random.uniform(0.0, 30.0), 2)
    
    # 9 fields are mandatory under the strict Security Rules:
    # anemometer, node_status, ombrometer, penempatan, sirine_status, situs, time, water_level, water_flow
    # 1 field is optional: espcam_img_url
    payload = {
        "anemometer": float(anemometer),
        "ombrometer": float(ombrometer),
        "node_status": True,
        "penempatan": penempatan,
        "sirine_status": water_level > 150 if penempatan == "hilir" else False,
        "situs": "pnj",
        "time": firestore.SERVER_TIMESTAMP,
        "water_level": float(water_level),
        "water_flow": float(water_flow),
        "espcam_img_url": "" # Optional field included as empty string
    }
    
    try:
        # Document write
        doc_ref = db.collection("monitoring").document("depok").collection("log_data").document()
        doc_ref.set(payload)
        print(f"[{penempatan.upper()}] Data sent: Lvl={water_level}cm, Flow={water_flow}L/min, Rain={ombrometer}mm/j. ID: {doc_ref.id}")
    except Exception as e:
        print(f"Error sending data: {e}")

try:
    while True:
        # Send data for Hulu
        send_data("hulu")
        time.sleep(2)
        
        # Send data for Hilir
        send_data("hilir")
        time.sleep(3)
        
except KeyboardInterrupt:
    print("\nSimulation stopped.")
