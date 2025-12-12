import '/backend/api_requests/api_calls.dart';
import '/components/addedto_cart_comp_widget.dart';
import '/flutter_flow/flutter_flow_icon_button.dart';
import '/flutter_flow/flutter_flow_theme.dart';
import '/flutter_flow/flutter_flow_util.dart';
import '/flutter_flow/flutter_flow_widgets.dart';
import 'dart:ui';
import '/flutter_flow/custom_functions.dart' as functions;
import '/index.dart';
import 'item_detail_page_widget.dart' show ItemDetailPageWidget;
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';

class ItemDetailPageModel extends FlutterFlowModel<ItemDetailPageWidget> {
  ///  Local state fields for this page.

  int qty = 1;

  double unitPrice = 0.0;

  double total = 0.0;

  String? selectedSize;

  @override
  void initState(BuildContext context) {}

  @override
  void dispose() {}
}
