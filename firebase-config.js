// firebase-config.js
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
import { getFirestore, doc, setDoc, serverTimestamp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js";

const firebaseConfig = {
  apiKey: "AIzaSyB25VaErJEsI3VLBeb52cpczKRmEWC4fEs",
  authDomain: "pak-earning-site.firebaseapp.com",
  databaseURL: "https://pak-earning-site-default-rtdb.firebaseio.com",
  projectId: "pak-earning-site",
  storageBucket: "pak-earning-site.firebasestorage.app",
  messagingSenderId: "830671389706",
  appId: "1:830671389706:web:16ab555ffdd85cff70cbf3",
  measurementId: "G-0CBTF67JM6"
};

const app = initializeApp(firebaseConfig);
const db = getFirestore(app);

async function syncUserToFirebase(userData) {
  try {
    if (!userData) return false;
    const docKey = userData.email || userData.username || "user_" + Date.now();
    const userRef = doc(db, "users", docKey);
    await setDoc(userRef, {
      username: userData.username || "",
      email: userData.email || "",
      phone: userData.phone || "",
      coins: userData.coins || 0,
      lastLogin: serverTimestamp()
    }, { merge: true });
    return true;
  } catch (error) {
    console.warn("Firebase sync skipped/failed:", error);
    return false;
  }
}

export { db, syncUserToFirebase };
