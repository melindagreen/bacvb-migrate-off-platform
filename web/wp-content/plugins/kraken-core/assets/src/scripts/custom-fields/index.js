// customFields.js
import PicklistField from "./PicklistField";
import "./index.scss";
//import AnotherCustomField from "./custom-fields/AnotherCustomField";
// ... import other custom fields

// Access the namespace
const { registerComponent } = window.mtphrSettingsRegistry || {};

// Check if the namespace is available
if (registerComponent) {
  // Register custom components
  registerComponent("picklist", PicklistField);
  //registerComponent("another_custom_field", AnotherCustomField);
  // ... register other custom fields
} else {
  console.error(`mtphrSettingsRegistry is not available.`);
}
