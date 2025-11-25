import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import './editor.scss';
import ServerSideRender from '@wordpress/server-side-render';
import Controls from "../controls";
import Wizard from "../controls/wizard";
import { initializeEventasticCalendarBlock } from "./calendar";
const Editor = (props) => {
	const {
		attributes,
		className
	} = props;

	const blockProps = useBlockProps();

	return (
		<div className={className} {...blockProps}>
			{attributes.mode === "edit" ? (
				<Wizard {...props} />
			) : (
				<ServerSideRender block={props.name} {...props} />
			)}
		</div>
	);
};

const edit = (props) => {
	return (
		<p { ...useBlockProps() }>
			<Controls {...props} />
			<Editor {...props} />			
		</p>
	);
};

/*** EXPORTS ****************************************************************/

export default edit;
