// firebase-config.js
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
import { 
    getAuth, 
    createUserWithEmailAndPassword, 
    signInWithEmailAndPassword 
} from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";
import { 
    getFirestore, 
    doc, 
    setDoc, 
    getDoc, 
    serverTimestamp 
} from "https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js";

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

// Save or Update User in Firestore
async function saveUserToFirestore(uid, userData) {
  try {
    const userRef = doc(db, "users", uid);
    await setDoc(userRef, {
      ...userData,
      lastLogin: serverTimestamp()
    }, { merge: true });
    return true;
  } catch (error) {
    console.error("Firestore Error:", error);
    return false;
  }
}

// Get User Profile
async function getUserFromFirestore(uid) {
  try {
    const userRef = doc(db, "users", uid);
    const snap = await getDoc(userRef);
    if (snap.exists()) {
      return snap.data();
    }
  } catch (error) {
    console.error("Fetch User Error:", error);
  }
  return null;
}

export { 
    auth, 
    db, 
    createUserWithEmailAndPassword, 
    signInWithEmailAndPassword, 
    saveUserToFirestore, 
    getUserFromFirestore 
};
