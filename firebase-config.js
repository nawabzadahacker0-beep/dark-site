import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
import { getAuth, signInWithEmailAndPassword, createUserWithEmailAndPassword } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";
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
const auth = getAuth(app);
const db = getFirestore(app);

async function syncUserToFirebase(userData) {
  try {
    if (!userData) return false;
    const docId = userData.email || userData.username || String(Date.now());
    const userRef = doc(db, "users", docId);
    await setDoc(userRef, {
      username: userData.username || "",
      email: userData.email || "",
      phone: userData.phone || "",
      coins: userData.coins || 0,
      lastLogin: serverTimestamp()
    }, { merge: true });
    
    console.log("Data Firebase mein successfully save ho gaya!");
    return true;
  } catch (error) {
    console.warn("Firebase Sync Warning (Proceeding with PHP Login):", error);
    return false;
  }
}

export { auth, db, syncUserToFirebase, signInWithEmailAndPassword, createUserWithEmailAndPassword };
