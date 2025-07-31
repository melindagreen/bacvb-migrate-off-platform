import domReady from "@wordpress/dom-ready";
import { initInteractiveMap } from "./assets/bradenton-map";

domReady(() => {
	initInteractiveMap();
});
