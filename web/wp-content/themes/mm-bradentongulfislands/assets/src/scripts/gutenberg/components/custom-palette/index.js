import { ColorPalette } from "@wordpress/components";

// WordPress dependencies

const CustomPalette = ({ colors, value, onChange, className }) => {
  return (
    <ColorPalette
      className={className}
      colors={colors}
      value={value}
      onChange={onChange}
      disableCustomColors
    />
  );
};

export default CustomPalette;
