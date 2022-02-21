import "./style.scss";
import { createMap } from "$lib/map";
import { createTooltipOverlay } from "$lib/overlays/tooltip";
import { defaultMarkerStyle } from "$lib/styles/defaultMarkerStyle";
import { singleFeature } from "$lib/layers/singlefeature";
import { fromLonLat } from "ol/proj";
import { geojson } from "$lib/layers/geojson";
import type { Map } from "ol";
import { addLoadingOverlay } from "$lib/addLoadingOverlay";

const addTooltipOverlay = (element: HTMLElement, map: Map) => {
  const tooltipElement = document.createElement("div");
  tooltipElement.classList.add("tooltip");
  element.insertAdjacentElement("afterend", tooltipElement);
  createTooltipOverlay(tooltipElement, map);
};

document.addEventListener("DOMContentLoaded", function () {
  const { settings, data } = bwOsmPlugin;
  for (const entry of data) {
    const mapElement = document.getElementById(entry.elementId);
    entry.style = defaultMarkerStyle(settings.assetsUrl);
    entry.center = fromLonLat(entry.center ?? settings.center);
    if (entry.categories || entry.tags || entry.taxonomies) {
      const loadingOverlay = addLoadingOverlay(mapElement);
      entry.layer = geojson(entry.src, entry.style);
      entry.layer
        .getSource()
        .getSource()
        .on("featuresloadend", () => {
          loadingOverlay.classList.add("hidden");
        });
    } else {
      entry.layer = singleFeature(entry);
    }
    entry.layer.setVisible(entry.show_marker);
    const map = createMap(entry, settings.key, settings.map);
    addTooltipOverlay(mapElement, map);
    /* @ts-ignore */
    map.on("clickFeature", (e: CustomEvent<Feature>) => {
      const features = e.detail.get("features");
      if (features?.length === 1) {
        const link = features[0].get("link");
        if (link) {
          window.location.href = link;
        }
      }
    });
  }
});
