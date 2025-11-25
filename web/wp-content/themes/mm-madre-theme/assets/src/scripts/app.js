import "./library/madden_jquery_lazy-load-v1.3";
import "./library/madden-parallax-layout-v1.3";
import { parseDataCookie, setDataCookie, getIsSmall } from "./inc/utilities";
import "../styles/style.scss";
(function($) {
  $(document).ready(function($) {
    $("*[data-load-type]").lazyLoad();
  });
})(jQuery);
