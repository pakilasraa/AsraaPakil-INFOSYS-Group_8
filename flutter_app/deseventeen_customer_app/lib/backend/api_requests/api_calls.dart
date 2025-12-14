import 'dart:convert';
import 'dart:typed_data';
import 'package:flutter/foundation.dart';

import '/flutter_flow/flutter_flow_util.dart';
import 'api_manager.dart';

export 'api_manager.dart' show ApiCallResponse;

const _kPrivateApiFunctionName = 'ffPrivateApiCall';

class GetProductsCall {
  static Future<ApiCallResponse> call({
    String? category = '',
    String? search = '',
  }) async {
    return ApiManager.instance.makeApiCall(
      callName: 'GetProducts',
      apiUrl: 'https://unadjustably-unwadable-nikia.ngrok-free.dev/api/products',
      callType: ApiCallType.GET,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'ngrok-skip-browser-warning': 'true',
      },
      params: {
        'category_name': category,
        'search': search,
      },
      returnBody: true,
      encodeBodyUtf8: false,
      decodeUtf8: false,
      cache: false,
      isStreamingApi: false,
      alwaysAllowBody: false,
    );
  }

  static List<int>? id(dynamic response) => (getJsonField(
        response,
        r'''$[:].id''',
        true,
      ) as List?)
          ?.withoutNulls
          .map((x) => castToType<int>(x))
          .withoutNulls
          .toList();
  static List<String>? name(dynamic response) => (getJsonField(
        response,
        r'''$[:].name''',
        true,
      ) as List?)
          ?.withoutNulls
          .map((x) => castToType<String>(x))
          .withoutNulls
          .toList();
  static List<int>? categoryId(dynamic response) => (getJsonField(
        response,
        r'''$[:].category_id''',
        true,
      ) as List?)
          ?.withoutNulls
          .map((x) => castToType<int>(x))
          .withoutNulls
          .toList();
  static List<String>? price(dynamic response) => (getJsonField(
        response,
        r'''$[:].price''',
        true,
      ) as List?)
          ?.withoutNulls
          .map((x) => castToType<String>(x))
          .withoutNulls
          .toList();
  static List<String>? priceSmall(dynamic response) => (getJsonField(
        response,
        r'''$[:].price_small''',
        true,
      ) as List?)
          ?.withoutNulls
          .map((x) => castToType<String>(x))
          .withoutNulls
          .toList();
  static List<String>? priceMedium(dynamic response) => (getJsonField(
        response,
        r'''$[:].price_medium''',
        true,
      ) as List?)
          ?.withoutNulls
          .map((x) => castToType<String>(x))
          .withoutNulls
          .toList();
  static List<String>? priceLarge(dynamic response) => (getJsonField(
        response,
        r'''$[:].price_large''',
        true,
      ) as List?)
          ?.withoutNulls
          .map((x) => castToType<String>(x))
          .withoutNulls
          .toList();
  static List<String>? imageUrl(dynamic response) => (getJsonField(
        response,
        r'''$[:].image_url''',
        true,
      ) as List?)
          ?.withoutNulls
          .map((x) => castToType<String>(x))
          .withoutNulls
          .toList();
  static List<String>? description(dynamic response) => (getJsonField(
        response,
        r'''$[:].description''',
        true,
      ) as List?)
          ?.withoutNulls
          .map((x) => castToType<String>(x))
          .withoutNulls
          .toList();
  static List<String>? categoryName(dynamic response) => (getJsonField(
        response,
        r'''$[:].category_name''',
        true,
      ) as List?)
          ?.withoutNulls
          .map((x) => castToType<String>(x))
          .withoutNulls
          .toList();
  static List<int>? isDrink(dynamic response) => (getJsonField(
        response,
        r'''$[:].is_drink''',
        true,
      ) as List?)
          ?.withoutNulls
          .map((x) => castToType<int>(x))
          .withoutNulls
          .toList();
}

class SyncCustomerCall {
  static Future<ApiCallResponse> call({
    String? firebaseUid = '',
    String? email = '',
    String? name = '',
    String? phone = '',
  }) async {
    final ffApiRequestBody = '''
{
  "firebase_uid": "${escapeStringForJson(firebaseUid)}",
  "email": "${escapeStringForJson(email)}",
  "name": "${escapeStringForJson(name)}",
  "phone": "${escapeStringForJson(phone)}"
}''';
    return ApiManager.instance.makeApiCall(
      callName: 'SyncCustomer',
      apiUrl: 'https://unadjustably-unwadable-nikia.ngrok-free.dev/api/customers/sync',
      callType: ApiCallType.POST,
      headers: {
        'Content-Type': 'application/json',
      },
      params: {},
      body: ffApiRequestBody,
      bodyType: BodyType.JSON,
      returnBody: true,
      encodeBodyUtf8: false,
      decodeUtf8: false,
      cache: false,
      isStreamingApi: false,
      alwaysAllowBody: false,
    );
  }

