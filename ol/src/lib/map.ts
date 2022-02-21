import { Map, View } from "ol";
import { osm } from "./layers/osm";
import { seamap } from "./layers/seamap";
import { DEFAULTS } from "../constants";
import { MapInstance } from "../types";
import { ScaleLine, defaults as defaultControls } from "ol/control";

export const createMap = (
  { elementId, zoom = DEFAULTS.zoom, center, layer }: MapInstance,
  apiKey: string,
  mapStyle: string
) => {
  return new Map({
    controls: defaultControls().extend([new ScaleLine({ units: "nautical" })]),
    target: elementId,
    layers: [osm(apiKey, mapStyle), seamap(), layer],
    view: new View({
      center,
      zoom,
    }),
  });
};
