export const PluginMessages = {
  PluginSettingsNavigation: 'PLUGIN_SETTINGS_NAVIGATION',
  PluginLeadinConfig: 'PLUGIN_LEADIN_CONFIG',
  TrackConsent: 'INTEGRATED_APP_EMBEDDER_TRACK_CONSENT',
  InternalTrackingFetchRequest: 'INTEGRATED_TRACKING_FETCH_REQUEST',
  InternalTrackingFetchResponse: 'INTEGRATED_TRACKING_FETCH_RESPONSE',
  InternalTrackingFetchError: 'INTEGRATED_TRACKING_FETCH_ERROR',
  InternalTrackingChangeRequest: 'INTEGRATED_TRACKING_CHANGE_REQUEST',
  InternalTrackingChangeError: 'INTEGRATED_TRACKING_CHANGE_ERROR',
  BusinessUnitFetchRequest: 'BUSINESS_UNIT_FETCH_REQUEST',
  BusinessUnitFetchResponse: 'BUSINESS_UNIT_FETCH_FETCH_RESPONSE',
  BusinessUnitFetchError: 'BUSINESS_UNIT_FETCH_FETCH_ERROR',
  BusinessUnitChangeRequest: 'BUSINESS_UNIT_CHANGE_REQUEST',
  BusinessUnitChangeError: 'BUSINESS_UNIT_CHANGE_ERROR',
  SkipReviewRequest: 'SKIP_REVIEW_REQUEST',
  SkipReviewResponse: 'SKIP_REVIEW_RESPONSE',
  SkipReviewError: 'SKIP_REVIEW_ERROR',
} as const;

export type PluginMessageType = typeof PluginMessages[keyof typeof PluginMessages];
