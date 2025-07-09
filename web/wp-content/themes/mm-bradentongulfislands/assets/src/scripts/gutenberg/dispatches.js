/*** EXPORTS ****************************************************************/
export default () => {
	//Remove ability to enable/disable comments
	const editorStore = wp.data.select("core/editor");

	if (editorStore) {
		wp.data.dispatch("core/editor").removeEditorPanel("discussion-panel");
	}
};
