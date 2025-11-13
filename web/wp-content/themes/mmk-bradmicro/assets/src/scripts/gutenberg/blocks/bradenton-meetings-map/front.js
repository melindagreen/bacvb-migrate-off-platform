import domReady from "@wordpress/dom-ready";
import { initInteractiveMap } from "./assets/bradenton-meetings-map";

domReady(() => {
	initInteractiveMap();
});
