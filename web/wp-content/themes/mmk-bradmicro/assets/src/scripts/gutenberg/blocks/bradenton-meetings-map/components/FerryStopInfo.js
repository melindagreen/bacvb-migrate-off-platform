import React, { useState, useEffect } from "react";
import { PanelBody, TextareaControl, TextControl } from "@wordpress/components";

const FerryStop = ({ attributes, setAttributes }) => {
	const [localAttributes, setLocalAttributes] = useState(attributes);

	// Update local state when props change
	useEffect(() => {
		setLocalAttributes(attributes);
	}, [attributes]);

	const updateAttributes = (newAttributes) => {
		const updatedAttributes = { ...localAttributes, ...newAttributes };
		setLocalAttributes(updatedAttributes);
		setAttributes(updatedAttributes);
	};

	const ferryStops = {
		annaMariaPier: "Anna Maria Pier",
		bishopMuseum: "Bishop Museum",
		bradentonConventionCenter: "Bradenton Convention Center",
		bradentonRiverwalk: "Bradenton Riverwalk",
		bridgeStFerryStop: "Bridge St Ferry Stop",
		coquinaBeach: "Coquina Beach",
		cortez: "Cortez",
		deSotoMemorial: "DeSoto Memorial",
		egmontKey: "Egmont Key",
		ellentonOutlets: "Ellenton Outlets",
		gulfIslandsFerry: "Gulf Islands Ferry",
		herrigCenterArts: "Herrig Center Arts",
		imgAcademy: "IMG Academy",
		lakeManateeStatePark: "Lake Manatee State Park",
		lakewoodRanchMainSt: "Lakewood Ranch Main St",
		lecomPark: "Lecom Park",
		manateeBeach: "Manatee Beach",
		manateePerformingArtsCenter: "Manatee Performing Arts Center",
		pineAvenue: "Pine Avenue",
		powelCrosleyEstate: "Powel Crosley Estate",
		robinsonPreserve: "Robinson Preserve",
		shoppesUniversityCenter: "Shoppes University Center",
		srqAirport: "SRQ Airport",
		sunshineSkywayBridge: "Sunshine Skyway Bridge",
		tampaInternationalAirport: "Tampa International Airport",
		villageOfArts: "Village of Arts",
	};

	return (
		<PanelBody title="City Info" initialOpen={true}>
			{Object.entries(ferryStops).map(([key, label]) => (
				<div key={key} style={{ marginBottom: "2rem" }}>
					<TextControl
						label={`${label} Title`}
						value={localAttributes[`${key}Title`] || ""}
						onChange={(value) => updateAttributes({ [`${key}Title`]: value })}
					/>
					<TextControl
						label={`${label} Url`}
						value={localAttributes[`${key}Url`] || ""}
						onChange={(value) => updateAttributes({ [`${key}Url`]: value })}
					/>
					<TextareaControl
						label={`${label} Description`}
						value={localAttributes[`${key}Description`] || ""}
						onChange={(value) =>
							updateAttributes({ [`${key}Description`]: value })
						}
					/>
				</div>
			))}
		</PanelBody>
	);
};

export default FerryStop;
