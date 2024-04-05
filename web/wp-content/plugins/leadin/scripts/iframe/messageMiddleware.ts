import { MessageType, PluginMessages } from '../iframe/integratedMessages';
import {
  fetchDisableInternalTracking,
  trackConsent,
  disableInternalTracking,
  getBusinessUnitId,
  setBusinessUnitId,
  skipReview,
} from '../api/wordpressApiClient';

export type Message = { key: MessageType; payload?: any };

const messageMapper: Map<MessageType, Function> = new Map([
  [
    PluginMessages.TrackConsent,
    (message: Message) => {
      trackConsent(message.payload);
    },
  ],
  [
    PluginMessages.InternalTrackingChangeRequest,
    (message: Message, embedder: any) => {
      disableInternalTracking(message.payload)
        .then(() => {
          embedder.postMessage({
            key: PluginMessages.InternalTrackingFetchResponse,
            payload: message.payload,
          });
        })
        .catch(payload => {
          embedder.postMessage({
            key: PluginMessages.InternalTrackingChangeError,
            payload,
          });
        });
    },
  ],
  [
    PluginMessages.InternalTrackingFetchRequest,
    (__message: Message, embedder: any) => {
      fetchDisableInternalTracking()
        .then(({ message: payload }) => {
          embedder.postMessage({
            key: PluginMessages.InternalTrackingFetchResponse,
            payload,
          });
        })
        .catch(payload => {
          embedder.postMessage({
            key: PluginMessages.InternalTrackingFetchError,
            payload,
          });
        });
    },
  ],
  [
    PluginMessages.BusinessUnitFetchRequest,
    (__message: Message, embedder: any) => {
      getBusinessUnitId()
        .then(payload => {
          embedder.postMessage({
            key: PluginMessages.BusinessUnitFetchResponse,
            payload,
          });
        })
        .catch(payload => {
          embedder.postMessage({
            key: PluginMessages.BusinessUnitFetchError,
            payload,
          });
        });
    },
  ],
  [
    PluginMessages.BusinessUnitChangeRequest,
    (message: Message, embedder: any) => {
      setBusinessUnitId(message.payload)
        .then(payload => {
          embedder.postMessage({
            key: PluginMessages.BusinessUnitFetchResponse,
            payload,
          });
        })
        .catch(payload => {
          embedder.postMessage({
            key: PluginMessages.BusinessUnitChangeError,
            payload,
          });
        });
    },
  ],
  [
    PluginMessages.SkipReviewRequest,
    (__message: Message, embedder: any) => {
      skipReview()
        .then(payload => {
          embedder.postMessage({
            key: PluginMessages.SkipReviewResponse,
            payload,
          });
        })
        .catch(payload => {
          embedder.postMessage({
            key: PluginMessages.SkipReviewError,
            payload,
          });
        });
    },
  ],
]);

export const messageMiddleware = (embedder: any) => (message: Message) => {
  const next = messageMapper.get(message.key);
  if (next) {
    next(message, embedder);
  }
};
