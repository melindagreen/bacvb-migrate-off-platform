# Example Wordpress Block - Static

This is a sample repository that contains boilerplate and examples for creating a static WordPress block to be integrated into the Madden Madre/Niño framework.

A static block renders its attributes to HTML + markup comments on post save using a React-like save function. Static blocks have a potential performance advantage over dynamic blocks. On the other hand, updates to a static block's render function will not apply to existing content unless the post in question is re-saved, and static blocks will throw errors within the editor if their attributes change after they've been saved.

In general, we recommend static blocks primarily for simpler components that are unlikely to change in the future.

## Helpful Links
* [How to install](https://wiki.maddenmedia.com/Install_MM_WordPress_(Gutenberg)_Block)
* [WordPress block reference guides](https://developer.wordpress.org/block-editor/reference-guides/)
* [WordPress components reference](https://developer.wordpress.org/block-editor/reference-guides/components/)
