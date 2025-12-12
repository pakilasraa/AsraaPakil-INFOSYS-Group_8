import '/backend/api_requests/api_calls.dart';
import '/flutter_flow/flutter_flow_choice_chips.dart';
import '/flutter_flow/flutter_flow_icon_button.dart';
import '/flutter_flow/flutter_flow_theme.dart';
import '/flutter_flow/flutter_flow_util.dart';
import '/flutter_flow/flutter_flow_widgets.dart';
import '/flutter_flow/form_field_controller.dart';
import 'dart:ui';
import '/custom_code/actions/index.dart' as actions;
import 'ai_page_widget.dart' show AiPageWidget;
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';

class AiPageModel extends FlutterFlowModel<AiPageWidget> {
  ///  Local state fields for this page.
  /// To store the text field input
  String? budget;

  /// "Coffee", "Non-Coffee", etc.
  List<String> selectedDrinkPrefs = [];
  void addToSelectedDrinkPrefs(String item) => selectedDrinkPrefs.add(item);
  void removeFromSelectedDrinkPrefs(String item) =>
      selectedDrinkPrefs.remove(item);
  void removeAtIndexFromSelectedDrinkPrefs(int index) =>
      selectedDrinkPrefs.removeAt(index);
  void insertAtIndexInSelectedDrinkPrefs(int index, String item) =>
      selectedDrinkPrefs.insert(index, item);
  void updateSelectedDrinkPrefsAtIndex(int index, Function(String) updateFn) =>
      selectedDrinkPrefs[index] = updateFn(selectedDrinkPrefs[index]);

  /// "Pastries", "Sandwiches", etc
  List<String> selectedFoodPrefs = [];
  void addToSelectedFoodPrefs(String item) => selectedFoodPrefs.add(item);
  void removeFromSelectedFoodPrefs(String item) =>
      selectedFoodPrefs.remove(item);
  void removeAtIndexFromSelectedFoodPrefs(int index) =>
      selectedFoodPrefs.removeAt(index);
  void insertAtIndexInSelectedFoodPrefs(int index, String item) =>
      selectedFoodPrefs.insert(index, item);
  void updateSelectedFoodPrefsAtIndex(int index, Function(String) updateFn) =>
      selectedFoodPrefs[index] = updateFn(selectedFoodPrefs[index]);

  /// "Hot" or "Iced"
  String? selectedTemp;

  /// To store the API response
  ///
  List<dynamic> aiRecommendations = [];
  void addToAiRecommendations(dynamic item) => aiRecommendations.add(item);
  void removeFromAiRecommendations(dynamic item) =>
      aiRecommendations.remove(item);
  void removeAtIndexFromAiRecommendations(int index) =>
      aiRecommendations.removeAt(index);
  void insertAtIndexInAiRecommendations(int index, dynamic item) =>
      aiRecommendations.insert(index, item);
  void updateAiRecommendationsAtIndex(int index, Function(dynamic) updateFn) =>
      aiRecommendations[index] = updateFn(aiRecommendations[index]);

  /// For loading spinner state
  ///
  bool isLoading = false;

  bool showFoodPairings = true;

  ///  State fields for stateful widgets in this page.

  // State field(s) for TextField widget.
  FocusNode? textFieldFocusNode;
  TextEditingController? textController;
  String? Function(BuildContext, String?)? textControllerValidator;
  // State field(s) for ChoiceChips widget.
  FormFieldController<List<String>>? choiceChipsValueController1;
  List<String>? get choiceChipsValues1 => choiceChipsValueController1?.value;
  set choiceChipsValues1(List<String>? val) =>
      choiceChipsValueController1?.value = val;
  // State field(s) for ChoiceChips widget.
  FormFieldController<List<String>>? choiceChipsValueController2;
  String? get choiceChipsValue2 =>
      choiceChipsValueController2?.value?.firstOrNull;
  set choiceChipsValue2(String? val) =>
      choiceChipsValueController2?.value = val != null ? [val] : [];
  // State field(s) for ChoiceChips widget.
  FormFieldController<List<String>>? choiceChipsValueController3;
  String? get choiceChipsValue3 =>
      choiceChipsValueController3?.value?.firstOrNull;
  set choiceChipsValue3(String? val) =>
      choiceChipsValueController3?.value = val != null ? [val] : [];
  // Stores action output result for [Custom Action - buildPreferenceString] action in Button widget.
  String? generatedPrompt;
  // Stores action output result for [Backend Call - API (GetAiRecommendationsx)] action in Button widget.
  ApiCallResponse? apiResult;

  @override
  void initState(BuildContext context) {}

  @override
  void dispose() {
    textFieldFocusNode?.dispose();
    textController?.dispose();
  }
}
