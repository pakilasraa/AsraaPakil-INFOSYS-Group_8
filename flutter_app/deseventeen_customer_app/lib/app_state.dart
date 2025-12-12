import 'package:flutter/material.dart';
import '/backend/backend.dart';
import '/backend/api_requests/api_manager.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'flutter_flow/flutter_flow_util.dart';
import 'dart:convert';

class FFAppState extends ChangeNotifier {
  static FFAppState _instance = FFAppState._internal();

  factory FFAppState() {
    return _instance;
  }

  FFAppState._internal();

  static void reset() {
    _instance = FFAppState._internal();
  }

  Future initializePersistedState() async {}

  void update(VoidCallback callback) {
    callback();
    notifyListeners();
  }

  String _authToken = '';
  String get authToken => _authToken;
  set authToken(String value) {
    _authToken = value;
  }

  String _userName = '';
  String get userName => _userName;
  set userName(String value) {
    _userName = value;
  }

  String _userEmail = '';
  String get userEmail => _userEmail;
  set userEmail(String value) {
    _userEmail = value;
  }

  int _userId = 0;
  int get userId => _userId;
  set userId(int value) {
    _userId = value;
  }

  dynamic _selectedProduct;
  dynamic get selectedProduct => _selectedProduct;
  set selectedProduct(dynamic value) {
    _selectedProduct = value;
  }

  dynamic _cartItems;
  dynamic get cartItems => _cartItems;
  set cartItems(dynamic value) {
    _cartItems = value;
  }

  List<dynamic> _productList = [
    jsonDecode(
        '{\"name\":\"Classic Cappuccino\",\"price\":75,\"image\":\"https://your-image-url.com/cappuccino.png\",\"category\":\"Coffee\"}')
  ];
  List<dynamic> get productList => _productList;
  set productList(List<dynamic> value) {
    _productList = value;
  }

  void addToProductList(dynamic value) {
    productList.add(value);
  }

  void removeFromProductList(dynamic value) {
    productList.remove(value);
  }

  void removeAtIndexFromProductList(int index) {
    productList.removeAt(index);
  }

  void updateProductListAtIndex(
    int index,
    dynamic Function(dynamic) updateFn,
  ) {
    productList[index] = updateFn(_productList[index]);
  }

  void insertAtIndexInProductList(int index, dynamic value) {
    productList.insert(index, value);
  }

  List<dynamic> _orderList = [];
  List<dynamic> get orderList => _orderList;
  set orderList(List<dynamic> value) {
    _orderList = value;
  }

  void addToOrderList(dynamic value) {
    orderList.add(value);
  }

  void removeFromOrderList(dynamic value) {
    orderList.remove(value);
  }

  void removeAtIndexFromOrderList(int index) {
    orderList.removeAt(index);
  }

  void updateOrderListAtIndex(
    int index,
    dynamic Function(dynamic) updateFn,
  ) {
    orderList[index] = updateFn(_orderList[index]);
  }

  void insertAtIndexInOrderList(int index, dynamic value) {
    orderList.insert(index, value);
  }

  List<dynamic> _cartList = [];
  List<dynamic> get cartList => _cartList;
  set cartList(List<dynamic> value) {
    _cartList = value;
  }

  void addToCartList(dynamic value) {
    cartList.add(value);
  }

  void removeFromCartList(dynamic value) {
    cartList.remove(value);
  }

  void removeAtIndexFromCartList(int index) {
    cartList.removeAt(index);
  }

  void updateCartListAtIndex(
    int index,
    dynamic Function(dynamic) updateFn,
  ) {
    cartList[index] = updateFn(_cartList[index]);
  }

  void insertAtIndexInCartList(int index, dynamic value) {
    cartList.insert(index, value);
  }

  int _appCustomerId = 0;
  int get appCustomerId => _appCustomerId;
  set appCustomerId(int value) {
    _appCustomerId = value;
  }

  List<dynamic> _Cart = [jsonDecode('{}')];
  List<dynamic> get Cart => _Cart;
  set Cart(List<dynamic> value) {
    _Cart = value;
  }

  void addToCart(dynamic value) {
    Cart.add(value);
  }

  void removeFromCart(dynamic value) {
    Cart.remove(value);
  }

  void removeAtIndexFromCart(int index) {
    Cart.removeAt(index);
  }

  void updateCartAtIndex(
    int index,
    dynamic Function(dynamic) updateFn,
  ) {
    Cart[index] = updateFn(_Cart[index]);
  }

  void insertAtIndexInCart(int index, dynamic value) {
    Cart.insert(index, value);
  }

  double _cartSubtotal = 0.0;
  double get cartSubtotal => _cartSubtotal;
  set cartSubtotal(double value) {
    _cartSubtotal = value;
  }

  int _customerId = 0;
  int get customerId => _customerId;
  set customerId(int value) {
    _customerId = value;
  }

  String _orderType = '';
  String get orderType => _orderType;
  set orderType(String value) {
    _orderType = value;
  }

  String _customerName = '';
  String get customerName => _customerName;
  set customerName(String value) {
    _customerName = value;
  }

  String _customerPhone = '';
  String get customerPhone => _customerPhone;
  set customerPhone(String value) {
    _customerPhone = value;
  }

  String _customerNotes = '';
  String get customerNotes => _customerNotes;
  set customerNotes(String value) {
    _customerNotes = value;
  }

  List<dynamic> _orderHistory = [];
  List<dynamic> get orderHistory => _orderHistory;
  set orderHistory(List<dynamic> value) {
    _orderHistory = value;
  }

  void addToOrderHistory(dynamic value) {
    orderHistory.add(value);
  }

  void removeFromOrderHistory(dynamic value) {
    orderHistory.remove(value);
  }

  void removeAtIndexFromOrderHistory(int index) {
    orderHistory.removeAt(index);
  }

  void updateOrderHistoryAtIndex(
    int index,
    dynamic Function(dynamic) updateFn,
  ) {
    orderHistory[index] = updateFn(_orderHistory[index]);
  }

  void insertAtIndexInOrderHistory(int index, dynamic value) {
    orderHistory.insert(index, value);
  }

  String _category = '';
  String get category => _category;
  set category(String value) {
    _category = value;
  }

  double _cartTotal = 0.0;
  double get cartTotal => _cartTotal;
  set cartTotal(double value) {
    _cartTotal = value;
  }

  String _search = '';
  String get search => _search;
  set search(String value) {
    _search = value;
  }
}
