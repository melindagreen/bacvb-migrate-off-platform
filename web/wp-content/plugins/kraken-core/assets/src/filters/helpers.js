export const getCardStyles = () => {
  const blockData = KrakenThemeSettings.blockData;
  const themeCardStyles = blockData.cardStyles || {};
  const addStyles = themeCardStyles.add || [];
  const removeStyles = themeCardStyles.remove || [];

  let cardStyles = [
    { label: "Default", value: "default" },
    { label: "Overlay", value: "overlay" },
    { label: "Overlay Partial", value: "overlay-partial" },
    { label: "Text Only", value: "text-only" },
  ];

  // Remove any styles whose value matches one in removeStyles
  cardStyles = cardStyles.filter((style) => !removeStyles.includes(style.value));

  // Append additional styles
  addStyles.forEach((item) => {
    cardStyles.push(item);
  });

  return cardStyles;
};

export const getIgnoredPostTypes = () => {
  const blockData = KrakenThemeSettings.blockData;
  const themeIgnoredPostTypes = blockData.ignoredPostTypes || {};
  const addPostTypes = themeIgnoredPostTypes.add || [];
  const removePostTypes = themeIgnoredPostTypes.remove || [];

  let ignoredPostTypes = [
    "attachment",
    "nav_menu_item",
    "wp_block",
    "wp_font_family",
    "wp_font_face",
    "wp_global_styles",
    "wp_template",
    "wp_template_part",
    "wp_navigation",
    "rm_content_editor",
    "rank_math_schema",
  ];

  // Remove ignored post types
  ignoredPostTypes = ignoredPostTypes.filter((postType) => !removePostTypes.includes(postType));

  // Append additional ignored post types
  addPostTypes.forEach((postType) => {
    ignoredPostTypes.push(postType);
  });

  return ignoredPostTypes;
};

// Re-export dynamic attribute functions for easier access
export {
  getDynamicAttributeSettings,
  renderDynamicControls,
  cleanupDynamicAttributes,
} from "./content-card/dynamic-attributes";
