import 'dart:convert';
import 'dart:math' as math;

import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:timeago/timeago.dart' as timeago;
import 'lat_lng.dart';
import 'place.dart';
import 'uploaded_file.dart';
import '/backend/backend.dart';
import 'package:cloud_firestore/cloud_firestore.dart';
import '/auth/firebase_auth/auth_util.dart';

double computeTotalPrice(
  double? price,
  double? priceSmall,
  double? priceMedium,
  double? priceLarge,
  String size,
  int quantity,
) {
  // gawing maliit ang letters at walang spaces sa gilid
  final s = (size ?? '').toLowerCase().trim();

  double selectedPrice;

  switch (s) {
    case 'small':
      selectedPrice = priceSmall ?? price ?? 0;
      break;
    case 'medium':
      selectedPrice = priceMedium ?? price ?? 0;
      break;
    case 'large':
      selectedPrice = priceLarge ?? price ?? 0;
      break;
    default:
      selectedPrice = price ?? 0;
      break;
  }

  final safeQty = quantity < 0 ? 0 : quantity;

  return selectedPrice * safeQty;
}

double sumCartTotals(List<dynamic> cartItems) {
  double subtotal = 0;

  for (final item in cartItems) {
    if (item is Map<String, dynamic>) {
      final total = item['total'];
      if (total is num) {
        subtotal += total.toDouble();
      }
    }
  }

  return subtotal;
}

double computeOrderTotal(
  double subtotal,
  double deliveryFee,
) {
  return subtotal + deliveryFee;
}
