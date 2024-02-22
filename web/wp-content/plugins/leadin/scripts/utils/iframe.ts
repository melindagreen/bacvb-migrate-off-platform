import { useEffect, useState } from 'react';
import Raven from '../lib/Raven';

const IFRAME_DISPLAY_TIMEOUT = 5000;

export function useIframeNotRendered(app: string) {
  const [iframeNotRendered, setIframeNotRendered] = useState(false);
  useEffect(() => {
    const timer = setTimeout(() => {
      const iframe = document.getElementById(app);
      if (!iframe) {
        Raven.captureException(new Error(`Leadin Iframe blocked`), {
          fingerprint: ['IFRAME_SETUP_ERROR'],
        });
        setIframeNotRendered(true);
      }
    }, IFRAME_DISPLAY_TIMEOUT);

    return () => {
      if (timer) {
        clearTimeout(timer);
      }
    };
  }, []);

  return iframeNotRendered;
}

export const resizeWindow = () => {
  const adminMenuWrap = document.getElementById('adminmenuwrap');
  const sideMenuHeight = adminMenuWrap ? adminMenuWrap.offsetHeight : 0;
  const adminBar = document.getElementById('wpadminbar');
  const adminBarHeight = (adminBar && adminBar.offsetHeight) || 0;
  const offset = 4;
  if (window.innerHeight < sideMenuHeight) {
    return sideMenuHeight - offset;
  } else {
    return window.innerHeight - adminBarHeight - offset;
  }
};
