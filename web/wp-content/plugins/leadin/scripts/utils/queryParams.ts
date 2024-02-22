export function addQueryObjectToUrl(
  urlObject: URL,
  queryParams: { [key: string]: any }
) {
  Object.keys(queryParams).forEach(key => {
    urlObject.searchParams.append(key, queryParams[key]);
  });
}
