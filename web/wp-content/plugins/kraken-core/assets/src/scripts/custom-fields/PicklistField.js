// PicklistField.js
import {
  BaseControl,
  Button,
  Flex,
  FlexItem,
  __experimentalText as Text,
  __experimentalHStack as HStack,
  __experimentalVStack as VStack,
} from "@wordpress/components";
import { useState, useMemo, useCallback } from "@wordpress/element";
import { useBaseControlProps } from "@wordpress/components";

const PicklistField = ({ field, value, settingsOption, onChange }) => {
  const { class: className, disabled, id, choices } = field;
  const { baseControlProps } = useBaseControlProps(field);

  // `value` is the list of DISABLED ids (to preserve existing storage).
  const disabledIds = useMemo(() => (Array.isArray(value) ? value : []), [value]);
  const allIds = useMemo(() => Object.keys(choices || {}), [choices]);

  const enabledIds = useMemo(
    () => allIds.filter((k) => !disabledIds.includes(k)),
    [allIds, disabledIds],
  );

  // Local UI selection state
  const [selectedEnabled, setSelectedEnabled] = useState([]);
  const [selectedDisabled, setSelectedDisabled] = useState([]);

  const commit = useCallback(
    (nextDisabled) => {
      onChange({ id, value: nextDisabled, settingsOption });
    },
    [id, onChange, settingsOption],
  );

  const moveToDisabled = () => {
    if (disabled || selectedEnabled.length === 0) return;
    const nextDisabled = Array.from(new Set([...disabledIds, ...selectedEnabled]));
    commit(nextDisabled);
    setSelectedEnabled([]);
  };

  const moveToEnabled = () => {
    if (disabled || selectedDisabled.length === 0) return;
    const nextDisabled = disabledIds.filter((k) => !selectedDisabled.includes(k));
    commit(nextDisabled);
    setSelectedDisabled([]);
  };

  const toggleFromEnabled = (id) => {
    setSelectedEnabled((sel) => (sel.includes(id) ? sel.filter((x) => x !== id) : [...sel, id]));
  };

  const toggleFromDisabled = (id) => {
    setSelectedDisabled((sel) => (sel.includes(id) ? sel.filter((x) => x !== id) : [...sel, id]));
  };

  const onDoubleClickEnabled = (id) => {
    const nextDisabled = Array.from(new Set([...disabledIds, id]));
    commit(nextDisabled);
    setSelectedEnabled((sel) => sel.filter((x) => x !== id));
  };

  const onDoubleClickDisabled = (id) => {
    const nextDisabled = disabledIds.filter((k) => k !== id);
    commit(nextDisabled);
    setSelectedDisabled((sel) => sel.filter((x) => x !== id));
  };

  const List = ({ ids, selected, onToggle, onDoubleClick, ariaLabel }) => (
    <VStack
      as="ul"
      spacing={1}
      style={{
        listStyle: "none",
        margin: 0,
        padding: "8px",
        border: "1px solid var(--wp-components-color-gray-200, #ddd)",
        borderRadius: "8px",
        minHeight: "200px",
        overflowY: "auto",
      }}
      role="listbox"
      aria-label={ariaLabel}
      aria-multiselectable="true"
    >
      {ids.map((k) => {
        const isSelected = selected.includes(k);
        return (
          <li key={k}>
            <Button
              variant={isSelected ? "primary" : "secondary"}
              isPressed={isSelected}
              onClick={() => onToggle(k)}
              onDoubleClick={() => onDoubleClick(k)}
              disabled={disabled}
              __next40pxDefaultSize
              style={{ width: "100%", justifyContent: "space-between" }}
            >
              <Text as="span">{choices[k]}</Text>
            </Button>
          </li>
        );
      })}
      {ids.length === 0 && <Text variant="muted">—</Text>}
    </VStack>
  );

  return (
    <BaseControl {...baseControlProps} __nextHasNoMarginBottom className={`picklist ${className}`}>
      <VStack spacing={2}>
        <HStack alignment="top" spacing={3} style={{ width: "100%" }}>
          {/* Enabled column */}
          <Flex direction="column" style={{ flex: 1, minWidth: 0 }}>
            <FlexItem>
              <Text as="div" style={{ fontWeight: 600, marginBottom: 6 }}>
                Enabled
              </Text>
            </FlexItem>
            <FlexItem>
              <List
                ids={enabledIds}
                selected={selectedEnabled}
                onToggle={toggleFromEnabled}
                onDoubleClick={onDoubleClickEnabled}
                ariaLabel="Enabled items"
              />
            </FlexItem>
          </Flex>

          {/* Middle controls */}
          <Flex
            direction="column"
            gap={2}
            align="center"
            justify="center"
            style={{ paddingTop: "35px", position: "sticky", top: "20px" }}
          >
            <Button
              variant="primary"
              onClick={moveToDisabled}
              disabled={disabled || selectedEnabled.length === 0}
              __next40pxDefaultSize
            >
              Move to Disabled →
            </Button>
            <Button
              variant="secondary"
              onClick={moveToEnabled}
              disabled={disabled || selectedDisabled.length === 0}
              __next40pxDefaultSize
            >
              ← Move to Enabled
            </Button>
          </Flex>

          {/* Disabled column */}
          <Flex direction="column" style={{ flex: 1, minWidth: 0 }}>
            <FlexItem>
              <Text as="div" style={{ fontWeight: 600, marginBottom: 6 }}>
                Disabled
              </Text>
            </FlexItem>
            <FlexItem>
              <List
                ids={disabledIds}
                selected={selectedDisabled}
                onToggle={toggleFromDisabled}
                onDoubleClick={onDoubleClickDisabled}
                ariaLabel="Disabled items"
              />
            </FlexItem>
          </Flex>
        </HStack>
      </VStack>
    </BaseControl>
  );
};

export default PicklistField;
