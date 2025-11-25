import { initSwiperSliders } from "./assets/slider";
import "./styles/style.scss";

window.addEventListener("DOMContentLoaded", () => {
  const sliderBlockSelector = ".wp-block-kraken-core-slider .swiper";
  const swiperInstances = [];

  let sliders;
  sliders = document.querySelectorAll(sliderBlockSelector);

  sliders.forEach((slider, index) => {
    if (!slider) {
      return;
    }
    //if the slider was already initialized, don't run again.
    if (slider.swiper) {
      return;
    }

    let swiperSlider = initSwiperSliders(slider);
    swiperInstances[index] = swiperSlider;
  });

  //console.log(swiperInstances);
});
