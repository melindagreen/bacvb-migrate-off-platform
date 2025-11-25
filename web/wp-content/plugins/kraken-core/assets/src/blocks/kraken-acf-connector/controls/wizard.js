/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import { SelectControl, TextControl, ToggleControl } from "@wordpress/components";
import { useBlockProps } from "@wordpress/block-editor";

/*** CONSTANTS **************************************************************/

/*** FUNCTIONS **************************************************************/

const Wizard = (props) => {
  const { attributes, setAttributes } = props;
  const blockProps = useBlockProps();

  const crmDataOptions = [
    {
      label: __("Hours", "kraken-core"),
      value: "crm_hours",
    },
    {
      label: __("Phone", "kraken-core"),
      value: "crm_phone",
    },
    {
      label: __("Email", "kraken-core"),
      value: "crm_email",
    },
    {
      label: __("Website", "kraken-core"),
      value: "crm_website",
    },
    {
      label: __("Address", "kraken-core"),
      value: "crm_address",
    },
    {
      label: __("Map Embed", "kraken-core"),
      value: "crm_map_embed",
    },
    {
      label: __("Directions Link", "kraken-core"),
      value: "crm_directions",
    },
    {
      label: __("Social Links", "kraken-core"),
      value: "crm_social",
    },
    {
      label: __("Mindtrip", "kraken-core"),
      value: "mindtrip",
    },
  ];

  const eventDataOptions = [
    {
      label: __("Date (Next Occurrence)", "kraken-core"),
      value: "event_dates",
    },
    {
      label: __("Dates (All Upcoming)", "kraken-core"),
      value: "event_recurring_dates",
    },
    {
      label: __("Time(s)", "kraken-core"),
      value: "event_times",
    },
    {
      label: __("Phone", "kraken-core"),
      value: "event_phone",
    },
    {
      label: __("Email", "kraken-core"),
      value: "event_email",
    },
    {
      label: __("Website", "kraken-core"),
      value: "event_website",
    },
    {
      label: __("Location", "kraken-core"),
      value: "event_location",
    },
    {
      label: __("Directions Link", "kraken-core"),
      value: "event_directions",
    },
    {
      label: __("Map Embed", "kraken-core"),
      value: "event_map_embed",
    },
    {
      label: __("Social Links", "kraken-core"),
      value: "event_social",
    },
    {
      label: __("Ticket Price", "kraken-core"),
      value: "event_ticket_price",
    },
    {
      label: __("Ticket Link", "kraken-core"),
      value: "event_ticket_link",
    },
    {
      label: __("Mindtrip", "kraken-core"),
      value: "mindtrip",
    },
  ];

  //this is used to determine if the customize link text field should appear or not
  const presetLinkFields = ["crm_directions", "event_directions", "event_ticket_link", "mindtrip"];

  return (
    <div className={`${blockProps.className}`}>
      <SelectControl
        label={__("Event Data")}
        value={attributes.contentType}
        options={[
          {
            label: __("Kraken CRM", "kraken-core"),
            value: "kraken-crm",
          },
          {
            label: __("Kraken Events", "kraken-core"),
            value: "kraken-events",
          },
          {
            label: __("Custom", "kraken-core"),
            value: "custom",
          },
          {
            label: __("Hook Only", "kraken-core"),
            value: "hook-only",
          },
        ]}
        onChange={(val) => {
          setAttributes({ contentType: val });
          setAttributes({ presetField: "" });
        }}
      />
      {attributes.contentType !== "custom" && (
        <>
          <SelectControl
            label={__("Kraken Presets")}
            value={attributes.presetField}
            options={attributes.contentType === "kraken-events" ? eventDataOptions : crmDataOptions}
            onChange={(val) => {
              setAttributes({ presetField: val });
            }}
          />
          {presetLinkFields.includes(attributes.presetField) && (
            <TextControl
              label="Customize Link Text"
              help="Default text will be used if blank"
              value={attributes.customLinkText}
              onChange={(val) => {
                setAttributes({ customLinkText: val });
              }}
            />
          )}
        </>
      )}
      {(attributes.contentType === "custom" || attributes.contentType === "hook-only") && (
        <TextControl
          label="Custom Data"
          help={
            attributes.contentType === "hook-only"
              ? `Enter the ACF field name to create a hook kraken-core/kraken-acf-connector/hook-only/${
                  attributes.customField || "my_field"
                } to use in your theme`
              : "Enter the ACF field name"
          }
          value={attributes.customField}
          onChange={(val) => {
            setAttributes({ customField: val });
          }}
        />
      )}
      {attributes.contentType !== "custom" && (
        <ToggleControl
          label="Display Icon"
          help="Only applies to some fields. Use hooks to customize the icon"
          checked={attributes.displayIcon}
          onChange={() => {
            setAttributes({ displayIcon: !attributes.displayIcon });
          }}
        />
      )}
      <ToggleControl
        label="Display Label"
        help="Some fields have default labels. Use hooks to customize the html"
        checked={attributes.displayLabel}
        onChange={() => {
          setAttributes({ displayLabel: !attributes.displayLabel });
        }}
      />
      {attributes.displayLabel && (
        <>
          <TextControl
            label="Customize Label Text"
            help="optional; default value or field label will be used"
            value={attributes.customLabelText}
            onChange={(val) => {
              setAttributes({ customLabelText: val });
            }}
          />
        </>
      )}
      {attributes.contentType === "custom" && (
        <>
          <ToggleControl
            label="Output as Link"
            help="Output field value as link; helpful to ouput term links or text fields with phone numbers or email addresses"
            checked={attributes.outputAsLink}
            onChange={() => {
              setAttributes({ outputAsLink: !attributes.outputAsLink });
            }}
          />
          {attributes.outputAsLink && (
            <>
              <TextControl
                label="Link Text"
                help="Defaults to field label if blank"
                value={attributes.customLinkText}
                onChange={(val) => {
                  setAttributes({ customLinkText: val });
                }}
              />
              <SelectControl
                label={__("Link Type")}
                help={__("Prefixes the link with tel: or mailto:")}
                value={attributes.customLinkType}
                options={[
                  {
                    label: __("n/a", "kraken-core"),
                    value: "",
                  },
                  {
                    label: __("Email", "kraken-core"),
                    value: "email",
                  },
                  {
                    label: __("Phone", "kraken-core"),
                    value: "phone",
                  },
                ]}
                onChange={(val) => {
                  setAttributes({ customLinkType: val });
                }}
              />
              <ToggleControl
                label="Open in New Window"
                help="Adds target=_blank to the link"
                checked={attributes.customLinkTarget}
                onChange={() => {
                  setAttributes({ customLinkTarget: !attributes.customLinkTarget });
                }}
              />
            </>
          )}
          <ToggleControl
            label="Output as List"
            help="For taxonomy fields"
            checked={attributes.outputAsList}
            onChange={() => {
              setAttributes({ outputAsList: !attributes.outputAsList });
            }}
          />
        </>
      )}
    </div>
  );
};

/*** EXPORTS ****************************************************************/

export default Wizard;