  static int? customerId(dynamic response) => castToType<int>(getJsonField(
        response,
        r'''$.customer_id''',
      ));
  static String? email(dynamic response) => castToType<String>(getJsonField(
        response,
        r'''$.email''',
      ));
  static String? name(dynamic response) => castToType<String>(getJsonField(
        response,
        r'''$.name''',
      ));
}

class PingApiCall {
  static Future<ApiCallResponse> call() async {
    return ApiManager.instance.makeApiCall(
      callName: 'PingApi',
      apiUrl: 'https://unadjustably-unwadable-nikia.ngrok-free.dev/api/ping',
      callType: ApiCallType.GET,
      headers: {},
      params: {},
      returnBody: true,
      encodeBodyUtf8: false,
      decodeUtf8: false,
      cache: false,
      isStreamingApi: false,
      alwaysAllowBody: false,
    );
  }
}

class PlaceOrderCall {
  static Future<ApiCallResponse> call({
    String? jsonBody = '',
  }) async {
    final ffApiRequestBody = '''
"${jsonBody}"''';
    return ApiManager.instance.makeApiCall(
      callName: 'PlaceOrder',
      apiUrl: 'https://unadjustably-unwadable-nikia.ngrok-free.dev/api/orders',
      callType: ApiCallType.POST,
      headers: {
        'Authorization': '{{ currentUserJwtToken }}',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      params: {},
      body: ffApiRequestBody,
      bodyType: BodyType.TEXT,
      returnBody: true,
      encodeBodyUtf8: false,
      decodeUtf8: false,
      cache: false,
      isStreamingApi: false,
      alwaysAllowBody: false,
    );
  }
}

class GetCartItemsCall {
  static Future<ApiCallResponse> call() async {
    return ApiManager.instance.makeApiCall(
      callName: 'GetCartItems',
      apiUrl: 'https://unadjustably-unwadable-nikia.ngrok-free.dev/api/cart',
      callType: ApiCallType.GET,
      headers: {},
      params: {},
      returnBody: true,
      encodeBodyUtf8: false,
      decodeUtf8: false,
      cache: false,
      isStreamingApi: false,
      alwaysAllowBody: false,
    );
  }
}

class OrderHistoryCall {
  static Future<ApiCallResponse> call() async {
    return ApiManager.instance.makeApiCall(
      callName: 'OrderHistory',
      apiUrl: 'https://unadjustably-unwadable-nikia.ngrok-free.dev/api/orders/history',
      callType: ApiCallType.GET,
      headers: {
        'Authorization': '<Firebase ID Token>',
      },
      params: {},
      returnBody: true,
      encodeBodyUtf8: false,
      decodeUtf8: false,
      cache: false,
      isStreamingApi: false,
      alwaysAllowBody: false,
    );
  }
}

class GetAiStockRecommendationsxCall {
  static Future<ApiCallResponse> call({
    String? menuContext = '',
    String? userPreferences = '',
    String? userBudget = '',
  }) async {
    final ffApiRequestBody = '''
{
  "menu_context": "${escapeStringForJson(menuContext)}",
  "preferences": "${escapeStringForJson(userPreferences)}",
  "budget": "${escapeStringForJson(userBudget)}"
}''';
    return ApiManager.instance.makeApiCall(
      callName: 'GetAiStockRecommendationsx',
      apiUrl: 'http://localhost:5678/webhook-test/recommend-products',
      callType: ApiCallType.POST,
      headers: {
        'Content-Type': 'application/json',
      },
      params: {},
      body: ffApiRequestBody,
      bodyType: BodyType.JSON,
      returnBody: true,
      encodeBodyUtf8: true,
      decodeUtf8: false,
      cache: false,
      isStreamingApi: false,
      alwaysAllowBody: false,
    );
  }
}

class ApiPagingParams {
  int nextPageNumber = 0;
  int numItems = 0;
  dynamic lastResponse;

  ApiPagingParams({
    required this.nextPageNumber,
    required this.numItems,
    required this.lastResponse,
  });

  @override
  String toString() =>
      'PagingParams(nextPageNumber: $nextPageNumber, numItems: $numItems, lastResponse: $lastResponse,)';
}

String _toEncodable(dynamic item) {
  if (item is DocumentReference) {
    return item.path;
  }
  return item;
}

String _serializeList(List? list) {
  list ??= <String>[];
  try {
    return json.encode(list, toEncodable: _toEncodable);
  } catch (_) {
    if (kDebugMode) {
      print("List serialization failed. Returning empty list.");
    }
    return '[]';
  }
}

String _serializeJson(dynamic jsonVar, [bool isList = false]) {
  jsonVar ??= (isList ? [] : {});
  try {
    return json.encode(jsonVar, toEncodable: _toEncodable);
  } catch (_) {
    if (kDebugMode) {
      print("Json serialization failed. Returning empty json.");
    }
    return isList ? '[]' : '{}';
  }
}

String? escapeStringForJson(String? input) {
  if (input == null) {
    return null;
  }
  return input
      .replaceAll('\\', '\\\\')
      .replaceAll('"', '\\"')
      .replaceAll('\n', '\\n')
      .replaceAll('\t', '\\t');
}
