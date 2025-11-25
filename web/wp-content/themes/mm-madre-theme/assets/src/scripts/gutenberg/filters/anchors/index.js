export default {
    name: 'add-anchor',
    hook: 'blocks.registerBlockType',
    action: props => {
        if (props.name.startsWith('mmmadre/') || props.name.startsWith('mmnino/') || props.name.startsWith('mmn-')) {
            if (props.supports) {
                props.supports = {
                    ...props.supports,
                    anchor: true,
                }
            }

            if (props.attributes) {
                props.attributes = {
                    ...props.attributes,
                    anchor: {
                        type: 'string',
                    }
                }
            }
        }

        return props
    }
}