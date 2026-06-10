const admin = require('firebase-admin');
const fs = require('fs');
const path = require('path');

const credPath = path.join(__dirname, 'storage', 'app', 'firebase-auth.json');

if (!fs.existsSync(credPath)) {
    console.error(`Error: ${credPath} not found.`);
    console.error("Please run this script from the root directory of the Laravel project.");
    process.exit(1);
}

const serviceAccount = require(credPath);

admin.initializeApp({
    credential: admin.credential.cert(serviceAccount)
});

const db = admin.firestore();

console.log("Cloud Firestore connected successfully!");
console.log("Starting Node.js real-time sensor simulation...");
console.log("Press Ctrl+C to stop.\n");

function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function getRandomFloat(min, max) {
    return parseFloat((Math.random() * (max - min) + min).toFixed(2));
}

async function sendData(penempatan) {
    const isHulu = penempatan === 'hulu';
    const water_level = isHulu ? getRandomInt(40, 160) : getRandomInt(30, 140);
    const water_flow = isHulu ? getRandomInt(10, 45) : getRandomInt(5, 30);
    const ombrometer = isHulu ? 0.0 : getRandomFloat(0, 50);
    const anemometer = isHulu ? 0.0 : getRandomFloat(0, 30);

    // 9 fields are mandatory under the strict Security Rules:
    // anemometer, node_status, ombrometer, penempatan, sirine_status, situs, time, water_level, water_flow
    // 1 field is optional: espcam_img_url
    const payload = {
        anemometer: Number(anemometer),
        ombrometer: Number(ombrometer),
        node_status: true,
        penempatan: penempatan,
        sirine_status: penempatan === 'hilir' ? (water_level > 150) : false,
        situs: "pnj",
        time: admin.firestore.FieldValue.serverTimestamp(),
        water_level: Number(water_level),
        water_flow: Number(water_flow),
        espcam_img_url: "" // Optional field included as empty string
    };

    try {
        const docRef = await db.collection("monitoring")
                               .doc("depok")
                               .collection("log_data")
                               .add(payload);
        console.log(`[${penempatan.toUpperCase()}] Data sent: Lvl=${water_level}cm, Flow=${water_flow}L/min, Rain=${ombrometer}mm/j. ID: ${docRef.id}`);
    } catch (error) {
        console.error("Error sending data to Firestore: ", error);
    }
}

// Loop simulation
const huluInterval = setInterval(() => sendData('hulu'), 3000);
const hilirInterval = setInterval(() => sendData('hilir'), 4500);

process.on('SIGINT', () => {
    clearInterval(huluInterval);
    clearInterval(hilirInterval);
    console.log("\nSimulation stopped.");
    process.exit(0);
});
