import { __ } from "@wordpress/i18n";
import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps } from "@wordpress/block-editor";
import Controls from "./controls";

const Editor = (props) => {
	const blockProps = useBlockProps();
	return (
		<div {...blockProps}>
			<ServerSideRender block={props.name} {...props} />
		</div>
	);
};

const edit = (props) => {
	return (
		<>
			<Controls {...props} />
			<Editor {...props} />
		</>
	);
};

export default edit;