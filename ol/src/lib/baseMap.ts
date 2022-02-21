import { settings } from "./settings";
import { ScaleLine, defaults as defaultControls } from "ol/control";
import { osm } from "./layers/osm";
import { seamap } from "./layers/seamap";
import { Map, View } from "ol";

export const baseMap = (
  target: HTMLElement,
  center: number[],
  zoom: number
) => {
  const { key, map } = settings;
  return new Map({
    target,
    controls: defaultControls().extend([new ScaleLine({ units: "nautical" })]),
    layers: [osm(key, map), seamap()],
    view: new View({
      center,
      zoom,
    }),
  });
};
