export const addLoadingOverlay = (element: HTMLElement) => {
  const loadingOverlay = document.createElement("div");
  loadingOverlay.classList.add("bw-map-loading");
  const loader = document.createElement("div");
  loader.classList.add("loader");
  loadingOverlay.append(loader);
  element.append(loadingOverlay);
  return loadingOverlay;
};
