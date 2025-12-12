import 'package:firebase_core/firebase_core.dart';
import 'package:flutter/foundation.dart';

Future initFirebase() async {
  if (kIsWeb) {
    await Firebase.initializeApp(
        options: FirebaseOptions(
            apiKey: "AIzaSyBr8Tj-YA7XDjwa71GDUtZkZryhRWaDlsQ",
            authDomain: "deseventeencustomerapp.firebaseapp.com",
            projectId: "deseventeencustomerapp",
            storageBucket: "deseventeencustomerapp.firebasestorage.app",
            messagingSenderId: "814820377164",
            appId: "1:814820377164:web:badba502c071ceff55eae9",
            measurementId: "G-TL6JVPRPLL"));
  } else {
    await Firebase.initializeApp();
  }
}
