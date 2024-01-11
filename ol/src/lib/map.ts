import { Map, View } from "ol";
import { seamap } from "./layers/seamap";
import { DEFAULTS } from "../constants";
import { MapInstance } from "../types";
import { ScaleLine, defaults as defaultControls } from "ol/control";
import apply from "ol-mapbox-style";

export const createMap = (
  { elementId, zoom = DEFAULTS.zoom, center, layer }: MapInstance,
  apiKey: string,
  mapStyle: string
) => {
  const map = new Map({
    controls: defaultControls().extend([new ScaleLine({ units: "nautical" })]),
    target: elementId,
    view: new View({
      center,
      zoom,
    }),
  });
  apply(map,`https://api.maptiler.com/maps/${mapStyle}-v2/style.json?key=${apiKey}`)
    .then(() => {map.addLayer(seamap()); map.addLayer(layer)});
  return map;
};
