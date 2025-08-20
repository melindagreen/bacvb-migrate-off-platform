export const THEME_PREFIX = "madden-theme";

export const POST_TYPES_TO_IGNORE = [
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

//Add more styles as needed; these values will be loaded on all blocks that import the Content Card
export const CARD_STYLES = [
	{ label: "Default", value: "default" },
	{ label: "Overlay", value: "overlay" },
	{ label: "Overlay Partial", value: "overlay-partial" },
];
