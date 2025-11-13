# Menu Toggle

A simple hamburger menu toggle to use with other elements on your site.

## Installation

You just need to add hamburgers to your package.json by one of the following commands;

```bash
yarn add hamburgers
npm install hamburgers
```

## Usage

When toggled the block will add a class of .is-active to the target element.

The block also dispatches a custom JavaScript event when the toggle is clicked. Here is an example of how it could be used:

```
const $menu = $("#menu-main-menu");
document.addEventListener("maddenMenuToggleClick", function (e) {
  const { isActive, targetSelector } = e.detail;
  const $target = jQuery(targetSelector);
  if (!$target.length) {
    return false;
  }
  if ("#menu-main-menu" === targetSelector) {
    const mobileMargin = $target.outerHeight();
    if (isActive) {
      $target.stop(true).animate({ marginTop: 0 }, 700, "easeInOutQuint"); // open animation
    } else {
      $target
        .stop(true)
        .animate({ marginTop: `-${mobileMargin}px` }, 700, "easeInOutQuint"); // open animation
    }
  }
});
```

Note: In order to use jQuery animate, you must add 'jquery-effects-core' as a script dependency for your JavaScript.
