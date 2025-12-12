// Automatic FlutterFlow imports
import '/backend/backend.dart';
import '/flutter_flow/flutter_flow_theme.dart';
import '/flutter_flow/flutter_flow_util.dart';
import 'index.dart'; // Imports other custom actions
import '/flutter_flow/custom_functions.dart'; // Imports custom functions
import 'package:flutter/material.dart';
// Begin custom action code
// DO NOT REMOVE OR MODIFY THE CODE ABOVE!

//

String buildPreferenceString(String? budget, List<String> drinkPrefs,
    List<String> foodPrefs, String? temperature) {
  List<String> parts = [];
  if (budget != null && budget.isNotEmpty) {
    parts.add("My budget is $budget.");
  }

  if (temperature != null && temperature.isNotEmpty) {
    parts.add("I prefer $temperature drinks.");
  }
  if (drinkPrefs.isNotEmpty) {
    parts.add("I want these types of drinks: ${drinkPrefs.join(', ')}.");
  }
  if (foodPrefs.isNotEmpty) {
    parts.add("I want to eat: ${foodPrefs.join(', ')}.");
  }
  // Combine all parts
  return parts.join(' ');
}

// Set your action name, define your arguments and return parameter,
// and then add the boilerplate code using the green button on the right!
